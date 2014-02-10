<?php

/**
 * ****************************************************************************************************************
 * Standard Device Replacement
 * ****************************************************************************************************************
 * Replacement devices must be cheaper than the device and have a cost delta > the cost threshold
 * A device's Average Monthly Page Volume must be between the replacement devices minimum and maximum page volume for the replacement to be eligible.
 * A replacement device must be the same type(Black/Black MFP/Color/Color MFP) or above(black can replace into a black MFP) as the replacement device.
 * Cost delta is calculated by subtracting the devices monthly cost by the replacements monthly cost.
 *
 * Class Hardwareoptimization_Model_Optimization_StandardDeviceReplacement
 */
class Hardwareoptimization_Model_Optimization_StandardDeviceReplacement implements Hardwareoptimization_Model_Optimization_DeviceReplacementInterface
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
     * @param                                          $dealerId
     * @param                                          $costThreshold
     * @param                                          $dealerCostPerPageSetting
     * @param                                          $replacementsCostPerPageSetting
     * @param                                          $reportLaborCostPerPage
     * @param                                          $reportPartsCostPerPage
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
            if ($deviceInstance->getMasterDevice()->isMfp())
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
            if ($deviceInstance->getMasterDevice()->isMfp())
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
        $suggestedDevice           = null;
        $greatestSavings           = 0;
        $deviceInstanceMonthlyCost = $deviceInstance->calculateMonthlyCost($this->_costPerPageSetting);
        foreach ($replacementDevices as $deviceSwap)
        {
            // Make sure we do not lose a3 compatibility
            if (!($deviceInstance->getMasterDevice()->isA3 && !$deviceSwap->getMasterDevice()->isA3))
            {
                $deviceReplacementCost = $deviceInstance->calculateMonthlyCost($this->_replacementCostPerPageSetting, Proposalgen_Model_Mapper_MasterDevice::getInstance()->findForReports($deviceSwap->masterDeviceId, $this->_dealerId, $this->_reportLaborCostPerPage, $this->_reportPartsCostPerPage));
                $costDelta             = ($deviceInstanceMonthlyCost - $deviceReplacementCost);
                if ($costDelta > $this->_savingsThreshold && $costDelta > $greatestSavings)
                {
                    // We replaced the device on cost at this point, we need to look at AMPV
                    if ($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() < $deviceSwap->maximumPageCount && $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() > $deviceSwap->minimumPageCount)
                    {
                        $suggestedDevice = $deviceSwap->getMasterDevice();
                        $greatestSavings = $costDelta;
                    }
                }
            }
        }

        return $suggestedDevice;
    }
}