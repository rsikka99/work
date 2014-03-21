<?php

/**
 * ****************************************************************************************************************
 * Memjet Device Replacement
 * ****************************************************************************************************************
 * A device's Average Monthly Page Volume must be between the replacement devices minimum and maximum page volume for the replacement to be eligible.
 * If there is more than one device that meets the page requirement, choose the one with the most functionality. If they are the same functionality then the cheapest one will be used?
 * A replacement device can upgrade color and MFP but can not downgrade either.
 *
 * Class Memjetoptimization_Model_Optimization_StandardDeviceReplacement
 */
class Memjetoptimization_Model_Optimization_MemjetDeviceReplacement implements Hardwareoptimization_Model_Optimization_DeviceReplacementInterface
{
    /**
     * @var Admin_Model_Memjet_Device_Swap[]
     */
    protected $_replacementDevices = array();

    /**
     * @var Proposalgen_Model_CostPerPageSetting
     */
    protected $_costPerPageSetting;

    /**
     * @var Proposalgen_Model_CostPerPageSetting
     */
    protected $_replacementCostPerPageSetting;

    /**
     * @var float
     */
    protected $_lossThreshold;

    /**
     * @var float
     */
    protected $_costSavingsThreshold;

    /**
     * @var float
     */
    protected $_reportPartsCostPerPage;

    /**
     * @var float
     */
    protected $_reportLaborCostPerPage;

    /**
     * @var int
     */
    protected $_dealerId;

    /**
     * @var int
     */
    protected $_blackToColorRatio;

    /**
     * @param Admin_Model_Memjet_Device_Swap[]     $replacementDevices
     * @param int                                  $dealerId
     * @param float                                $lossThreshold
     * @param float                                $costSavingsThreshold
     * @param Proposalgen_Model_CostPerPageSetting $dealerCppSetting
     * @param Proposalgen_Model_CostPerPageSetting $replacementsCppSetting
     * @param float                                $reportLaborCpp
     * @param float                                $reportPartsCpp
     * @param int                                  $blackToColorRatio
     */
    public function __construct ($replacementDevices, $dealerId, $lossThreshold, $costSavingsThreshold, $dealerCppSetting, $replacementsCppSetting, $reportLaborCpp, $reportPartsCpp, $blackToColorRatio = null)
    {
        if (isset($replacementDevices['black']))
        {
            $this->_replacementDevices = array_merge($replacementDevices['black'], $this->_replacementDevices);
        }

        if (isset($replacementDevices['blackmfp']))
        {
            $this->_replacementDevices = array_merge($replacementDevices['blackmfp'], $this->_replacementDevices);
        }

        if (isset($replacementDevices['color']))
        {
            $this->_replacementDevices = array_merge($replacementDevices['color'], $this->_replacementDevices);
        }

        if (isset($replacementDevices['colormfp']))
        {
            $this->_replacementDevices = array_merge($replacementDevices['colormfp'], $this->_replacementDevices);
        }

        $this->_costPerPageSetting                              = $dealerCppSetting;
        $this->_replacementCostPerPageSetting                   = $replacementsCppSetting;
        $this->_dealerId                                        = $dealerId;
        $this->_costSavingsThreshold                            = $costSavingsThreshold;
        $this->_blackToColorRatio                               = $blackToColorRatio;
        $this->_reportPartsCostPerPage                          = $reportPartsCpp;
        $this->_reportLaborCostPerPage                          = $reportLaborCpp;
        Proposalgen_Model_MasterDevice::$ReportLaborCostPerPage = $reportLaborCpp;
        Proposalgen_Model_MasterDevice::$ReportPartsCostPerPage = $reportPartsCpp;

        /**
         * Convert loss threshold into a negative number for comparison purposes.
         */
        $this->_lossThreshold = -$lossThreshold;
    }

