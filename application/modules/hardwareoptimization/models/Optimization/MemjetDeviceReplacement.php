<?php

/**
 * ****************************************************************************************************************
 * Memjet Device Replacement
 * ****************************************************************************************************************
 * A device's Average Monthly Page Volume must be between the replacement devices minimum and maximum page volume for the replacement to be eligible.
 * If there is more than one device that meets the page requirement, choose the one with the most functionality. If they are the same functionality then the cheapest one will be used?
 * A replacement device must be the same type(Black/Black MFP/Color/Color MFP) or above(black can replace into a black MFP) as the replacement device.
 *
 * Class Hardwareoptimization_Model_Optimization_StandardDeviceReplacement
 */
class Hardwareoptimization_Model_Optimization_MemjetDeviceReplacement implements Hardwareoptimization_Model_Optimization_DeviceReplacementInterface
{
    /**
     * @var Hardwareoptimization_Model_Device_Swap[]
     */
    protected $_blackReplacementDevices = array();

    /**
     * @var Hardwareoptimization_Model_Device_Swap[]
     */
    protected $_blackMfpReplacementDevices = array();

    /**
     * @var Hardwareoptimization_Model_Device_Swap[]
     */
    protected $_colorReplacementDevices = array();

    /**
     * @var Hardwareoptimization_Model_Device_Swap[]
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
    protected $_savingsThreshold;

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
     * @param Hardwareoptimization_Model_Device_Swap[] $replacementDevices
     * @param int                                      $dealerId
     * @param int                                      $costThreshold
     * @param Proposalgen_Model_CostPerPageSetting     $dealerCostPerPageSetting
     * @param Proposalgen_Model_CostPerPageSetting     $replacementsCostPerPageSetting
     * @param Proposalgen_Model_CostPerPage            $reportLaborCostPerPage
     * @param Proposalgen_Model_CostPerPage            $reportPartsCostPerPage
     */
    public function __construct ($replacementDevices, $dealerId, $costThreshold, $dealerCostPerPageSetting, $replacementsCostPerPageSetting, $reportLaborCostPerPage, $reportPartsCostPerPage)
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
        $this->_savingsThreshold                                = $costThreshold;
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
        if ($deviceInstance->getMasterDevice()->tonerConfigId === Proposalgen_Model_TonerConfig::BLACK_ONLY)
        {
            if ($deviceInstance->getMasterDevice()->isCopier)
            {
                $suggestedDevice = $this->_findReplacement($deviceInstance, $this->_blackMfpReplacementDevices);
            }
            else
            {
                $suggestedDevice = $this->_findReplacement($deviceInstance, $this->_blackReplacementDevices);
            }
        }
        else
        {
            if ($deviceInstance->getMasterDevice()->isCopier)
            {
                $suggestedDevice = $this->_findReplacement($deviceInstance, $this->_colorMfpReplacementDevices);
            }
            else
            {
                $suggestedDevice = $this->_findReplacement($deviceInstance, $this->_colorReplacementDevices);
            }
        }

        return $suggestedDevice;
    }

    /**
     * Finds a suitable replacement for a device instance or returns null if no replacement was found
     *
     * @param Proposalgen_Model_DeviceInstance         $deviceInstance
     * @param Hardwareoptimization_Model_Device_Swap[] $replacementDevices
     *
     * @return Proposalgen_Model_MasterDevice
     */
    protected function _findReplacement (Proposalgen_Model_DeviceInstance $deviceInstance, $replacementDevices)
    {
        $suggestedDevice = null;
        $greatestSavings = 0;
        // FIXME triehl: Document $isMfp better or rename variable to better name so that it's purpose is clearer
        $isMfp                     = false;
        $deviceInstanceMonthlyCost = $deviceInstance->calculateMonthlyCost($this->_costPerPageSetting);
        foreach ($replacementDevices as $deviceSwap)
        {
            $deviceReplacementCost = $deviceInstance->calculateMonthlyCost($this->_replacementCostPerPageSetting, Proposalgen_Model_Mapper_MasterDevice::getInstance()->findForReports($deviceSwap->masterDeviceId, $this->_dealerId, $this->_reportLaborCostPerPage, $this->_reportPartsCostPerPage));
            $costDelta             = ($deviceInstanceMonthlyCost - $deviceReplacementCost);

            if ($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() < $deviceSwap->maximumPageCount && $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() > $deviceSwap->minimumPageCount)
            {
                // We have replaced a device based on ampv, but if there is more than one device within the page range, we want to get the cheapest one
                if ($suggestedDevice == null)
                {
                    $suggestedDevice = $deviceSwap->getMasterDevice();
                    $greatestSavings = $costDelta;
                    $isMfp           = $suggestedDevice->isCopier;
                }

                /**
                 * Replacement Conditions
                 * 1: New device is MFP while the last suggested is not OR
                 * 2: They have the same functionality and it is cheaper.
                 */
                else if (($deviceSwap->getMasterDevice()->isCopier && $isMfp == false) || ($costDelta > $greatestSavings && $deviceSwap->getMasterDevice()->isCopier == $isMfp))
                {
                    $suggestedDevice = $deviceSwap->getMasterDevice();
                    $greatestSavings = $costDelta;
                    $isMfp           = $suggestedDevice->isCopier;
                }
            }
        }

        return $suggestedDevice;
    }
}