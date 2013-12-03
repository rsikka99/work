<?php
/**
 * Class Memjetoptimization_ViewModel_Optimization
 */
class Memjetoptimization_ViewModel_Optimization
{
    /**
     * @var Memjetoptimization_Model_Memjet_Optimization
     */
    protected $_optimization;

    /**
     * @var float
     */
    protected $_cashHeldInInventory;
    /**
     * @var Proposalgen_Model_MasterDevice []
     */
    protected $_uniqueDeviceList;

    /**
     * @var Proposalgen_Model_MasterDevice []
     */
    protected $_uniquePurchasedDeviceList;

    /**
     * @var Proposalgen_Model_Toner []
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
     * @var  Proposalgen_Model_PageCounts[]
     */
    protected $_pageCounts = array();

    /**
     * @var Memjetoptimization_ViewModel_Devices
     */
    protected $_devices;

    /**
     * The weighted average monthly cost per page when using replacements
     *
     * @var Proposalgen_Model_CostPerPage
     */
    protected $_dealerWeightedAverageMonthlyCostPerPageWithReplacements;

    /**
     * The dealers monthly cost with replacements
     *
     * @var number
     */
    protected $_dealerMonthlyCostWithReplacements;

    /**
     * Cost per page setting for a dealer
     *
     * @var Proposalgen_Model_CostPerPageSetting
     */
    protected $_costPerPageSettingForDealer;

    /**
     * Cost per page setting for replacement devices
     *
     * @var Proposalgen_Model_CostPerPageSetting
     */
    protected $_costPerPageSettingForReplacements;

    /**
     * @var float
     */
    protected $_dealerMonthlyRevenueUsingTargetCostPerPageWithReplacements;

    /**
     * @var Proposalgen_Model_PageCounts
     */
    protected $_totalMonthlyPageCountsWithReplacements;

    /**
     * @var stdClass
     */
    protected $_percentages;

    /**
     * Constructor
     *
     * @param int|Memjetoptimization_Model_Memjet_Optimization $memjetOptimization
     */
    public function __construct ($memjetOptimization)
    {
        if ($memjetOptimization instanceof Memjetoptimization_Model_Memjet_Optimization)
        {
            $this->_optimization = $memjetOptimization;
        }
        else
        {
            $this->_optimization = Memjetoptimization_Model_Mapper_Memjet_Optimization::getInstance()->find($memjetOptimization);
        }
    }

    /**
     * Gets the devices object for the report.
     *
     * @return Memjetoptimization_ViewModel_Devices
     */
    public function getDevices ()
    {
        if (!isset($this->_devices))
        {
            $this->_devices = new Memjetoptimization_ViewModel_Devices($this->_optimization);
        }

        return $this->_devices;
    }

    /**
     * Gets the cost per page settings for the dealers point of view
     *
     * @return Proposalgen_Model_CostPerPageSetting
     */
    public function getCostPerPageSettingForDealer ()
    {
        if (!isset($this->_costPerPageSettingForDealer))
        {
            $this->_costPerPageSettingForDealer = new Proposalgen_Model_CostPerPageSetting();

            $reportSettings                                             = $this->_optimization->getMemjetoptimizationSetting();
            $this->_costPerPageSettingForDealer->adminCostPerPage       = $reportSettings->adminCostPerPage;
            $this->_costPerPageSettingForDealer->pageCoverageMonochrome = $reportSettings->pageCoverageMonochrome;
            $this->_costPerPageSettingForDealer->pageCoverageColor      = $reportSettings->pageCoverageColor;
            $this->_costPerPageSettingForDealer->laborCostPerPage       = $reportSettings->laborCostPerPage;
            $this->_costPerPageSettingForDealer->partsCostPerPage       = $reportSettings->partsCostPerPage;
            $this->_costPerPageSettingForDealer->monochromeTonerRankSet = $reportSettings->getDealerMonochromeRankSet();
            $this->_costPerPageSettingForDealer->colorTonerRankSet      = $reportSettings->getDealerColorRankSet();
        }

        return $this->_costPerPageSettingForDealer;
    }

