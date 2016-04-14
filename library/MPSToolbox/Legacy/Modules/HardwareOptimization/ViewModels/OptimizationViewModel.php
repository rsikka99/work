<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\ViewModels;

use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\OptimizationDealerModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\OptimizationCustomerModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationDeviceInstanceModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\PageCountsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsExcludedRowModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use CpChart\Services\pChartFactory;
use stdClass;
use My_Brand;

/**
 * Class OptimizationViewModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\ViewModels
 */
class OptimizationViewModel
{

    const AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE = 200;
    const AVERAGE_MONTHLY_PAGES_PER_DEVICE   = 1500;
    const AVERAGE_EMPLOYEES_PER_DEVICE = 4.4;

    /**
     * @var HardwareOptimizationModel
     */
    protected $_optimization;

    /**
     * @var float
     */
    protected $_cashHeldInInventory;

    /**
     * @var MasterDeviceModel[]
     */
    protected $_uniqueDeviceList;

    /**
     * @var MasterDeviceModel[]
     */
    protected $_uniquePurchasedDeviceList;

    /**
     * @var TonerModel[]
     */
    protected $_uniquePurchasedTonerList;

    /**
     * @var int
     */
    protected $_numberOfUniqueModels;

    /**
     * @var int
     */
    protected $_numberOfColorCapableDevices;

    /**
     * @var int
     */
    protected $_numberOfUniquePurchasedModels;

    /**
     * @var int
     */
    protected $_numberOfUniquePurchasedToners;

    /**
     * @var int
     */
    protected $_maximumMonthlyPrintVolume;

    /**
     * @var  PageCountsModel
     */
    protected $_pageCounts;

    /**
     * @var  PageCountsModel
     */
    protected $_newPageCounts;

    /**
     * @var DevicesViewModel
     */
    protected $_devices;

    /**
     * The weighted average monthly cost per page when using replacements
     *
     * @var CostPerPageModel
     */
    protected $_dealerWeightedAverageMonthlyCostPerPageWithReplacements;

    /**
     * The weighted average monthly cost per page
     *
     * @var CostPerPageModel
     */
    protected $_dealerWeightedAverageMonthlyCostPerPage;

    /**
     * The dealers monthly cost with replacements
     *
     * @var number
     */
    protected $_dealerMonthlyCostWithReplacements;

    /**
     * Cost per page setting for a dealer
     *
     * @var CostPerPageSettingModel
     */
    protected $_costPerPageSettingForDealer;

    /**
     * Cost per page setting for replacement devices
     *
     * @var CostPerPageSettingModel
     */
    protected $_costPerPageSettingForReplacements;

    /**
     * @var DeviceInstanceModel[]
     */
    protected $_devicesGroupedByAction;

    /**
     * Constructor
     *
     * @param int|HardwareOptimizationModel $hardwareOptimization
     */
    public function __construct ($hardwareOptimization)
    {
        if ($hardwareOptimization instanceof HardwareOptimizationModel)
        {
            $this->_optimization = $hardwareOptimization;
        }
        else
        {
            $this->_optimization = HardwareOptimizationMapper::getInstance()->find($hardwareOptimization);
        }
    }

    /**
     * Gets the devices object for the report.
     *
     * @return DevicesViewModel
     */
    public function getDevices ()
    {
        if (!isset($this->_devices))
        {
            $clientSettings = $this->_optimization->getClient()->getClientSettings();
            $this->_devices = new DevicesViewModel(
                $this->_optimization->rmsUploadId,
                $clientSettings->currentFleetSettings->defaultMonochromeLaborCostPerPage,
                $clientSettings->currentFleetSettings->defaultMonochromePartsCostPerPage,
                $clientSettings->currentFleetSettings->adminCostPerPage
            );
        }

        return $this->_devices;
    }

    /**
     * Gets the amount of color capable devices with replacement devices
     *
     * @return int
     */
    public function getNumberOfDevicesWithReplacements ()
    {
        $numberOfDevices = 0;
        foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
        {
            if ($device->getReplacementMasterDeviceForHardwareOptimization($this->_optimization->id) instanceof MasterDeviceModel)
            {
                $numberOfDevices++;
            }
        }

        return $numberOfDevices;
    }