    /**
     * Finds a suitable replacement for a device instance or returns null if no replacement was found
     *
     * 1. Consider replacing anything within the specified page volumes of the potential replacements as long as we are considering the following:<br>
     *      - If we are NOT upgrading functionality then we better be saving a minimum amount of money.<br>
     *      - If we are upgrading functionality we can lose up to X amount of money.<br>
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function findReplacement ($deviceInstance)
    {
        $suggestedDevice = null;

        if ($deviceInstance->getAction($this->_costPerPageSetting) != "Retire")
        {

            $greatestSavings           = 0;
            $deviceInstanceMonthlyCost = $deviceInstance->calculateMonthlyCost($this->_costPerPageSetting);
            $masterDeviceMapper        = Proposalgen_Model_Mapper_MasterDevice::getInstance();

            /**
             * Check each potential device swap
             */
            foreach ($this->_replacementDevices as $deviceSwap)
            {
                /*
                 * We've ordered the device swaps specifically so that we can break out early
                 */
                if ($suggestedDevice instanceof Admin_Model_Memjet_Device_Swap && $suggestedDevice->getReplacementCategory() !== $deviceSwap->getReplacementCategory())
                {
                    break;
                }
                // Make sure we do not lose a3 compatibility
                if (!($deviceInstance->getMasterDevice()->isA3 && !$deviceSwap->getMasterDevice()->isA3))
                {
                    $replacementDevice     = $masterDeviceMapper->findForReports($deviceSwap->masterDeviceId, $this->_dealerId, $this->_reportLaborCostPerPage, $this->_reportPartsCostPerPage);
                    $blackToColorRatio     = ($replacementDevice->isColor() && $deviceInstance->getMasterDevice()->isColor() == false) ? $this->_blackToColorRatio : 0;
                    $deviceReplacementCost = $deviceInstance->calculateMonthlyCost($this->_replacementCostPerPageSetting, $replacementDevice, $blackToColorRatio);

                    $costDelta = ($deviceInstanceMonthlyCost - $deviceReplacementCost);

                    /*
                     * 1. Are we inside the page volume and above the lossThreshold?
                     * 2. If we are a Color MFP and cost savings is less than the threshold, don't swap.
                     */
                    if
                    (
                        $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() <= $deviceSwap->getDealerMaximumPageCount($this->_dealerId) &&
                        $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() >= $deviceSwap->getDealerMinimumPageCount($this->_dealerId) &&
                        $costDelta > $this->_lossThreshold &&
                        !($deviceInstance->getMasterDevice()->isColor() && $deviceInstance->getMasterDevice()->isMfp() && $costDelta < $this->_costSavingsThreshold)
                    )
                    {
                        /*
                         * We fit inside the ampv, but we do not want to downgrade color or functionality
                         */
                        if
                        (
                            $suggestedDevice == null &&
                            (
                                !($deviceInstance->getMasterDevice()->isColor() === true && $deviceSwap->getMasterDevice()->isColor() === false) && // Not losing color
                                !($deviceInstance->getMasterDevice()->isMfp() === true && $deviceSwap->getMasterDevice()->isMfp() === false) // Not losing mfp

                            )
                        )
                        {
                            $suggestedDevice = $deviceSwap;
                            $greatestSavings = $costDelta;
                        }
                        else if
                        (
                            /*
                             * Here we take 4 steps to compare the previous upgrade
                             */
                            $suggestedDevice instanceof Admin_Model_Memjet_Device_Swap &&
                            (
                                /*
                                 *  1. Upgrade color, no functionality change(Mono -> Color, Mono MFP -> Color MFP)
                                 */
                                (
                                    $deviceSwap->getMasterDevice()->isColor()
                                    && $suggestedDevice->getMasterDevice()->isColor() == false
                                    && $suggestedDevice->getMasterDevice()->isMfp() == $deviceSwap->getMasterDevice()->isMfp()
                                ) ||

                                /*
                                 *  2. Functionality change, no color changes (Mono -> Mono MFP, Color -> Color MFP)
                                 */
                                (
                                    $deviceSwap->getMasterDevice()->isMfp()
                                    && $suggestedDevice->getMasterDevice()->isMfp() == false
                                    && $deviceSwap->getMasterDevice()->isColor() == $suggestedDevice->getMasterDevice()->isColor()
                                ) ||

                                /*
                                 *  3. Functionality and color upgrade (Mono -> Color MFP)
                                 */
                                (
                                    $deviceSwap->getMasterDevice()->isColor() == false
                                    && $deviceSwap->getMasterDevice()->isMfp() == false
                                    && $suggestedDevice->getMasterDevice()->isColor() && $suggestedDevice->getMasterDevice()->isMfp()
                                ) ||

                                /*
                                 *  4. No functionality upgrade, no color upgrade, just cheaper.
                                 */
                                (
                                    $costDelta > $greatestSavings
                                    && $deviceSwap->getMasterDevice()->isMfp() == $suggestedDevice->getMasterDevice()->isMfp()
                                    && $deviceSwap->getMasterDevice()->isColor() == $suggestedDevice->getMasterDevice()->isColor()
                                )
                            )
                        )
                        {
                            $suggestedDevice = $deviceSwap->getMasterDevice();
                            $greatestSavings = $costDelta;
                        }
                    }
                }
            }

            /**
             * Convert into a master device
             */
            if ($suggestedDevice instanceof Admin_Model_Memjet_Device_Swap)
            {
                $suggestedDevice = $suggestedDevice->getMasterDevice();
            }
        }

        return $suggestedDevice;
    }
}