<?php
/**
 * Class Hardwareoptimization_ViewModel_Optimization
 */
class Hardwareoptimization_ViewModel_Optimization
{
    /** @var Hardwareoptimization_Model_Hardware_Optimization */
    protected $_optimization;
    /** @var float */
    protected $_cashHeldInInventory;
    /** @var Proposalgen_Model_MasterDevice [] */
    protected $_uniqueDeviceList;
    /** @var Proposalgen_Model_MasterDevice [] */
    protected $_uniquePurchasedDeviceList;
    /** @var Proposalgen_Model_Toner [] */
    protected $_uniquePurchasedTonerList;
    /** @var int */
    protected $_numberOfUniqueModels;
    /** @var int */
    protected $_numberOfColorCapableDevices;
    /** @var int */
    protected $_numberOfUniquePurchasedModels;
    /** @var int */
    protected $_numberOfUniquePurchasedToners;
    /** @var int */
    protected $_maximumMonthlyPrintVolume;
    /** @var  Proposalgen_Model_PageCounts */
    protected $_pageCounts;
    /** @var Hardwareoptimization_ViewModel_Devices */
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
     * Constructor
     *
     * @param int|Hardwareoptimization_Model_Hardware_Optimization $hardwareOptimization
     */
    public function __construct ($hardwareOptimization)
    {
        if ($hardwareOptimization instanceof Hardwareoptimization_Model_Hardware_Optimization)
        {
            $this->_optimization = $hardwareOptimization;
        }
        else
        {
            $this->_optimization = Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->find($hardwareOptimization);
        }
    }

    /**
     * Gets the devices object for the report.
     *
     * @return Hardwareoptimization_ViewModel_Devices
     */
    public function getDevices ()
    {
        if (!isset($this->_devices))
        {
            $this->_devices = new Hardwareoptimization_ViewModel_Devices($this->_optimization);
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

            $reportSettings                                           = $this->_optimization->getHardwareOptimizationSetting();
            $this->_costPerPageSettingForDealer->adminCostPerPage     = $reportSettings->adminCostPerPage;
            $this->_costPerPageSettingForDealer->pricingConfiguration = $reportSettings->getPricingConfig($reportSettings->dealerPricingConfigId);
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

            $reportSettings                                                 = $this->_optimization->getHardwareOptimizationSetting();
            $this->_costPerPageSettingForReplacements->adminCostPerPage     = $reportSettings->adminCostPerPage;
            $this->_costPerPageSettingForReplacements->pricingConfiguration = $reportSettings->getPricingConfig($reportSettings->replacementPricingConfigId);
        }

        return $this->_costPerPageSettingForReplacements;
    }

    /**
     * Gets the page counts
     *
     * @return Proposalgen_Model_PageCounts
     */
    public function getPageCounts ()
    {
        if (!isset($this->_pageCounts))
        {
            $this->_pageCounts = $this->getDevices()->allIncludedDeviceInstances->getPageCounts();
        }

        return $this->_pageCounts;
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getMonthly())
            {
                $Percentages->TotalColorPercentage = $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->color->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getMonthly();
            }
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->monochrome->getYearly())
            {
                $Percentages->PurchasedVsLeasedBlackAndWhite->Leased    = $this->getDevices()->leasedDeviceInstances->getPageCounts()->monochrome->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->monochrome->getYearly();
                $Percentages->PurchasedVsLeasedBlackAndWhite->Purchased = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->monochrome->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->monochrome->getYearly();
            }
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->color->getYearly())
            {
                $Percentages->PurchasedVsLeasedColor->Leased    = $this->getDevices()->leasedDeviceInstances->getPageCounts()->color->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->color->getYearly();
                $Percentages->PurchasedVsLeasedColor->Purchased = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->color->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->color->getYearly();
            }
            $this->Percentages = $Percentages;
        }

        return $this->Percentages;
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
                $costArray[] = array($key, ($deviceInstance->getPageCounts()->color->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->colorCostPerPage) + ($deviceInstance->getPageCounts()->monochrome->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->monochromeCostPerPage));
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
    public function getNumberOfColorCapableDevicesWithReplacements ()
    {
        $numberOfDevices = 0;
        foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
        {
            $replacementDevice = $device->getReplacementMasterDeviceForHardwareOptimization($this->_optimization->id);
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
                $this->_dealerMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getPageCounts()->monochrome->getMonthly() * $this->_optimization->getHardwareOptimizationSetting()->targetMonochromeCostPerPage;
                $this->_dealerMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getPageCounts()->color->getMonthly() * $this->_optimization->getHardwareOptimizationSetting()->targetColorCostPerPage;
            }
        }

        return $this->_dealerMonthlyRevenueUsingTargetCostPerPage;
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
                $maxVolume += $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_optimization->id)->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForReplacements());
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
            $totalMonthlyMonoPagesPrinted  = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->monochrome->getMonthly();
            $totalMonthlyColorPagesPrinted = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->color->getMonthly();
            $colorCpp                      = 0;
            $monoCpp                       = 0;

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $costPerPage = $deviceInstance->calculateCostPerPage($costPerPageSetting);
                $monoCpp += ($deviceInstance->getPageCounts()->monochrome->getMonthly() / $totalMonthlyMonoPagesPrinted) * $costPerPage->monochromeCostPerPage;
                if ($totalMonthlyColorPagesPrinted > 0 && $deviceInstance->getMasterDevice()->isColor())
                {
                    $colorCpp += ($deviceInstance->getPageCounts()->color->getMonthly() / $totalMonthlyColorPagesPrinted) * $costPerPage->colorCostPerPage;
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
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateDealerWeightedAverageMonthlyCostPerPageWithReplacements ()
    {
        if (!isset($this->_dealerWeightedAverageMonthlyCostPerPageWithReplacements))
        {
            $this->_dealerWeightedAverageMonthlyCostPerPageWithReplacements = new Proposalgen_Model_CostPerPage();

            $costPerPageSetting            = $this->getCostPerPageSettingForDealer();
            $totalMonthlyMonoPagesPrinted  = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->monochrome->getMonthly();
            $totalMonthlyColorPagesPrinted = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->color->getMonthly();
            $colorCpp                      = 0;
            $monoCpp                       = 0;

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $costPerPage = $deviceInstance->calculateCostPerPageWithReplacement($costPerPageSetting, $this->_optimization->id);
                $monoCpp += ($deviceInstance->getPageCounts()->monochrome->getMonthly() / $totalMonthlyMonoPagesPrinted) * $costPerPage->monochromeCostPerPage;
                if ($totalMonthlyColorPagesPrinted > 0 && $deviceInstance->getMasterDevice()->isColor())
                {
                    $colorCpp += ($deviceInstance->getPageCounts()->color->getMonthly() / $totalMonthlyColorPagesPrinted) * $costPerPage->colorCostPerPage;
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
        return $this->calculateDealerMonthlyRevenueUsingTargetCostPerPage() - $this->calculateDealerMonthlyCostWithReplacements();
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

            $costPerPageSetting = $this->getCostPerPageSettingForDealer();

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $this->_dealerMonthlyCostWithReplacements += $deviceInstance->calculateMonthlyCost($costPerPageSetting, $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_optimization->id));
            }
        }

        return $this->_dealerMonthlyCostWithReplacements;
    }

}
