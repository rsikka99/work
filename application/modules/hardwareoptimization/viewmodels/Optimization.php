<?php
class Hardwareoptimization_ViewModel_Optimization
{
    /**
     * @var Hardwareoptimization_Model_Hardware_Optimization
     */
    protected $_optimization;

    /**
     * @var Hardwareoptimization_ViewModel_Devices
     */
    protected $_purchaseDevices;

    /**
     * @var Hardwareoptimization_ViewModel_Devices
     */
    protected $_leasedDevices;

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
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getPurchasedDevices ()
    {
        if (!isset($this->_purchaseDevices))
        {
            $this->_purchaseDevices = $this->getDevices()->purchasedDeviceInstances;
        }

        return $this->_purchaseDevices;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getLeasedDevices ()
    {
        if (!isset($this->_leasedDevices))
        {
            $this->_leasedDevices = $this->getDevices()->leasedDeviceInstances;
        }

        return $this->_leasedDevices;
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

            foreach ($this->getPurchasedDevices() as $deviceInstance)
            {
                $this->_dealerMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getAverageMonthlyBlackAndWhitePageCount() * $this->_optimization->getHardwareOptimizationSetting()->targetMonochromeCostPerPage;
                $this->_dealerMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getAverageMonthlyColorPageCount() * $this->_optimization->getHardwareOptimizationSetting()->targetColorCostPerPage;
            }
        }

        return $this->_dealerMonthlyRevenueUsingTargetCostPerPage;
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
     * Calculates the weighted average monthly cost per page of the current fleet
     *
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateDealerWeightedAverageMonthlyCostPerPage ()
    {
        if (!isset($this->_dealerWeightedAverageMonthlyCostPerPage))
        {
            $this->_dealerWeightedAverageMonthlyCostPerPage = new Proposalgen_Model_CostPerPage();

            $costPerPageSetting            = $this->getCostPerPageSettingForDealer();
            $totalMonthlyMonoPagesPrinted  = $this->getPageCounts()->Purchased->BlackAndWhite->Monthly;
            $totalMonthlyColorPagesPrinted = $this->getPageCounts()->Purchased->Color->Monthly;
            $colorCpp                      = 0;
            $monoCpp                       = 0;

            foreach ($this->getPurchasedDevices() as $deviceInstance)
            {
                $costPerPage = $deviceInstance->calculateCostPerPage($costPerPageSetting);
                $monoCpp += ($deviceInstance->getAverageMonthlyBlackAndWhitePageCount() / $totalMonthlyMonoPagesPrinted) * $costPerPage->monochromeCostPerPage;
                if ($totalMonthlyColorPagesPrinted > 0 && $deviceInstance->getMasterDevice()->isColor())
                {
                    $colorCpp += ($deviceInstance->getAverageMonthlyColorPageCount() / $totalMonthlyColorPagesPrinted) * $costPerPage->colorCostPerPage;
                }
            }

            $this->_dealerWeightedAverageMonthlyCostPerPage->monochromeCostPerPage = $monoCpp;
            $this->_dealerWeightedAverageMonthlyCostPerPage->colorCostPerPage      = $colorCpp;
        }

        return $this->_dealerWeightedAverageMonthlyCostPerPage;
    }

    /**
     * Gets fleet page counts
     *
     * @return stdClass
     */
    public function getPageCounts ()
    {
        if (!isset($this->PageCounts))
        {
            $pageCounts = new stdClass();
            // Purchased Pages
            $pageCounts->Purchased                         = new stdClass();
            $pageCounts->Purchased->BlackAndWhite          = new stdClass();
            $pageCounts->Purchased->BlackAndWhite->Monthly = 0;
            $pageCounts->Purchased->Color                  = new stdClass();
            $pageCounts->Purchased->Color->Monthly         = 0;
            $pageCounts->Purchased->Combined               = new stdClass();
            foreach ($this->getPurchasedDevices() as $device)
            {
                $pageCounts->Purchased->BlackAndWhite->Monthly += $device->getAverageMonthlyBlackAndWhitePageCount();
                $pageCounts->Purchased->Color->Monthly += $device->getAverageMonthlyColorPageCount();
            }
            $pageCounts->Purchased->BlackAndWhite->Yearly = $pageCounts->Purchased->BlackAndWhite->Monthly * 12;
            $pageCounts->Purchased->Color->Yearly         = $pageCounts->Purchased->Color->Monthly * 12;
            $pageCounts->Purchased->Combined->Monthly     = $pageCounts->Purchased->BlackAndWhite->Monthly + $pageCounts->Purchased->Color->Monthly;
            $pageCounts->Purchased->Combined->Yearly      = $pageCounts->Purchased->BlackAndWhite->Yearly + $pageCounts->Purchased->Color->Yearly;
            // Leased Pages
            $pageCounts->Leased                         = new stdClass();
            $pageCounts->Leased->BlackAndWhite          = new stdClass();
            $pageCounts->Leased->BlackAndWhite->Monthly = 0;
            $pageCounts->Leased->Color                  = new stdClass();
            $pageCounts->Leased->Color->Monthly         = 0;
            $pageCounts->Leased->Combined               = new stdClass();
            foreach ($this->getLeasedDevices() as $device)
            {
                $pageCounts->Leased->BlackAndWhite->Monthly += $device->getAverageMonthlyBlackAndWhitePageCount();
                $pageCounts->Leased->Color->Monthly += $device->getAverageMonthlyColorPageCount();
            }
            $pageCounts->Leased->BlackAndWhite->Yearly = $pageCounts->Leased->BlackAndWhite->Monthly * 12;
            $pageCounts->Leased->Color->Yearly         = $pageCounts->Leased->Color->Monthly * 12;
            $pageCounts->Leased->Combined->Monthly     = $pageCounts->Leased->BlackAndWhite->Monthly + $pageCounts->Leased->Color->Monthly;
            $pageCounts->Leased->Combined->Yearly      = $pageCounts->Leased->BlackAndWhite->Yearly + $pageCounts->Leased->Color->Yearly;
            // Total Pages
            $pageCounts->Total                         = new stdClass();
            $pageCounts->Total->BlackAndWhite          = new stdClass();
            $pageCounts->Total->BlackAndWhite->Monthly = $pageCounts->Purchased->BlackAndWhite->Monthly + $pageCounts->Leased->BlackAndWhite->Monthly;
            $pageCounts->Total->BlackAndWhite->Yearly  = $pageCounts->Purchased->BlackAndWhite->Yearly + $pageCounts->Leased->BlackAndWhite->Yearly;
            $pageCounts->Total->Color                  = new stdClass();
            $pageCounts->Total->Color->Monthly         = $pageCounts->Purchased->Color->Monthly + $pageCounts->Leased->Color->Monthly;
            $pageCounts->Total->Color->Yearly          = $pageCounts->Purchased->Color->Yearly + $pageCounts->Leased->Color->Yearly;
            $pageCounts->Total->Combined               = new stdClass();
            $pageCounts->Total->Combined->Monthly      = $pageCounts->Purchased->Combined->Monthly + $pageCounts->Leased->Combined->Monthly;
            $pageCounts->Total->Combined->Yearly       = $pageCounts->Purchased->Combined->Yearly + $pageCounts->Leased->Combined->Yearly;
            $this->PageCounts                          = $pageCounts;
        }

        return $this->PageCounts;
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

            foreach ($this->getPurchasedDevices() as $deviceInstance)
            {
                $this->_dealerMonthlyCost += $deviceInstance->calculateMonthlyCost($costPerPageSetting);
            }
        }

        return $this->_dealerMonthlyCost;
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
            $totalMonthlyMonoPagesPrinted  = $this->getPageCounts()->Purchased->BlackAndWhite->Monthly;
            $totalMonthlyColorPagesPrinted = $this->getPageCounts()->Purchased->Color->Monthly;
            $colorCpp                      = 0;
            $monoCpp                       = 0;

            foreach ($this->getPurchasedDevices() as $deviceInstance)
            {
                $costPerPage = $deviceInstance->calculateCostPerPageWithReplacement($costPerPageSetting);
                $monoCpp += ($deviceInstance->getAverageMonthlyBlackAndWhitePageCount() / $totalMonthlyMonoPagesPrinted) * $costPerPage->monochromeCostPerPage;
                if ($totalMonthlyColorPagesPrinted > 0 && $deviceInstance->getMasterDevice()->isColor())
                {
                    $colorCpp += ($deviceInstance->getAverageMonthlyColorPageCount() / $totalMonthlyColorPagesPrinted) * $costPerPage->colorCostPerPage;
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

            foreach ($this->getPurchasedDevices() as $deviceInstance)
            {
                $this->_dealerMonthlyCostWithReplacements += $deviceInstance->calculateMonthlyCost($costPerPageSetting, $deviceInstance->getReplacementMasterDevice());
            }
        }

        return $this->_dealerMonthlyCostWithReplacements;
    }

}