    /**
     * Gets the cost per page settings for the dealers point of view
     *
     * @return CostPerPageSettingModel
     */
    public function getCostPerPageSettingForDealer ()
    {
        if (!isset($this->_costPerPageSettingForDealer))
        {
            $this->_costPerPageSettingForDealer = new CostPerPageSettingModel();

            $clientSettings                                                 = $this->_optimization->getClient()->getClientSettings();
            $this->_costPerPageSettingForDealer->adminCostPerPage           = $clientSettings->proposedFleetSettings->adminCostPerPage;
            $this->_costPerPageSettingForDealer->pageCoverageMonochrome     = $clientSettings->proposedFleetSettings->defaultMonochromeCoverage;
            $this->_costPerPageSettingForDealer->pageCoverageColor          = $clientSettings->proposedFleetSettings->defaultColorCoverage;
            $this->_costPerPageSettingForDealer->monochromeLaborCostPerPage = $clientSettings->proposedFleetSettings->defaultMonochromeLaborCostPerPage;
            $this->_costPerPageSettingForDealer->monochromePartsCostPerPage = $clientSettings->proposedFleetSettings->defaultMonochromePartsCostPerPage;
            $this->_costPerPageSettingForDealer->colorLaborCostPerPage      = $clientSettings->proposedFleetSettings->defaultColorLaborCostPerPage;
            $this->_costPerPageSettingForDealer->colorPartsCostPerPage      = $clientSettings->proposedFleetSettings->defaultColorPartsCostPerPage;

            $this->_costPerPageSettingForDealer->monochromeTonerRankSet = $clientSettings->proposedFleetSettings->getMonochromeRankSet();
            $this->_costPerPageSettingForDealer->colorTonerRankSet      = $clientSettings->proposedFleetSettings->getColorRankSet();
            $this->_costPerPageSettingForDealer->useDevicePageCoverages = $clientSettings->proposedFleetSettings->useDevicePageCoverages;
            $this->_costPerPageSettingForDealer->level                  = $clientSettings->proposedFleetSettings->level;
        }

        return $this->_costPerPageSettingForDealer;
    }

    /**
     * Gets the cost per page settings for the replacement devices
     *
     * @return CostPerPageSettingModel
     */
    public function getCostPerPageSettingForReplacements ()
    {
        if (!isset($this->_costPerPageSettingForReplacements))
        {
            $this->_costPerPageSettingForReplacements = new CostPerPageSettingModel();

            $clientSettings = $this->_optimization->getClient()->getClientSettings();

            $this->_costPerPageSettingForReplacements->adminCostPerPage           = $clientSettings->proposedFleetSettings->adminCostPerPage;
            $this->_costPerPageSettingForReplacements->pageCoverageMonochrome     = $clientSettings->proposedFleetSettings->defaultMonochromeCoverage;
            $this->_costPerPageSettingForReplacements->pageCoverageColor          = $clientSettings->proposedFleetSettings->defaultColorCoverage;
            $this->_costPerPageSettingForReplacements->monochromeLaborCostPerPage = $clientSettings->proposedFleetSettings->defaultMonochromeLaborCostPerPage;
            $this->_costPerPageSettingForReplacements->monochromePartsCostPerPage = $clientSettings->proposedFleetSettings->defaultMonochromePartsCostPerPage;
            $this->_costPerPageSettingForReplacements->colorLaborCostPerPage      = $clientSettings->proposedFleetSettings->defaultColorLaborCostPerPage;
            $this->_costPerPageSettingForReplacements->colorPartsCostPerPage      = $clientSettings->proposedFleetSettings->defaultColorPartsCostPerPage;
            $this->_costPerPageSettingForReplacements->monochromeTonerRankSet     = $clientSettings->proposedFleetSettings->getMonochromeRankSet();
            $this->_costPerPageSettingForReplacements->colorTonerRankSet          = $clientSettings->proposedFleetSettings->getColorRankSet();
            $this->_costPerPageSettingForReplacements->useDevicePageCoverages     = $clientSettings->proposedFleetSettings->useDevicePageCoverages;
            $this->_costPerPageSettingForReplacements->level                      = $clientSettings->proposedFleetSettings->level;
        }

        return $this->_costPerPageSettingForReplacements;
    }