    /**
     * Gets the cost per page settings for the replacement devices
     *
     * @return Proposalgen_Model_CostPerPageSetting
     */
    public function getCostPerPageSettingForReplacements ()
    {
        if (!isset($this->_costPerPageSettingForReplacements))
        {
            $this->_costPerPageSettingForReplacements = new Proposalgen_Model_CostPerPageSetting();

            $reportSettings                                                   = $this->_optimization->getMemjetoptimizationSetting();
            $this->_costPerPageSettingForReplacements->adminCostPerPage       = $reportSettings->adminCostPerPage;
            $this->_costPerPageSettingForReplacements->pageCoverageMonochrome = $reportSettings->pageCoverageMonochrome;
            $this->_costPerPageSettingForReplacements->pageCoverageColor      = $reportSettings->pageCoverageColor;
            $this->_costPerPageSettingForReplacements->laborCostPerPage       = $reportSettings->laborCostPerPage;
            $this->_costPerPageSettingForReplacements->partsCostPerPage       = $reportSettings->partsCostPerPage;
            $this->_costPerPageSettingForReplacements->monochromeTonerRankSet = $reportSettings->getReplacementMonochromeRankSet();
            $this->_costPerPageSettingForReplacements->colorTonerRankSet      = $reportSettings->getReplacementColorRankSet();
        }

        return $this->_costPerPageSettingForReplacements;
    }

    /**
     * Gets the page counts
     *
     * @param int $blackToColorRatio
     *
     * @return Proposalgen_Model_PageCounts
     */
    public function getPageCounts ($blackToColorRatio = 0)
    {
        if (!isset($this->_pageCounts[$blackToColorRatio]))
        {
            if ($blackToColorRatio > 0)
            {
                $pageCounts = new Proposalgen_Model_PageCounts();
                foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
                {
                    $memjetReplacement  = $deviceInstance->getReplacementMasterDeviceForMemjetOptimization($this->_optimization->id);
                    $isUpgradingToColor = $memjetReplacement instanceof Proposalgen_Model_MasterDevice && $memjetReplacement->isColor() && $deviceInstance->getMasterDevice()->isColor() == false;
                    $ratio              = ($isUpgradingToColor) ? $blackToColorRatio : 0;

                    $pageCounts->add($deviceInstance->getPageCounts($ratio));
                    $this->_pageCounts[$blackToColorRatio] = $pageCounts;
                }
            }
            else
            {
                $this->_pageCounts[$blackToColorRatio] = $this->getDevices()->allIncludedDeviceInstances->getPageCounts();
            }
        }

        return $this->_pageCounts[$blackToColorRatio];
    }

