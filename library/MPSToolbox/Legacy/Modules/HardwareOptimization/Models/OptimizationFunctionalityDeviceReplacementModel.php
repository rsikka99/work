<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;

/**
 * ****************************************************************************************************************
 * Functionality Device Replacement
 * ****************************************************************************************************************
 * A device's Average Monthly Page Volume must be between the replacement devices minimum and maximum page volume for the replacement to be eligible.
 * If there is more than one device that meets the page requirement, choose the one with the most functionality. If they are the same functionality then the cheapest one will be used?
 * A replacement device can upgrade color and MFP but can not downgrade either.
 *
 * Class OptimizationFunctionalityDeviceReplacementModel
 */
class OptimizationFunctionalityDeviceReplacementModel implements OptimizationDeviceReplacementInterface
{
    /**
     * @var DeviceSwapModel[]
     */
    protected $_blackReplacementDevices = array();

    /**
     * @var DeviceSwapModel[]
     */
    protected $_blackMfpReplacementDevices = array();

    /**
     * @var DeviceSwapModel[]
     */
    protected $_colorReplacementDevices = array();

    /**
     * @var DeviceSwapModel[]
     */
    protected $_colorMfpReplacementDevices = array();

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
     * @param DeviceSwapModel[][]     $replacementDevices
     * @param int                     $dealerId
     * @param float                   $lossThreshold
     * @param float                   $costSavingsThreshold
     * @param CostPerPageSettingModel $dealerCppSetting
     * @param CostPerPageSettingModel $replacementsCppSetting
     * @param float                   $reportLaborCpp
     * @param float                   $reportPartsCpp
     * @param int                     $blackToColorRatio
     */
    public function __construct ($replacementDevices, $dealerId, $lossThreshold, $costSavingsThreshold, $dealerCppSetting, $replacementsCppSetting, $reportLaborCpp, $reportPartsCpp, $blackToColorRatio = null)
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

        $this->_costPerPageSetting                 = $dealerCppSetting;
        $this->_replacementCostPerPageSetting      = $replacementsCppSetting;
        $this->_dealerId                           = $dealerId;
        $this->_costSavingsThreshold               = $costSavingsThreshold;
        $this->_blackToColorRatio                  = $blackToColorRatio;
        $this->_reportPartsCostPerPage             = $reportPartsCpp;
        $this->_reportLaborCostPerPage             = $reportLaborCpp;
        MasterDeviceModel::$ReportLaborCostPerPage = $reportLaborCpp;
        MasterDeviceModel::$ReportPartsCostPerPage = $reportPartsCpp;

        /**
         * Convert loss threshold into a negative number for comparison purposes.
         */
        $this->_lossThreshold = -$lossThreshold;
    }

    /**
     * @param DeviceInstanceModel $deviceInstance
     *
     * @return MasterDeviceModel
     */
    public function findReplacement ($deviceInstance)
    {
        $suggestedDevice = null;

        /**
         * We don't try to upgrade to color if the action is retire or do not repair.
         */
        if ($deviceInstance->getAction($this->_costPerPageSetting) != DeviceInstanceModel::ACTION_RETIRE &&
            $deviceInstance->getAction($this->_costPerPageSetting) != DeviceInstanceModel::ACTION_REPLACE
        )
        {
            if (!$deviceInstance->getMasterDevice()->isColor())
            {
                if ($deviceInstance->getMasterDevice()->isMfp())
                {
                    // Replace with color mfp devices
                    $suggestedDevice = $this->_findReplacement($deviceInstance, $this->_colorMfpReplacementDevices);
                }
                else
                {
                    // Replace with color devices or color mfp devices
                    $suggestedDevice = $this->_findReplacement($deviceInstance, $this->_colorReplacementDevices);
                }
            }
        }

        return $suggestedDevice;
    }

    /**
     * Finds a suitable replacement for a device instance or returns null if no replacement was found
     *
     * 1. Consider replacing anything within the specified page volumes of the potential replacements as long as we are considering the following:<br>
     *      - If we are NOT upgrading functionality then we better be saving a minimum amount of money.<br>
     *      - If we are upgrading functionality we can lose up to X amount of money.<br>
     *
     * @param DeviceInstanceModel $deviceInstance
     * @param DeviceSwapModel[]   $replacementDevices
     *
     * @return MasterDeviceModel
     */
    protected function _findReplacement ($deviceInstance, $replacementDevices)
    {
        $suggestedDeviceSwap = null;

        $greatestSavings           = 0;
        $deviceInstanceMonthlyCost = $deviceInstance->calculateMonthlyCost($this->_costPerPageSetting);
        $masterDeviceMapper        = MasterDeviceMapper::getInstance();

        /**
         * Check each potential device swap
         */
        foreach ($replacementDevices as $deviceSwap)
        {
            /**
             * Mono to Color means we need to convert some pages
             */
            $blackToColorRatio = ($deviceSwap->getMasterDevice()->isColor() && !$deviceInstance->getMasterDevice()->isColor()) ? $this->_blackToColorRatio : 0;

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
            if ($deviceInstance->getPageCounts($blackToColorRatio)->getCombinedPageCount()->getMonthly() < $deviceSwap->minimumPageCount)
            {
                continue;
            }
            else if ($deviceInstance->getPageCounts($blackToColorRatio)->getCombinedPageCount()->getMonthly() > $deviceSwap->maximumPageCount)
            {
                continue;
            }

            /**
             * Ensure the replacement does not cost too much
             */
            $replacementDevice     = $masterDeviceMapper->findForReports($deviceSwap->masterDeviceId, $this->_dealerId, $this->_reportLaborCostPerPage, $this->_reportPartsCostPerPage);
            $deviceReplacementCost = $deviceInstance->calculateMonthlyCost($this->_replacementCostPerPageSetting, $replacementDevice, $blackToColorRatio);
            $costDelta             = ($deviceInstanceMonthlyCost - $deviceReplacementCost);

            if ($costDelta <= $this->_lossThreshold)
            {
                continue;
            }

            /**
             * If we have a suggested device we want to ensure that we're picking the better device.
             */
            if ($suggestedDeviceSwap instanceof DeviceSwapModel)
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

                if ($costDelta < $greatestSavings)
                {
                    continue;
                }
                else if ($costDelta == $greatestSavings)
                {
                    /**
                     * If we have the same cost we should probably use the smaller machine (as it will probably be a bit cheaper)
                     */
                    if ($deviceSwap->maximumPageCount < $suggestedDeviceSwap->maximumPageCount)
                    {
                        continue;
                    }
                }
            }

            /**
             * If we've made it all the way here we can set the suggested device.
             */
            $suggestedDeviceSwap = $deviceSwap;
            $greatestSavings     = $costDelta;
        }

        return ($suggestedDeviceSwap instanceof DeviceSwapModel) ? $suggestedDeviceSwap->getMasterDevice() : null;
    }
}