    /**
     * Gets the page counts
     *
     * @return PageCountsModel
     */
    public function getPageCounts ()
    {
        if (!isset($this->_pageCounts))
        {
            $this->_pageCounts = $this->getDevices()->purchasedDeviceInstances->getPageCounts();
        }

        return $this->_pageCounts;
    }

    /**
     *
     */
    public function getNewPageCounts ()
    {
        if (!isset($this->_newPageCounts))
        {
            if ($this->_optimization->getClient()->getClientSettings()->optimizationSettings->autoOptimizeFunctionality)
            {
                $pageCounts = new PageCountsModel();
                foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
                {
                    $replacementMasterDevice = $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_optimization->id);
                    $isUpgradingToColor      = $replacementMasterDevice instanceof MasterDeviceModel && $replacementMasterDevice->isColor() && $deviceInstance->getMasterDevice()->isColor() == false;
                    $ratio                   = ($isUpgradingToColor) ? $this->_optimization->getClient()->getClientSettings()->optimizationSettings->blackToColorRatio : 0;

                    $pageCounts->add($deviceInstance->getPageCounts($ratio));
                    $this->_newPageCounts = $pageCounts;
                }
            }
            else
            {
                $this->_newPageCounts = $this->getDevices()->purchasedDeviceInstances->getPageCounts();
            }
        }

