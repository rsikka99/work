<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;

/**
 * Class OptimizationStandardDeviceReplacementModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 *
 * ****************************************************************************************************************
 * Standard Device Replacement
 * ****************************************************************************************************************
 * Replacement devices must be cheaper than the device and have a cost delta > the cost threshold
 * A device's Average Monthly Page Volume must be between the replacement devices minimum and maximum page volume for the replacement to be eligible.
 * A replacement device must be the same type(Black/Black MFP/Color/Color MFP) or above(black can replace into a black MFP) as the replacement device.
 * Cost delta is calculated by subtracting the devices monthly cost by the replacements monthly cost.
 */
class OptimizationStandardDeviceReplacementModel implements OptimizationDeviceReplacementInterface
{
    /**
     * @var DeviceSwapModel[]
     */
    protected $_blackReplacementDevices = [];

    /**
     * @var DeviceSwapModel[]
     */
    protected $_blackMfpReplacementDevices = [];

    /**
     * @var DeviceSwapModel[]
     */
    protected $_colorReplacementDevices = [];

    /**
     * @var DeviceSwapModel[]
     */
    protected $_colorMfpReplacementDevices = [];

    /**
     * @var CostPerPageSettingModel
     */
    protected $_costPerPageSetting;

    /**
     * @var CostPerPageSettingModel
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
     * @param DeviceSwapModel[]                        $replacementDevices
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

        $this->_costPerPageSetting                 = $dealerCostPerPageSetting;
        $this->_replacementCostPerPageSetting      = $replacementsCostPerPageSetting;
        $this->_dealerId                           = $dealerId;
        $this->_savingsThreshold                   = $costThreshold;
        $this->_reportPartsCostPerPage             = $reportPartsCostPerPage;
        $this->_reportLaborCostPerPage             = $reportLaborCostPerPage;
        MasterDeviceModel::$ReportLaborCostPerPage = $reportLaborCostPerPage;
        MasterDeviceModel::$ReportPartsCostPerPage = $reportPartsCostPerPage;
    }

    /**
     * @param DeviceSwapModel[] $blackReplacementDevices
     */
    public function setBlackReplacementDevices($blackReplacementDevices)
    {
        $this->_blackReplacementDevices = $blackReplacementDevices;
    }

    /**
     * @param DeviceSwapModel[] $blackMfpReplacementDevices
     */
    public function setBlackMfpReplacementDevices($blackMfpReplacementDevices)
    {
        $this->_blackMfpReplacementDevices = $blackMfpReplacementDevices;
    }

    /**
     * @param DeviceSwapModel[] $colorReplacementDevices
     */
    public function setColorReplacementDevices($colorReplacementDevices)
    {
        $this->_colorReplacementDevices = $colorReplacementDevices;
    }

