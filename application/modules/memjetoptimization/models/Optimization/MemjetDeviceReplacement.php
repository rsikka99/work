<?php

/**
 * ****************************************************************************************************************
 * Memjet Device Replacement
 * ****************************************************************************************************************
 * A device's Average Monthly Page Volume must be between the replacement devices minimum and maximum page volume for the replacement to be eligible.
 * If there is more than one device that meets the page requirement, choose the one with the most functionality. If they are the same functionality then the cheapest one will be used?
 * A replacement device can upgrade color and mfp but can not downgrade either.
 *
 * Class Memjetoptimization_Model_Optimization_StandardDeviceReplacement
 */
class Memjetoptimization_Model_Optimization_MemjetDeviceReplacement implements Memjetoptimization_Model_Optimization_DeviceReplacementInterface
{
    /**
     * @var Admin_Model_Memjet_Device_Swap[]
     */
    protected $_blackReplacementDevices = array();

    /**
     * @var Admin_Model_Memjet_Device_Swap[]
     */
    protected $_blackMfpReplacementDevices = array();

    /**
     * @var Admin_Model_Memjet_Device_Swap[]
     */
    protected $_colorReplacementDevices = array();

    /**
     * @var Admin_Model_Memjet_Device_Swap[]
     */
    protected $_colorMfpReplacementDevices = array();

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
     * @param int                                  $lossThreshold
     * @param Proposalgen_Model_CostPerPageSetting $dealerCostPerPageSetting
     * @param Proposalgen_Model_CostPerPageSetting $replacementsCostPerPageSetting
     * @param Proposalgen_Model_CostPerPage        $reportLaborCostPerPage
     * @param Proposalgen_Model_CostPerPage        $reportPartsCostPerPage
     * @param int                                  $blackToColorRatio
     */
    public function __construct ($replacementDevices, $dealerId, $lossThreshold, $dealerCostPerPageSetting, $replacementsCostPerPageSetting, $reportLaborCostPerPage, $reportPartsCostPerPage, $blackToColorRatio = null)
    {
        if (isset($replacementDevices['black']))
        {
            $this->_blackReplacementDevices = $replacementDevices['black'];
        }

        if (isset($replacementDevices['blackmfp']))
        {
            $this->_blackMfpReplacementDevices = $replacementDevices['blackmfp'];
        }

        if (isset($replacementDevices['color']))
        {
            $this->_colorReplacementDevices = $replacementDevices['color'];
        }

        if (isset($replacementDevices['colormfp']))
        {
            $this->_colorMfpReplacementDevices = $replacementDevices['colormfp'];
        }

        $this->_costPerPageSetting                              = $dealerCostPerPageSetting;
        $this->_replacementCostPerPageSetting                   = $replacementsCostPerPageSetting;
        $this->_dealerId                                        = $dealerId;
        $this->_lossThreshold                                   = $lossThreshold;
        $this->_blackToColorRatio                               = $blackToColorRatio;
        $this->_reportPartsCostPerPage                          = $reportPartsCostPerPage;
        $this->_reportLaborCostPerPage                          = $reportLaborCostPerPage;
        Proposalgen_Model_MasterDevice::$ReportLaborCostPerPage = $reportLaborCostPerPage;
        Proposalgen_Model_MasterDevice::$ReportPartsCostPerPage = $reportPartsCostPerPage;
    }

    /**
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     *
     * @return \Proposalgen_Model_MasterDevice
     */
    public function findReplacement ($deviceInstance)
    {
        if ($deviceInstance->getAction() != "Retire")
        {
            return $this->_findReplacement($deviceInstance, array_merge($this->_blackReplacementDevices, $this->_blackMfpReplacementDevices, $this->_colorReplacementDevices, $this->_colorMfpReplacementDevices));
        }

        return null;
    }

    /**
     * Finds a suitable replacement for a device instance or returns null if no replacement was found
     *
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     * @param Admin_Model_Memjet_Device_Swap[] $replacementDevices
     *
     * @return Proposalgen_Model_MasterDevice
     */
    protected function _findReplacement (Proposalgen_Model_DeviceInstance $deviceInstance, $replacementDevices)
    {
        $suggestedDevice           = null;
        $greatestSavings           = 0;
        $deviceInstanceMonthlyCost = $deviceInstance->calculateMonthlyCost($this->_costPerPageSetting);
        foreach ($replacementDevices as $deviceSwap)
        {
            $replacementDevice     = Proposalgen_Model_Mapper_MasterDevice::getInstance()->findForReports($deviceSwap->masterDeviceId, $this->_dealerId, $this->_reportLaborCostPerPage, $this->_reportPartsCostPerPage);
            $deviceReplacementCost = $deviceInstance->calculateMonthlyCost($this->_replacementCostPerPageSetting, $replacementDevice, ($replacementDevice->isColor() && $deviceInstance->getMasterDevice()->isColor() == false) ? $this->_blackToColorRatio : null);

            $costDelta = ($deviceInstanceMonthlyCost - $deviceReplacementCost);
            // Are we inside the page volume and above the lossThreshold?
            // If we are a color mfp and the replacement loses money, don't swap
            if
            (
                $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() <= $deviceSwap->getDealerMaximumPageCount($this->_dealerId) &&
                $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() >= $deviceSwap->getDealerMinimumPageCount($this->_dealerId) &&
                $costDelta > -$this->_lossThreshold &&
                !($deviceInstance->getMasterDevice()->isColor() && $deviceInstance->getMasterDevice()->isMfp() && $costDelta < 0)
            )
            {
                // We fit inside the ampv, but we do not want to downgrade color or functionality
                if
                (
                    $suggestedDevice == null &&
                    !($deviceInstance->getMasterDevice()->isColor() && $deviceSwap->getMasterDevice()->isColor() == false) && // we are not downgrading color
                    !($deviceInstance->getMasterDevice()->isMfp() && $deviceSwap->getMasterDevice()->isMfp() == false) // we are not downgrading functionality
                )
                {
                    $suggestedDevice = $deviceSwap->getMasterDevice();
                    $greatestSavings = $costDelta;
                }
                else if
                (
                    $suggestedDevice instanceof Proposalgen_Model_MasterDevice &&
                    (
                        //Upgrade color, no functionality change(monochrome->color,monomfp->colormfp)
                        ($deviceSwap->getMasterDevice()->isColor() && $suggestedDevice->isColor() == false && $suggestedDevice->isMfp() == $deviceSwap->getMasterDevice()->isMfp()) ||
                        // functionality change, no color changes (mono->monomfp, color->colormfp)
                        ($deviceSwap->getMasterDevice()->isMfp() && $suggestedDevice->isMfp() == false && $deviceSwap->getMasterDevice()->isColor() == $suggestedDevice->isColor()) ||
                        // functionality and color upgrade(mono->colormfp)
                        ($deviceSwap->getMasterDevice()->isColor() == false && $deviceSwap->getMasterDevice()->isMfp() == false && $suggestedDevice->isColor() && $suggestedDevice->isMfp()) ||
                        // no functionality upgrade, no color upgrade, just cheaper
                        ($costDelta > $greatestSavings && $deviceSwap->getMasterDevice()->isMfp() == $suggestedDevice->isMfp() && $deviceSwap->getMasterDevice()->isColor() == $suggestedDevice->isColor())
                    )
                )
                {
                    $suggestedDevice = $deviceSwap->getMasterDevice();
                    $greatestSavings = $costDelta;
                }
            }
        }

        return $suggestedDevice;
    }
}