        return $this->_newPageCounts;
    }

    /**
     * Gets fleet percentages
     *
     * @return stdClass
     */
    public function getPercentages ()
    {
        if (!isset($this->Percentages))
        {
            $Percentages                                            = new stdClass();
            $Percentages->TotalColorPercentage                      = 0;
            $Percentages->PurchasedVsLeasedBlackAndWhite            = new stdClass();
            $Percentages->PurchasedVsLeasedBlackAndWhite->Leased    = 0;
            $Percentages->PurchasedVsLeasedBlackAndWhite->Purchased = 0;
            $Percentages->PurchasedVsLeasedColor                    = new stdClass();
            $Percentages->PurchasedVsLeasedColor->Leased            = 0;
            $Percentages->PurchasedVsLeasedColor->Purchased         = 0;
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())
            {
                $Percentages->TotalColorPercentage = $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            }
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly())
            {
                $Percentages->PurchasedVsLeasedBlackAndWhite->Leased    = $this->getDevices()->leasedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly();
                $Percentages->PurchasedVsLeasedBlackAndWhite->Purchased = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly();
            }
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly())
            {
                $Percentages->PurchasedVsLeasedColor->Leased    = $this->getDevices()->leasedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly();
                $Percentages->PurchasedVsLeasedColor->Purchased = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly();
            }
            $this->Percentages = $Percentages;
        }

        return $this->Percentages;
    }

    /**
     * @return RmsExcludedRowModel[]
     */
    public function getExcludedDevices ()
    {
        if (!isset($this->ExcludedDevices))
        {
            $this->ExcludedDevices = array_merge($this->getDevices()->unmappedDeviceInstances->getDeviceInstances(), $this->getDevices()->excludedDeviceInstances->getDeviceInstances());
        }

        return $this->ExcludedDevices;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return DeviceInstanceModel []
     */
    public function getMonthlyHighCostPurchasedDevice (CostPerPageSettingModel $costPerPageSetting)
    {
        if (!isset($this->highCostPurchasedDevices))
        {
            $deviceArray = $this->getDevices()->purchasedDeviceInstances->getDeviceInstances();
            $costArray   = [];
            /**@var $value DeviceInstanceModel */
            foreach ($deviceArray as $key => $deviceInstance)
            {
                $costArray[] = [
                    $key,
                    ($deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->monochromeCostPerPage) +
                    ($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->colorCostPerPage)
                ];
            }

            usort($costArray, [
                $this,
                "descendingSortDevicesByColorCost",
            ]);
            $highCostDevices = [];

            foreach ($costArray as $costs)
            {
                $highCostDevices[] = $deviceArray[$costs[0]];
            }

            $this->highCostPurchasedDevices = $highCostDevices;
        }

        return $this->highCostPurchasedDevices;
    }

    /**
     * Callback function for uSort when we want to sort a device based on power
     * consumption
     *
     * @param DeviceInstanceModel $deviceA
     * @param DeviceInstanceModel $deviceB
     *
     * @return int
     */
    public function descendingSortDevicesByColorCost ($deviceA, $deviceB)
    {
        if ($deviceA[0] == $deviceB[0])
        {
            return 0;
        }

        return ($deviceA[1] > $deviceB[1]) ? -1 : 1;
    }

    /**
     * @return int
     */
    public function getDeviceCount ()
    {
        return count($this->getDevices()->allIncludedDeviceInstances);
    }

    /**
     * @return int
     */
    public function getNumberOfUniqueModels ()
    {
        if (!isset($this->_numberOfUniqueModels))
        {
            $this->_numberOfUniqueModels = count($this->getUniqueDeviceList());
        }

        return $this->_numberOfUniqueModels;
    }


    /**
     * @return MasterDeviceModel[]
     */
    public function getUniqueDeviceList ()
    {
        if (!isset($this->_uniqueDeviceList))
        {
            $masterDevices = [];
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                if (!in_array($deviceInstance->getMasterDevice(), $masterDevices))
                {
                    $masterDevices [] = $deviceInstance->getMasterDevice();
                }
            }
            $this->_uniqueDeviceList = $masterDevices;
        }

        return $this->_uniqueDeviceList;
    }

    /**
     * @return int
     */
    public function getNumberOfColorCapableDevices ()
    {
        if (!isset($this->_numberOfColorCapableDevices))
        {
            $numberOfDevices = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                if ($device->getMasterDevice()->tonerConfigId != TonerConfigModel::BLACK_ONLY)
                {
                    $numberOfDevices++;
                }
            }
            $this->_numberOfColorCapableDevices = $numberOfDevices;
        }

        return $this->_numberOfColorCapableDevices;
    }

    /**
     * @return int
     */
    public function getNumberOfUniquePurchasedModels ()
    {
        if (!isset($this->_numberOfUniquePurchasedModels))
        {
            $numberOfModels   = 0;
            $uniqueModelArray = [];
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
            {
                if (!in_array($device->getMasterDevice()->modelName, $uniqueModelArray))
                {
                    $numberOfModels++;
                    $uniqueModelArray [] = $device->getMasterDevice()->modelName;
                }
            }
            $this->_numberOfUniquePurchasedModels = $numberOfModels;
        }

        return $this->_numberOfUniquePurchasedModels;
    }

    /**
     * @return int
     */
    public function getNumberOfUniquePurchasedToners ()
    {
        if (!isset($this->_numberOfUniquePurchasedToners))
        {
            $this->_numberOfUniquePurchasedToners = count($this->getUniquePurchasedTonerList());
        }

        return $this->_numberOfUniquePurchasedToners;
    }

    /**
     * @return TonerModel[]
     */
    public function getUniquePurchasedTonerList ()
    {
        if (!isset($this->_uniquePurchasedTonerList))
        {
            $uniqueToners = [];
            foreach ($this->getUniquePurchasedDeviceList() as $masterDevice)
            {
                $deviceToners = $masterDevice->getTonersForAssessment($this->getCostPerPageSettingForDealer());
                foreach ($deviceToners as $toner)
                {
                    if (!in_array($toner, $uniqueToners))
                    {
                        $uniqueToners [] = $toner;
                    }
                }
            }
            $this->_uniquePurchasedTonerList = $uniqueToners;
        }

        return $this->_uniquePurchasedTonerList;
    }

    /**
     * @return MasterDeviceModel[]
     */
    public function getUniquePurchasedDeviceList ()
    {
        if (!isset($this->_uniquePurchasedDeviceList))
        {
            $masterDevices = [];
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
            {
                if (!in_array($device->getMasterDevice(), $masterDevices))
                {
                    $masterDevices [] = $device->getMasterDevice();
                }
            }
            $this->_uniquePurchasedDeviceList = $masterDevices;
        }

        return $this->_uniquePurchasedDeviceList;
    }


    /**
     * @return float
     */
    public function getCashHeldInInventory ()
    {
        if (!isset($this->_cashHeldInInventory))
        {
            $inventoryCash = 0.0;
            foreach ($this->getUniquePurchasedTonerList() as $toner)
            {
                $inventoryCash += $toner->cost;
            }
            $this->_cashHeldInInventory = $inventoryCash * 2;
        }

        return $this->_cashHeldInInventory;
    }


    /**
     * @return int
     */
    public function getMaximumMonthlyPrintVolume ()
    {
        if (!isset($this->_maximumMonthlyPrintVolume))
        {
            $maxVolume = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $maxVolume += $deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume;
            }
            $this->_maximumMonthlyPrintVolume = $maxVolume;
        }

        return $this->_maximumMonthlyPrintVolume;
    }

    /**
     * Gets the amount of color capable devices with replacement devices
     *
     * @return int
     */
    public function getNumberOfColorCapableDevicesWithReplacements ()
    {
        $numberOfDevices = 0;
        foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
        {
            $replacementDevice = $device->getReplacementMasterDeviceForHardwareOptimization($this->_optimization->id);
            if ($replacementDevice instanceof MasterDeviceModel)
            {
                if ($replacementDevice->isColor())
                {
                    $numberOfDevices++;
                }
            }
            else if ($device->getMasterDevice()->tonerConfigId != TonerConfigModel::BLACK_ONLY)
            {
                $numberOfDevices++;
            }
        }

        return $numberOfDevices;
    }

    /**
     * Calculates the dealers monthly cost
     *
     * @return number
     */
    public function calculateDealerMonthlyCost ()
    {
        if (!isset($this->_dealerMonthlyCost))
        {
            $this->_dealerMonthlyCost = 0;

            $costPerPageSetting = $this->getCostPerPageSettingForDealer();

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $this->_dealerMonthlyCost += $deviceInstance->calculateMonthlyCost($costPerPageSetting);
            }
        }

        return $this->_dealerMonthlyCost;
    }

    /**
     * Calculates the dealers monthly revenue when using a target cost per page schema
     *
     * @return number
     */
    public function calculateDealerMonthlyRevenueUsingTargetCostPerPage ()
    {
        if (!isset($this->_dealerMonthlyRevenueUsingTargetCostPerPage))
        {
            $this->_dealerMonthlyRevenueUsingTargetCostPerPage = 0;

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $this->_dealerMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly() * $this->_optimization->getClient()->getClientSettings()->genericSettings->targetMonochromeCostPerPage;
                $this->_dealerMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() * $this->_optimization->getClient()->getClientSettings()->genericSettings->targetColorCostPerPage;
            }
        }

        return $this->_dealerMonthlyRevenueUsingTargetCostPerPage;
    }

    /**
     * Calculates the dealers monthly revenue when using a target cost per page schema
     *
     * @return number
     */
    public function calculateOptimizedDealerMonthlyRevenueUsingTargetCostPerPage ()
    {
        if (!isset($this->_dealerOptimizedMonthlyRevenueUsingTargetCostPerPage))
        {
            $this->_dealerOptimizedMonthlyRevenueUsingTargetCostPerPage = 0;

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $ratio = 0;
                if ($this->_optimization->getClient()->getClientSettings()->optimizationSettings->autoOptimizeFunctionality)
                {
                    $replacementMasterDevice = $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_optimization->id);
                    $isUpgradingToColor      = $replacementMasterDevice instanceof MasterDeviceModel && $replacementMasterDevice->isColor() && $deviceInstance->getMasterDevice()->isColor() == false;
                    $ratio                   = ($isUpgradingToColor) ? $this->_optimization->getClient()->getClientSettings()->optimizationSettings->blackToColorRatio : 0;
                }

                $this->_dealerOptimizedMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getPageCounts($ratio)->getBlackPageCount()->getMonthly() * $this->_optimization->getClient()->getClientSettings()->optimizationSettings->optimizedTargetMonochromeCostPerPage;
                $this->_dealerOptimizedMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getPageCounts($ratio)->getColorPageCount()->getMonthly() * $this->_optimization->getClient()->getClientSettings()->optimizationSettings->optimizedTargetColorCostPerPage;
            }
        }

        return $this->_dealerOptimizedMonthlyRevenueUsingTargetCostPerPage;
    }

    /**
     * @return int
     */
    public function calculateMaximumMonthlyPrintVolumeWithReplacements ()
    {
        $maxVolume = 0;
        foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
        {
            if ($deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_optimization->id))
            {
                $maxVolume += $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_optimization->id)->maximumRecommendedMonthlyPageVolume;
            }
            else
            {
                $maxVolume += $deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume;
            }
        }

        return $maxVolume;
    }

    /**
     * Calculates the weighted average monthly cost per page of the current fleet
     *
     * @param bool $recalculate
     *
     * @return CostPerPageModel
     */
    public function calculateDealerWeightedAverageMonthlyCostPerPage ($recalculate = false)
    {
        if (!isset($this->_dealerWeightedAverageMonthlyCostPerPage) || $recalculate)
        {
            $this->_dealerWeightedAverageMonthlyCostPerPage = new CostPerPageModel();

            $costPerPageSetting            = $this->getCostPerPageSettingForDealer();
            $totalMonthlyMonoPagesPrinted  = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly();
            $totalMonthlyColorPagesPrinted = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly();
            $colorCpp                      = 0;
            $monoCpp                       = 0;

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $costPerPage = $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage();

                if ($totalMonthlyMonoPagesPrinted > 0)
                {
                    $monoCpp += ($deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly() * $costPerPage->monochromeCostPerPage) / $totalMonthlyMonoPagesPrinted;
                }
                if ($totalMonthlyColorPagesPrinted > 0 && $deviceInstance->getMasterDevice()->isColor())
                {
                    $colorCpp += ($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() * $costPerPage->colorCostPerPage) / $totalMonthlyColorPagesPrinted;
                }
            }

            $this->_dealerWeightedAverageMonthlyCostPerPage->monochromeCostPerPage = $monoCpp;
            $this->_dealerWeightedAverageMonthlyCostPerPage->colorCostPerPage      = $colorCpp;
        }

        return $this->_dealerWeightedAverageMonthlyCostPerPage;
    }

    /**
     * Calculates the dealers monthly profit when using a target cost per page schema
     *
     * @return number
     */
    public function calculateDealerMonthlyProfitUsingTargetCostPerPage ()
    {
        return $this->calculateDealerMonthlyRevenueUsingTargetCostPerPage() - $this->calculateDealerMonthlyCost();
    }

    /**
     * Calculates the weighted average monthly cost per page when using replacements
     *
     * @return CostPerPageModel
     */
    public function calculateDealerWeightedAverageMonthlyCostPerPageWithReplacements ()
    {
        if (!isset($this->_dealerWeightedAverageMonthlyCostPerPageWithReplacements))
        {
            $this->_dealerWeightedAverageMonthlyCostPerPageWithReplacements = new CostPerPageModel();

            $costPerPageSetting            = $this->getCostPerPageSettingForDealer();
            $totalMonthlyMonoPagesPrinted  = $this->getNewPageCounts()->getBlackPageCount()->getMonthly();
            $totalMonthlyColorPagesPrinted = $this->getNewPageCounts()->getColorPageCount()->getMonthly();
            $colorCpp                      = 0;
            $monoCpp                       = 0;

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $replacementMasterDevice = $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_optimization->id);
                $isUpgradingToColor      = $replacementMasterDevice instanceof MasterDeviceModel && $replacementMasterDevice->isColor() && $deviceInstance->getMasterDevice()->isColor() == false;
                $ratio                   = ($isUpgradingToColor) ? $this->_optimization->getClient()->getClientSettings()->optimizationSettings->blackToColorRatio : 0;

                $costPerPage = $deviceInstance->calculateCostPerPageWithReplacement($costPerPageSetting, $this->_optimization->id);
                $monoCpp += ($deviceInstance->getPageCounts($ratio)->getBlackPageCount()->getMonthly() / $totalMonthlyMonoPagesPrinted) * $costPerPage->monochromeCostPerPage;

                if ($totalMonthlyColorPagesPrinted > 0 && $deviceInstance->getMasterDevice()->isColor())
                {
                    $colorCpp += ($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() / $totalMonthlyColorPagesPrinted) * $costPerPage->colorCostPerPage;
                }
                elseif ($isUpgradingToColor)
                {
                    $colorCpp += ($deviceInstance->getPageCounts($ratio)->getColorPageCount()->getMonthly() / $totalMonthlyColorPagesPrinted) * $costPerPage->colorCostPerPage;
                }
            }

            $this->_dealerWeightedAverageMonthlyCostPerPageWithReplacements->monochromeCostPerPage = $monoCpp;
            $this->_dealerWeightedAverageMonthlyCostPerPageWithReplacements->colorCostPerPage      = $colorCpp;
        }

        return $this->_dealerWeightedAverageMonthlyCostPerPageWithReplacements;
    }

    /**
     * Calculates the dealers monthly profit when using a target cost per page schema and replacement devices
     *
     * @return number
     */
    public function calculateDealerMonthlyProfitUsingTargetCostPerPageAndReplacements ()
    {
        return $this->calculateOptimizedDealerMonthlyRevenueUsingTargetCostPerPage() - $this->calculateDealerMonthlyCostWithReplacements();
    }

    /**
     * Calculates the dealers monthly cost with replacements
     *
     * @return number
     */
    public function calculateDealerMonthlyCostWithReplacements ()
    {
        if (!isset($this->_dealerMonthlyCostWithReplacements))
        {
            $this->_dealerMonthlyCostWithReplacements = 0;

            $replacementCostPerPageSetting = $this->getCostPerPageSettingForReplacements();
            $costPerPageSetting            = $this->getCostPerPageSettingForDealer();

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $replacementMasterDevice = $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_optimization->id);
                if ($replacementMasterDevice instanceof MasterDeviceModel)
                {
                    $isUpgradingToColor = $replacementMasterDevice instanceof MasterDeviceModel && $replacementMasterDevice->isColor() && $deviceInstance->getMasterDevice()->isColor() == false;
                    $ratio              = ($isUpgradingToColor) ? $this->_optimization->getClient()->getClientSettings()->optimizationSettings->blackToColorRatio : 0;
                    $this->_dealerMonthlyCostWithReplacements += $deviceInstance->calculateMonthlyCost($replacementCostPerPageSetting, $replacementMasterDevice, $ratio);
                }
                else
                {
                    $this->_dealerMonthlyCostWithReplacements += $deviceInstance->calculateMonthlyCost($costPerPageSetting);
                }
            }
        }

        return $this->_dealerMonthlyCostWithReplacements;
    }

    /**
     * Gets all the devices grouped by their action
     */
    public function getDevicesGroupedByAction ()
    {
        if (!isset($this->_devicesGroupedByAction))
        {
            $this->_devicesGroupedByAction = [];
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $hardwareOptimizationDeviceInstance = $deviceInstance->getHardwareOptimizationDeviceInstance($this->_optimization->id);
                switch ($hardwareOptimizationDeviceInstance->action)
                {
                    case HardwareOptimizationDeviceInstanceModel::ACTION_KEEP:
                        $this->_devicesGroupedByAction[HardwareOptimizationDeviceInstanceModel::ACTION_KEEP][] = $deviceInstance;
                        break;

                    case HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE:
                        $this->_devicesGroupedByAction[HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE][] = $deviceInstance;
                        break;

                    case HardwareOptimizationDeviceInstanceModel::ACTION_RETIRE:
                        $this->_devicesGroupedByAction[HardwareOptimizationDeviceInstanceModel::ACTION_RETIRE][] = $deviceInstance;
                        break;

                    case HardwareOptimizationDeviceInstanceModel::ACTION_DNR:
                        $this->_devicesGroupedByAction[HardwareOptimizationDeviceInstanceModel::ACTION_DNR][] = $deviceInstance;
                        break;

                    case HardwareOptimizationDeviceInstanceModel::ACTION_UPGRADE:
                        $this->_devicesGroupedByAction[HardwareOptimizationDeviceInstanceModel::ACTION_UPGRADE][] = $deviceInstance;
                        break;
                }
            }
        }

        return $this->_devicesGroupedByAction;
    }

    public function dealer_png($name) {
        $model = new OptimizationDealerModel($this->_optimization);
        $myPicture = $this->getChart($name, $model);
        if ($myPicture) {
            header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
            header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
            header( 'Cache-Control: no-store, no-cache, must-revalidate' );
            header( 'Cache-Control: post-check=0, pre-check=0', false );
            header( 'Pragma: no-cache' );
            $myPicture->autoOutput();
        }
    }

    public function customer_png($name) {
        $model = new OptimizationCustomerModel($this->_optimization);
        $myPicture = $this->getChart($name, $model);
        if ($myPicture) {
            header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
            header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
            header( 'Cache-Control: no-store, no-cache, must-revalidate' );
            header( 'Cache-Control: post-check=0, pre-check=0', false );
            header( 'Pragma: no-cache' );
            $myPicture->autoOutput();
        }
    }

    public function getCustomerCharts()
    {
        $model = new OptimizationCustomerModel($this->_optimization);
        $result=array();
        foreach (array(
                     0=>'AverageMonthlyPagesBarGraph',
                     1=>'AverageMonthlyPagesPerEmployeeBarGraph',
                     2=>'LeasedVsPurchasedPageCountBarGraph',
                     3=>'OptimizationChartSummary',
                     4=>'HardwareUtilizationCapacityPercent',
                     5=>'EmployeesPerDevice',
                     6=>'PrintingListJITCompatible',
                     7=>'LeasedVsPurchasedBarGraph',
                     8=>'TechnologyFeatures',
                     9=>'TechnologyFeaturesOptimized',
                     10=>'AgeOfDevices',
                     11=>'AgeOfDevicesOptimized',
                     12=>'UniqueSupplyTypes',
                     13=>'UniqueSupplyTypesOptimized',
                     14=>'ColorCapableDevices',
                     15=>'ColorCapableDevicesOptimized',
                 ) as $i=>$name) {
            $result[$i] = $this->getChart($name, $model);
        }
        return $result;
    }

    public function getDealerCharts()
    {
        $model = new OptimizationDealerModel($this->_optimization);
        $result=array();
        foreach (array(
                     'OptimizationChartSummary',
                     'OtherDevicesInFleet',
                 ) as $name) {
            $result[$name] = $this->getChart($name, $model);
        }
        return $result;
    }

    public function getChart($name, $model) {
        $dealerBranding = My_Brand::getDealerBranding();
        $companyName   = mb_strimwidth($this->_optimization->getClient()->companyName, 0, 23, "...");
        $employeeCount = $this->_optimization->getClient()->employeeCount;

        $pieChartStyles  = ["SecondPass" => true, "ValuePosition" => PIE_VALUE_INSIDE, "WriteValues" => PIE_VALUE_PERCENTAGE, "SliceHeight" => 10];
        $pieLegendStyles = ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "BoxSize" => 10];

        $hexToRGBConverter = new \Tangent\Format\HexToRGB();
        $factory = new pChartFactory();
        $myPicture = null;
        if (file_exists($file = dirname(__FILE__).'/charts/'.$name.'.php')) require $file;
        return $myPicture;
    }

    /**
     * @return HardwareOptimizationModel
     */
    public function getOptimization()
    {
        return $this->_optimization;
    }



}