    /**
     * Gets fleet percentages
     *
     * @return stdClass
     */
    public function getPercentages ()
    {
        if (!isset($this->_percentages))
        {
            $fleetPercentages                                            = new stdClass();
            $fleetPercentages->TotalColorPercentage                      = 0;
            $fleetPercentages->PurchasedVsLeasedBlackAndWhite            = new stdClass();
            $fleetPercentages->PurchasedVsLeasedBlackAndWhite->Leased    = 0;
            $fleetPercentages->PurchasedVsLeasedBlackAndWhite->Purchased = 0;
            $fleetPercentages->PurchasedVsLeasedColor                    = new stdClass();
            $fleetPercentages->PurchasedVsLeasedColor->Leased            = 0;
            $fleetPercentages->PurchasedVsLeasedColor->Purchased         = 0;
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())
            {
                $fleetPercentages->TotalColorPercentage = $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            }
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly())
            {
                $fleetPercentages->PurchasedVsLeasedBlackAndWhite->Leased    = $this->getDevices()->leasedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly();
                $fleetPercentages->PurchasedVsLeasedBlackAndWhite->Purchased = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly();
            }
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly())
            {
                $fleetPercentages->PurchasedVsLeasedColor->Leased    = $this->getDevices()->leasedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly();
                $fleetPercentages->PurchasedVsLeasedColor->Purchased = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly();
            }
            $this->_percentages = $fleetPercentages;
        }

        return $this->_percentages;
    }

    /**
     * @return Proposalgen_Model_Rms_Excluded_Row[]
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return Proposalgen_Model_DeviceInstance []
     */
    public function getMonthlyHighCostPurchasedDevice (Proposalgen_Model_CostPerPageSetting $costPerPageSetting)
    {
        if (!isset($this->highCostPurchasedDevices))
        {
            $deviceArray = $this->getDevices()->purchasedDeviceInstances->getDeviceInstances();
            $costArray   = array();
            /**@var $value Proposalgen_Model_DeviceInstance */
            foreach ($deviceArray as $key => $deviceInstance)
            {
                $costArray[] = array($key, ($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->colorCostPerPage) + ($deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->monochromeCostPerPage));
            }

            usort($costArray, array(
                                   $this,
                                   "descendingSortDevicesByColorCost"
                              ));
            $highCostDevices = array();

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
     * @param Proposalgen_Model_DeviceInstance $deviceA
     * @param Proposalgen_Model_DeviceInstance $deviceB
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
        return $this->getDevices()->allIncludedDeviceInstances->getCount();
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
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function getUniqueDeviceList ()
    {
        if (!isset($this->_uniqueDeviceList))
        {
            $masterDevices = array();
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
                if ($device->getMasterDevice()->tonerConfigId != Proposalgen_Model_TonerConfig::BLACK_ONLY)
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
            $uniqueModelArray = array();
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
     * @return Proposalgen_Model_Toner[]
     */
    public function getUniquePurchasedTonerList ()
    {
        if (!isset($this->_uniquePurchasedTonerList))
        {
            $uniqueToners = array();
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
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function getUniquePurchasedDeviceList ()
    {
        if (!isset($this->_uniquePurchasedDeviceList))
        {
            $masterDevices = array();
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
                $maxVolume += $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForReplacements());
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
    public function getNumberOfDevicesWithReplacements ()
    {
        $numberOfDevices = 0;
        foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
        {
            if ($device->getReplacementMasterDeviceForMemjetoptimization($this->_optimization->id) instanceof Proposalgen_Model_MasterDevice)
            {
                $numberOfDevices++;
            }
        }

        return $numberOfDevices;
    }

    /**
     * Gets the amount of color capable devices with replacement devices
     *
     * @return int
     */
    public function getNumberOfColorCapableDevicesWithReplacements ()
    {
        $numberOfDevices = 0;
        foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
        {
            $replacementDevice = $device->getReplacementMasterDeviceForMemjetoptimization($this->_optimization->id);
            if ($replacementDevice instanceof Proposalgen_Model_MasterDevice)
            {
                if ($replacementDevice->isColor())
                {
                    $numberOfDevices++;
                }
            }
            else if ($device->getMasterDevice()->tonerConfigId != Proposalgen_Model_TonerConfig::BLACK_ONLY)
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
                $this->_dealerMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly() * $this->_optimization->getMemjetoptimizationSetting()->targetMonochromeCostPerPage;
                $this->_dealerMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() * $this->_optimization->getMemjetoptimizationSetting()->targetColorCostPerPage;
            }
        }

        return $this->_dealerMonthlyRevenueUsingTargetCostPerPage;
    }

    /**
     * Calculates the dealers monthly revenue when using a target cost per page schema with replacements
     *
     * @param int $blackToColorRatio
     *
     * @return number
     */
    public function calculateDealerMonthlyRevenueUsingTargetCostPerPageWithReplacements ($blackToColorRatio = 0)
    {
        if (!isset($this->_dealerMonthlyRevenueUsingTargetCostPerPageWithReplacements))
        {
            $total    = 0;
            $settings = $this->_optimization->getMemjetoptimizationSetting();

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $replacementDevice = $deviceInstance->getReplacementMasterDeviceForMemjetoptimization($this->_optimization->id);

                /**
                 * Only need to change the ratio of pages when we've upgraded to color functionality
                 */
                if ($replacementDevice instanceof Proposalgen_Model_MasterDevice && $deviceInstance->getMasterDevice()->isColor() == false && $replacementDevice->isColor())
                {
                    $total += $deviceInstance->getPageCounts($blackToColorRatio)->getBlackPageCount()->getMonthly() * $settings->targetMonochromeCostPerPage;
                    $total += $deviceInstance->getPageCounts($blackToColorRatio)->getColorPageCount()->getMonthly() * $settings->targetColorCostPerPage;
                }
                else
                {
                    $total += $deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly() * $settings->targetMonochromeCostPerPage;
                    $total += $deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() * $settings->targetColorCostPerPage;
                }

            }

            $this->_dealerMonthlyRevenueUsingTargetCostPerPageWithReplacements = $total;
        }

        return $this->_dealerMonthlyRevenueUsingTargetCostPerPageWithReplacements;
    }

    /**
     * @return int
     */
    public function calculateMaximumMonthlyPrintVolumeWithReplacements ()
    {
        $maxVolume = 0;
        foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
        {
            if ($deviceInstance->getReplacementMasterDeviceForMemjetoptimization($this->_optimization->id))
            {
                $maxVolume += $deviceInstance->getReplacementMasterDeviceForMemjetoptimization($this->_optimization->id)->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForReplacements());
            }
            else
            {
                $maxVolume += $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForReplacements());
            }
        }

        return $maxVolume;
    }

    /**
     * Calculates the weighted average monthly cost per page of the current fleet
     *
     * @param bool $recalculate
     *
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateDealerWeightedAverageMonthlyCostPerPage ($recalculate = false)
    {
        if (!isset($this->_dealerWeightedAverageMonthlyCostPerPage) || $recalculate)
        {
            $this->_dealerWeightedAverageMonthlyCostPerPage = new Proposalgen_Model_CostPerPage();

            $costPerPageSetting            = $this->getCostPerPageSettingForDealer();
            $totalMonthlyMonoPagesPrinted  = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly();
            $totalMonthlyColorPagesPrinted = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly();
            $colorCpp                      = 0;
            $monoCpp                       = 0;

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $costPerPage = $deviceInstance->calculateCostPerPage($costPerPageSetting);
                if ($totalMonthlyMonoPagesPrinted > 0)
                {
                    $monoCpp += ($deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly() / $totalMonthlyMonoPagesPrinted) * $costPerPage->monochromeCostPerPage;
                }
                if ($totalMonthlyColorPagesPrinted > 0 && $deviceInstance->getMasterDevice()->isColor())
                {
                    $colorCpp += ($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() / $totalMonthlyColorPagesPrinted) * $costPerPage->colorCostPerPage;
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
     * @param int $blackToColorRatio
     *
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateDealerWeightedAverageMonthlyCostPerPageWithReplacements ($blackToColorRatio = 0)
    {
        if (!isset($this->_dealerWeightedAverageMonthlyCostPerPageWithReplacements))
        {
            $this->_dealerWeightedAverageMonthlyCostPerPageWithReplacements = new Proposalgen_Model_CostPerPage();

            $costPerPageSetting            = $this->getCostPerPageSettingForDealer();
            $totalMonthlyMonoPagesPrinted  = 0;
            $totalMonthlyColorPagesPrinted = 0;

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $memjetReplacement  = $deviceInstance->getReplacementMasterDeviceForMemjetOptimization($this->_optimization->id);
                $isUpgradingToColor = $memjetReplacement instanceof Proposalgen_Model_MasterDevice && $memjetReplacement->isColor() && $deviceInstance->getMasterDevice()->isColor() == false;
                $ratio              = ($isUpgradingToColor) ? $blackToColorRatio : 0;

                $totalMonthlyMonoPagesPrinted += $deviceInstance->getPageCounts($ratio)->getBlackPageCount()->getMonthly();
                $totalMonthlyColorPagesPrinted += $deviceInstance->getPageCounts($ratio)->getColorPageCount()->getMonthly();
            }

            $colorCpp = 0;
            $monoCpp  = 0;

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $memjetReplacement  = $deviceInstance->getReplacementMasterDeviceForMemjetOptimization($this->_optimization->id);
                $isUpgradingToColor = $memjetReplacement instanceof Proposalgen_Model_MasterDevice && $memjetReplacement->isColor() && $deviceInstance->getMasterDevice()->isColor() == false;
                $ratio              = ($isUpgradingToColor) ? $blackToColorRatio : 0;
                $costPerPage        = $deviceInstance->calculateMemjetCostPerPageWithReplacement($costPerPageSetting, $this->_optimization->id);

                /**
                 * Mono CPP
                 */
                $monoCpp += ($deviceInstance->getPageCounts($ratio)->getBlackPageCount()->getMonthly() / $totalMonthlyMonoPagesPrinted) * $costPerPage->monochromeCostPerPage;

                /**
                 * Only calculate when we are a color device or we are being replaced by a color device and we have color volume in the fleet
                 */
                if ($totalMonthlyColorPagesPrinted > 0 && ($deviceInstance->getMasterDevice()->isColor() || ($memjetReplacement instanceof Proposalgen_Model_MasterDevice && $memjetReplacement->isColor())))
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
     * @param int $blackToColorRatio
     *
     * @return number
     */
    public function calculateDealerMonthlyProfitUsingTargetCostPerPageAndReplacements ($blackToColorRatio)
    {
        return $this->calculateDealerMonthlyRevenueUsingTargetCostPerPageWithReplacements($blackToColorRatio) - $this->calculateDealerMonthlyCostWithReplacements($blackToColorRatio);
    }

    /**
     * Calculates the dealers monthly cost with replacements
     *
     * @param int $blackToColorRatio
     *
     * @return number
     */
    public function calculateDealerMonthlyCostWithReplacements ($blackToColorRatio = 0)
    {
        if (!isset($this->_dealerMonthlyCostWithReplacements))
        {
            $this->_dealerMonthlyCostWithReplacements = 0;

            $costPerPageSetting = $this->getCostPerPageSettingForDealer();

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $replacementDevice = $deviceInstance->getReplacementMasterDeviceForMemjetoptimization($this->_optimization->id);
                if ($replacementDevice instanceof Proposalgen_Model_MasterDevice)
                {
                    $replacementMasterDevice = $deviceInstance->getReplacementMasterDeviceForMemjetoptimization($this->_optimization->id);
                    $ratio                   = ($replacementDevice->isColor() && $deviceInstance->getMasterDevice()->isColor() == false) ? $blackToColorRatio : 0;
                    $this->_dealerMonthlyCostWithReplacements += $deviceInstance->calculateMonthlyCost($costPerPageSetting, $replacementMasterDevice, $ratio);
                }
                else
                {
                    $this->_dealerMonthlyCostWithReplacements += $deviceInstance->calculateMonthlyCost($costPerPageSetting);
                }
            }
        }

        return $this->_dealerMonthlyCostWithReplacements;
    }

    public function getTotalMonthlyPageCountsWithReplacements ($blackToColorRatio = 0)
    {
        if (!isset($this->_totalMonthlyPageCountsWithReplacements))
        {
            $pageCounts = new Proposalgen_Model_PageCounts();

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $memjetReplacement = $deviceInstance->getReplacementMasterDeviceForMemjetOptimization($this->_optimization->id);
                $ratio             = ($memjetReplacement instanceof Proposalgen_Model_MasterDevice && $memjetReplacement->isColor() && $deviceInstance->getMasterDevice()->isColor() == false) ? $blackToColorRatio : 0;

                $pageCounts->add($deviceInstance->getPageCounts($ratio));
            }

            $this->_totalMonthlyPageCountsWithReplacements = $pageCounts;
        }

        return $this->_totalMonthlyPageCountsWithReplacements;
    }
}