    /**
     * @param DeviceSwapModel[] $colorMfpReplacementDevices
     */
    public function setColorMfpReplacementDevices($colorMfpReplacementDevices)
    {
        $this->_colorMfpReplacementDevices = $colorMfpReplacementDevices;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     */
    public function setCostPerPageSetting($costPerPageSetting)
    {
        $this->_costPerPageSetting = $costPerPageSetting;
    }

    /**
     * @param CostPerPageSettingModel $replacementCostPerPageSetting
     */
    public function setReplacementCostPerPageSetting($replacementCostPerPageSetting)
    {
        $this->_replacementCostPerPageSetting = $replacementCostPerPageSetting;
    }

    /**
     * @param float $savingsThreshold
     */
    public function setSavingsThreshold($savingsThreshold)
    {
        $this->_savingsThreshold = $savingsThreshold;
    }

    /**
     * @param float $reportPartsCostPerPage
     */
    public function setReportPartsCostPerPage($reportPartsCostPerPage)
    {
        $this->_reportPartsCostPerPage = $reportPartsCostPerPage;
    }

    /**
     * @param float $reportLaborCostPerPage
     */
    public function setReportLaborCostPerPage($reportLaborCostPerPage)
    {
        $this->_reportLaborCostPerPage = $reportLaborCostPerPage;
    }

    /**
     * @param int $dealerId
     */
    public function setDealerId($dealerId)
    {
        $this->_dealerId = $dealerId;
    }

    /**
     * @param DeviceInstanceModel $deviceInstance
     *
     * @return MasterDeviceModel
     */
    public function findReplacement ($deviceInstance)
    {
        $suggestedDevice = null;

        if ($deviceInstance->getAction($this->_costPerPageSetting) != DeviceInstanceModel::ACTION_RETIRE)
        {
            if ($deviceInstance->getMasterDevice()->isColor())
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
            else
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
        }

        return $suggestedDevice;
    }

    /**
     * Finds a suitable replacement for a device instance or returns null if no replacement was found
     *
     * @param DeviceInstanceModel $deviceInstance
     * @param DeviceSwapModel[]   $replacementDevices
     *
     * @return MasterDeviceModel
     */
    protected function _findReplacement (DeviceInstanceModel $deviceInstance, $replacementDevices)
    {
        $suggestedDeviceSwap       = null;
        $greatestSavings           = 0;
        $deviceInstanceMonthlyCost = $deviceInstance->calculateMonthlyCost($this->_costPerPageSetting);
        $masterDeviceMapper        = MasterDeviceMapper::getInstance();

        /**
         * May the best device win
         */
        foreach ($replacementDevices as $deviceSwap)
        {
            /**
             * Cannot swap with same device
             */
            if ($deviceInstance->getMasterDevice()->id == $deviceSwap->masterDeviceId)
            {
                continue;
            }

            /**
             * Preserve Color
             */
            if ($deviceInstance->getMasterDevice()->isColor() && !$deviceSwap->getMasterDevice()->isColor())
            {
                continue;
            }

            /**
             * Preserve MFP
             */
            if ($deviceInstance->getMasterDevice()->isMfp() && !$deviceSwap->getMasterDevice()->isMfp())
            {
                continue;
            }

            /**
             * Preserve A3
             */
            if ($deviceInstance->getMasterDevice()->isA3 && !$deviceSwap->getMasterDevice()->isA3)
            {
                continue;
            }

            /**
             * Ensure we are within the page counts
             */
            if ($deviceInstance->getCombinedMonthlyPageCount() < $deviceSwap->minimumPageCount)
            {
                continue;
            }
            else if ($deviceInstance->getCombinedMonthlyPageCount() > $deviceSwap->maximumPageCount)
            {
                continue;
            }

            /**
             * Ensure the replacement does not cost too much
             */
            $replacementDevice     = $masterDeviceMapper->findForReports($deviceSwap->masterDeviceId, $this->_dealerId);
            $deviceReplacementCost = $deviceInstance->calculateMonthlyCost($this->_replacementCostPerPageSetting, $replacementDevice);
            $costDelta             = ($deviceInstanceMonthlyCost - $deviceReplacementCost);

            if ($costDelta <= $this->_savingsThreshold)
            {
                continue;
            }

            /**
             * If we have a suggested device and it costs the exact same then we want to ensure that we're picking the better device.
             */
            if ($suggestedDeviceSwap instanceof DeviceSwapModel && $costDelta == $greatestSavings)
            {
                /**
                 * Ensure we are better than the last upgrade
                 */
                if (!$deviceSwap->getMasterDevice()->isMfp() && $suggestedDeviceSwap->getMasterDevice()->isMfp())
                {
                    continue;
                }

                if (!$deviceSwap->getMasterDevice()->isColor() && $suggestedDeviceSwap->getMasterDevice()->isColor())
                {
                    continue;
                }

                /**
                 * If we have the same cost we should probably use the smaller machine (as it will probably be a bit cheaper)
                 */
                if ($deviceSwap->maximumPageCount < $suggestedDeviceSwap->maximumPageCount)
                {
                    continue;
                }
            }

            /**
             * If we've made it all the way here we can set the suggested device.
             */
            $suggestedDeviceSwap = $deviceSwap->getMasterDevice();
            $greatestSavings     = $costDelta;
        }

        return $suggestedDeviceSwap;
    }
}