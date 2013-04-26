<?php
class Healthcheck_ViewModel_Healthcheck extends Healthcheck_ViewModel_Abstract
{
    /**
     * All devices that have ages older or equal to this are in the old device list report
     */
    const OLD_DEVICE_LIST = 5;
    /**
     * All devices printing less than this are considered underutilized.
     */
    const UNDERUTILIZED_THRESHHOLD_PERCENTAGE = 0.05;
    /**
     * All devices that have ages older than this are considered old/
     */
    const OLD_DEVICE_THRESHHOLD = 10;

    const GALLONS_WATER_PER_PAGE = 0.121675; // Number of pages * this gives amount of gallons
    const TREE_PER_PAGE = 7800; //Number of pages divided by this, gives amount of trees
    public static $Proposal;

    // New Separated Proposal
    protected $Ranking;
    protected $ReportId;
    protected $DefaultToners;
    protected $Devices;
    protected $ExcludedDevices;
    protected $LeasedDevices;
    protected $PurchasedDevices;
    protected $User;
    protected $DealerCompany;
    protected $CompanyMargin;
    protected $ReportMargin;
    protected $MaximumMonthlyPrintVolume;
    protected $PageCounts;
    protected $Percentages;
    protected $YearlyBlackAndWhitePercentage;
    protected $YearlyColorPercentage;
    protected $YearlyLeasedBlackAndWhitePercentage;
    protected $YearlyLeasedColorPercentage;
    protected $YearlyPurchasedBlackAndWhitePercentage;
    protected $YearlyPurchasedColorPercentage;

    // Leased Section
    protected $CombinedAnnualLeasePayments;
    protected $PerPageLeaseCost;
    protected $LeasedBlackAndWhiteCharge;
    protected $LeasedColorCharge;
    protected $EstimatedAnnualCostOfLeaseMachines;
    protected $LeasedEstimatedBlackAndWhiteCPP;
    protected $LeasedEstimatedColorCPP;

    // Purchased Section
    protected $PurchasedEstimatedBlackAndWhiteCPP;
    protected $PurchasedEstimatedColorCPP;
    protected $AnnualCostOfHardwarePurchases;
    protected $CostOfInkAndToner;
    protected $CostOfInkAndTonerMonthly;

    // Summary
    // Other
    protected $NumberOfColorCapableDevices;
    protected $NumberOfBlackAndWhiteCapableDevices;
    protected $NumberOfScanCapableDevices;
    protected $NumberOfDuplexCapableDevices;
    protected $NumberOfFaxCapableDevices;
    protected $NumberOfUniqueModels;
    protected $NumberOfUniquePurchasedModels;
    protected $NumberOfUniqueToners;
    protected $CashHeldInInventory;
    protected $AverageCostOfDevices;
    protected $PercentDevicesUnderused;
    protected $PercentDevicesOverused;
    protected $LeastUsedDevices;
    protected $LeastUsedDeviceCount;
    protected $LeastUsedDevicePercentage;
    protected $MostUsedDevices;
    protected $MostUsedDeviceCount;
    protected $MostUsedDevicePercentage;
    protected $PercentColorDevices;
    protected $AverageAgeOfDevices;
    protected $HighPowerConsumptionDevices;
    protected $HighCostMonochromeDevices;
    protected $MostExpensiveDevices;
    protected $DateReportPrepared;
    protected $AveragePowerUsagePerMonth;
    protected $AveragePowerCostPerMonth;
    protected $AverageOperatingWatts;
    protected $AverageDeviceAge;
    protected $PercentageOfDevicesReportingPower;
    protected $NumberOfDevicesReportingPower;
    protected $GrossMarginTotalMonthlyCost;
    protected $GrossMarginTotalMonthlyRevenue;
    protected $DevicesReportingPowerThreshold;
    protected $NumberOfRepairs;
    protected $AverageTimeBetweenBreakdownAndFix;
    protected $AnnualDowntimeFromBreakdowns;
    protected $NumberOfAnnualInkTonerOrders;
    protected $NumberOfUniquePurchasedToners;
    protected $PercentPrintingDoneOnInkjet;
    protected $HighRiskDevices;
    protected $EstimatedAnnualSupplyRelatedExpense;
    protected $AnnualCostOfOutSourcing;
    protected $CostOfExecutingSuppliesOrders;
    protected $WeeklyITHours;
    protected $AnnualITHours;
    protected $AverageITRate;
    protected $AnnualITCost;
    protected $EstimatedAnnualCostOfService;
    protected $TotalPurchasedAnnualCost;
    protected $EstimatedAllInBlackAndWhiteCPP;
    protected $EstimatedAllInColorCPP;
    protected $MPSBlackAndWhiteCPP;
    protected $MPSColorCPP;
    protected $InternalAdminCost;
    protected $PrintIQTotalCost;
    protected $PrintIQSavings;
    protected $CostOfExecutingSuppliesOrder;
    protected $Graphs;
    // Device Replacement
    protected $DevicesToBeReplaced;
    protected $UniqueVendorCount;
    protected $NumberOfOrdersPerMonth;
    protected $EmployeeCount;
    protected $ReplacementDevices;
    protected $ReplacementDeviceCount;
    protected $LeftOverBlackAndWhitePageCount;
    protected $LeftOverColorPageCount;
    protected $LeftOverPrintIQCost;
    protected $LeftOverCostOfColorDevices;
    protected $LeftOverCostOfBlackAndWhiteDevices;
    protected $CostOfRemainingDevices;
    protected $CurrentCostOfReplacedColorPrinters;
    protected $CurrentCostOfReplacedBlackAndWhitePrinters;
    protected $ProposedCostOfReplacedBlackAndWhitePrinters;
    protected $ProposedCostOfReplacedColorPrinters;
    protected $CurrentCostOfReplacedColorMFPPrinters;
    protected $CurrentCostOfReplacedBlackAndWhiteMFPPrinters;
    protected $ProposedCostOfReplacedBlackAndWhiteMFPPrinters;
    protected $ProposedCostOfReplacedColorMFPPrinters;
    protected $TotalProposedAnnualCost;
    protected $TotalAnnualSavings;
    protected $GrossMarginMonthlyProfit;
    protected $GrossMarginOverallMargin;
    protected $GrossMarginWeightedCPP;
    protected $GrossMarginBlackAndWhiteMargin;
    protected $GrossMarginColorMargin;
    protected $UniqueTonerList;
    protected $UniquePurchasedTonerList;
    protected $UniqueDeviceList;
    protected $UniquePurchasedDeviceList;
    protected $_averageCompatibleOnlyCostPerPage;
    protected $_averageOemOnlyCostPerPage;
    protected $_numberOfDevicesReportingTonerLevels;
    protected $_numberOfColorCapablePurchasedDevices;
    protected $_maximumMonthlyPurchasedPrintVolume;
    protected $_purchasedTotalMonthlyCost;
    protected $_purchasedColorMonthlyCost;
    protected $_purchasedMonochromeMonthlyCost;
    protected $_optimizedDevices;
    protected $_numberOfDevicesNotReportingTonerLevels;
    protected $_numberOfCopyCapableDevices;
    protected $_includedDevicesSortedAscendingByAge;
    protected $_includedDevicesSortedDescendingByAge;

    public $highCostPurchasedDevices;

    /**
     * @param Healthcheck_Model_Healthcheck $report
     */
    public function __construct (Healthcheck_Model_Healthcheck $report)
    {
        parent::__construct($report);
        $this->DealerCompany = "Office Depot Inc.";

        if (isset(self::$Proposal))
        {
            self::$Proposal = $this;
        }

        // Get the report settings
        $healthcheckSettings = $this->healthcheck->getHealthcheckSettings();

        // Set Page Coverage
        Proposalgen_Model_Toner::setESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE($this->getPageCoverageBlackAndWhite() / 100);
        Proposalgen_Model_Toner::setESTIMATED_PAGE_COVERAGE_COLOR($this->getPageCoverageColor() / 100);

        // Gross Margin Report Page Coverage
        Proposalgen_Model_Toner::setACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE($healthcheckSettings->actualPageCoverageMono / 100);
        Proposalgen_Model_Toner::setACTUAL_PAGE_COVERAGE_COLOR($healthcheckSettings->actualPageCoverageColor / 100);

        Proposalgen_Model_DeviceInstance::$KWH_Cost = $healthcheckSettings->kilowattsPerHour;
        Proposalgen_Model_MasterDevice::setPricingConfig($healthcheckSettings->getAssessmentPricingConfig());
        $pricingConfig                  = new Proposalgen_Model_PricingConfig();
        $pricingConfig->pricingConfigId = Proposalgen_Model_PricingConfig::OEM;
        Proposalgen_Model_MasterDevice::setGrossMarginPricingConfig($pricingConfig);
        Proposalgen_Model_MasterDevice::setReportMargin(1 - ((((int)$healthcheckSettings->assessmentReportMargin)) / 100));

        Proposalgen_Model_DeviceInstance::$ITCostPerPage = (($this->getAnnualITCost() * 0.5 + $this->getAnnualCostOfOutSourcing()) / $this->getPageCounts()->Purchased->Combined->Yearly);
    }

    /**
     * @return Healthcheck_ViewModel_Ranking
     */
    public function getRanking ()
    {
        if (!isset($this->Ranking))
        {
            $this->Ranking = new Healthcheck_ViewModel_Ranking($this);
        }

        return $this->Ranking;
    }


    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getLeasedDevices ()
    {
        if (!isset($this->LeasedDevices))
        {
            $this->LeasedDevices = $this->getDevices()->leasedDeviceInstances;
        }

        return $this->LeasedDevices;
    }

    /**
     * @return float
     */
    public function getHealthcheckMargin ()
    {
        if (!isset($this->ReportMargin))
        {
            $this->ReportMargin = 1 - ((((float)$this->healthcheck->getHealthcheckSettings()->assessmentReportMargin)) / 100);
        }

        return $this->ReportMargin;
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
    public function getLeasedDeviceCount ()
    {
        return count($this->getLeasedDevices());
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getPurchasedDevices ()
    {
        if (!isset($this->PurchasedDevices))
        {
            $this->PurchasedDevices = $this->getDevices()->purchasedDeviceInstances;
        }

        return $this->PurchasedDevices;
    }

    /**
     * @return int
     */
    public function getPurchasedDeviceCount ()
    {
        return count($this->getPurchasedDevices());
    }

    /**
     * @return float
     */
    public function getCombinedAnnualLeasePayments ()
    {
        if (!isset($this->CombinedAnnualLeasePayments))
        {

            $this->CombinedAnnualLeasePayments = $this->healthcheck->getHealthcheckSettings()->monthlyLeasePayment * $this->getLeasedDeviceCount() * 12;
        }

        return $this->CombinedAnnualLeasePayments;
    }

    /**
     * @return float
     */
    public function getLeasedBlackAndWhiteCharge ()
    {
        if (!isset($this->LeasedBlackAndWhiteCharge))
        {
            $this->LeasedBlackAndWhiteCharge = $this->healthcheck->getHealthcheckSettings()->leasedBwCostPerPage;
        }

        return $this->LeasedBlackAndWhiteCharge;
    }

    /**
     * @return float
     */
    public function getLeasedColorCharge ()
    {
        if (!isset($this->LeasedColorCharge))
        {
            $this->LeasedColorCharge = $this->healthcheck->getHealthcheckSettings()->leasedColorCostPerPage;
        }

        return $this->LeasedColorCharge;
    }

    /**
     * @return float
     */
    public function getEstimatedAnnualCostOfLeaseMachines ()
    {
        if (!isset($this->EstimatedAnnualCostOfLeaseMachines))
        {
            $this->EstimatedAnnualCostOfLeaseMachines = $this->getCombinedAnnualLeasePayments() + ($this->getPageCounts()->Leased->BlackAndWhite->Yearly * $this->getLeasedBlackAndWhiteCharge()) + ($this->getPageCounts()->Leased->Color->Yearly * $this->getLeasedColorCharge());
        }

        return $this->EstimatedAnnualCostOfLeaseMachines;
    }

    /**
     * @return float
     */
    public function getAnnualCostOfHardwarePurchases ()
    {
        if (!isset($this->AnnualCostOfHardwarePurchases))
        {
            $totalAge = 0;

            foreach ($this->getPurchasedDevices() as $device)
            {
                $totalAge += $device->getAge();
            }

            if ($this->getPurchasedDeviceCount())
            {
                $averageAge                          = $totalAge / $this->getPurchasedDeviceCount();
                $this->AnnualCostOfHardwarePurchases = ($this->getDeviceCount() / $averageAge) * $this->healthcheck->getHealthcheckSettings()->defaultPrinterCost;
            }
            else
            {
                $this->AnnualCostOfHardwarePurchases = 0;
            }
        }

        return $this->AnnualCostOfHardwarePurchases;
    }


    /**
     * @return float
     */
    public function getCostOfInkAndTonerMonthly ($costPerPageSetting)
    {
        if (!isset($this->CostOfInkAndTonerMonthly))
        {
            // Calculate
            $totalCost = 0;
            foreach ($this->getPurchasedDevices() as $device)
            {
                $totalCost += $device->getCostOfInkAndToner($costPerPageSetting,$this->getHealthcheckMargin());
            }
            $this->CostOfInkAndTonerMonthly = $totalCost;
        }

        return $this->CostOfInkAndTonerMonthly;
    }

    /**
     * @return float
     */
    public function getCostOfInkAndToner ($costPerPageSetting)
    {
        if (!isset($this->CostOfInkAndToner))
        {
            $this->CostOfInkAndToner = $this->getCostOfInkAndTonerMonthly($costPerPageSetting) * 12;
        }

        return $this->CostOfInkAndToner;
    }

    /**
     * @return int
     */
    public function getNumberOfScanCapableDevices ()
    {
        if (!isset($this->NumberOfScanCapableDevices))
        {
            $numberOfDevices = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->getMasterDevice()->isScanner)
                {
                    $numberOfDevices++;
                }
            }
            $this->NumberOfScanCapableDevices = $numberOfDevices;
        }

        return $this->NumberOfScanCapableDevices;
    }

    /**
     * @return int
     */
    public function getNumberOfCopyCapableDevices ()
    {
        if (!isset($this->_numberOfCopyCapableDevices))
        {
            $numberOfDevices = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->getMasterDevice()->isCopier)
                {
                    $numberOfDevices++;
                }
            }
            $this->_numberOfCopyCapableDevices = $numberOfDevices;
        }

        return $this->_numberOfCopyCapableDevices;
    }

    /**
     * @return int
     */
    public function getNumberOfDuplexCapableDevices ()
    {
        if (!isset($this->NumberOfDuplexCapableDevices))
        {
            $numberOfDevices = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $device)
            {
                if ($device->getMasterDevice()->isDuplex)
                {
                    $numberOfDevices++;
                }
            }
            $this->NumberOfDuplexCapableDevices = $numberOfDevices;
        }

        return $this->NumberOfDuplexCapableDevices;
    }

    /**
     * @return int
     */
    public function getNumberOfFaxCapableDevices ()
    {
        if (!isset($this->NumberOfFaxCapableDevices))
        {
            $numberOfDevices = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->getMasterDevice()->isFax)
                {
                    $numberOfDevices++;
                }
            }
            $this->NumberOfFaxCapableDevices = $numberOfDevices;
        }

        return $this->NumberOfFaxCapableDevices;
    }

    /**
     * @return int
     */
    public function getNumberOfUniqueModels ()
    {
        if (!isset($this->NumberOfUniqueModels))
        {
            $this->NumberOfUniqueModels = count($this->getUniqueDeviceList());
        }

        return $this->NumberOfUniqueModels;
    }

    /**
     * @return float
     */
    public function getCashHeldInInventory ()
    {
        if (!isset($this->CashHeldInInventory))
        {
            $inventoryCash = 0.0;
            foreach ($this->getUniquePurchasedTonerList() as $toner)
            {
                $inventoryCash += $toner->cost;
            }
            $this->CashHeldInInventory = $inventoryCash * 2;
        }

        return $this->CashHeldInInventory;
    }


    /**
     * @return int
     */
    public function getMaximumMonthlyPrintVolume ()
    {
        if (!isset($this->MaximumMonthlyPrintVolume))
        {
            $maxVolume = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                $maxVolume += $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()->pricingConfiguration);
            }
            $this->MaximumMonthlyPrintVolume = $maxVolume;
        }

        return $this->MaximumMonthlyPrintVolume;
    }

    /**
     * @return int
     */
    public function getNumberOfColorCapableDevices ()
    {
        if (!isset($this->NumberOfColorCapableDevices))
        {
            $numberOfDevices = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $device)
            {
                if ($device->getMasterDevice()->tonerConfigId != Proposalgen_Model_TonerConfig::BLACK_ONLY)
                {
                    $numberOfDevices++;
                }
            }
            $this->NumberOfColorCapableDevices = $numberOfDevices;
        }

        return $this->NumberOfColorCapableDevices;
    }

    /**
     * @return int
     */
    public function getNumberOfColorCapablePurchasedDevices ()
    {
        if (!isset($this->_numberOfColorCapablePurchasedDevices))
        {
            $numberOfDevices = 0;
            foreach ($this->getDevices()->purchasedDeviceInstances as $device)
            {
                if ($device->getMasterDevice()->tonerConfigId != Proposalgen_Model_TonerConfig::BLACK_ONLY)
                {
                    $numberOfDevices++;
                }
            }
            $this->_numberOfColorCapablePurchasedDevices = $numberOfDevices;
        }

        return $this->_numberOfColorCapablePurchasedDevices;
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
     * @return float
     */
    public function getPercentDevicesUnderused ()
    {
        if (!isset($this->PercentDevicesUnderused))
        {
            $devicesUnderusedCount = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->getAverageMonthlyPageCount() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()->pricingConfiguration) * self::UNDERUTILIZED_THRESHHOLD_PERCENTAGE))
                {
                    $devicesUnderusedCount++;
                }
            }
            $this->PercentDevicesUnderused = ($devicesUnderusedCount / count($this->getDevices()->allIncludedDeviceInstances)) * 100;
        }

        return $this->PercentDevicesUnderused;
    }

    /**
     * @return float
     */
    public function getPercentDevicesOverused ()
    {
        if (!isset($this->PercentDevicesOverused))
        {
            $devicesOverusedCount = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->getAverageMonthlyPageCount() > $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()->pricingConfiguration))
                {
                    $devicesOverusedCount++;
                }
            }
            $this->PercentDevicesOverused = ($devicesOverusedCount / count($this->getDevices()->allIncludedDeviceInstances)) * 100;
        }

        return $this->PercentDevicesOverused;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getUnderutilizedDevices ()
    {
        if (!isset($this->_underutilizedDevices))
        {
            $devicesArray = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->getAverageMonthlyPageCount() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()->pricingConfiguration) * self::UNDERUTILIZED_THRESHHOLD_PERCENTAGE))
                {
                    $devicesArray[] = $deviceInstance;
                }
            }
            $this->_underutilizedDevices = $devicesArray;
        }

        return $this->_underutilizedDevices;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getOverutilizedDevices ()
    {
        if (!isset($this->_overutilizedDevices))
        {
            $devicesArray = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->getUsage($this->getCostPerPageSettingForCustomer()->pricingConfiguration) > 1)
                {
                    $devicesArray[] = $deviceInstance;
                }
            }
            $this->_overutilizedDevices = $devicesArray;
        }

        return $this->_overutilizedDevices;
    }


    /**
     * @return \Proposalgen_Model_DeviceInstance[]
     */
    public function getLeastUsedDevices ()
    {
        if (!isset($this->LeastUsedDevices))
        {
            $deviceArray = $this->getDevices()->allIncludedDeviceInstances;
            usort($deviceArray, array(
                                     $this,
                                     "ascendingSortDevicesByUsage"
                                ));
            // returning only the first 2
            $deviceArray            = array(
                $deviceArray [0],
                $deviceArray [1]
            );
            $this->LeastUsedDevices = $deviceArray;
        }

        return $this->LeastUsedDevices;
    }

    /**
     * Callback function for uSort when we want to sort a device based on usage
     *
     * @param $deviceA \Proposalgen_Model_DeviceInstance
     * @param $deviceB \Proposalgen_Model_DeviceInstance
     *
     * @return int
     */
    public function ascendingSortDevicesByUsage ($deviceA, $deviceB)
    {
        if ($deviceA->getUsage($this->getCostPerPageSettingForCustomer()->pricingConfiguration) == $deviceB->getUsage($this->getCostPerPageSettingForCustomer()->pricingConfiguration))
        {
            return 0;
        }

        return ($deviceA->getUsage($this->getCostPerPageSettingForCustomer()->pricingConfiguration) < $deviceB->getUsage($this->getCostPerPageSettingForCustomer()->pricingConfiguration)) ? -1 : 1;
    }

    /**
     * Callback function for uSort when we want to sort a device based on
     * monthly cost
     *
     * @param Proposalgen_Model_DeviceInstance $deviceA
     * @param Proposalgen_Model_DeviceInstance $deviceB
     *
     * @return int
     */
    public function ascendingSortDevicesByMonthlyCost ($deviceA, $deviceB)
    {
        if ($deviceA->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getHealthcheckMargin()) == $deviceB->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin()))
        {
            return 0;
        }

        return ($deviceA->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getHealthcheckMargin()) > $deviceB->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin())) ? -1 : 1;
    }

    /**
     * Callback function for uSort when we want to sort a device based on usage
     *
     * @param \Proposalgen_Model_DeviceInstance $deviceA
     * @param \Proposalgen_Model_DeviceInstance $deviceB
     *
     * @return int
     */
    public function descendingSortDevicesByUsage ($deviceA, $deviceB)
    {
        if ($deviceA->getUsage($this->getCostPerPageSettingForCustomer()->pricingConfiguration) == $deviceB->getUsage($this->getCostPerPageSettingForCustomer()->pricingConfiguration))
        {
            return 0;
        }

        return ($deviceA->getUsage($this->getCostPerPageSettingForCustomer()->pricingConfiguration) > $deviceB->getUsage($this->getCostPerPageSettingForCustomer()->pricingConfiguration)) ? -1 : 1;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getOptimizedDevices ()
    {
        if (!isset($this->_optimizedDevices))
        {
            $deviceArray = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {

                //Check to see if it is not underutilized
                if (($deviceInstance->getAverageMonthlyPageCount() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()->pricingConfiguration) * self::UNDERUTILIZED_THRESHHOLD_PERCENTAGE)) == false)
                {
                    //Check to see if it is not overUtilized
                    if ($deviceInstance->getUsage($this->getCostPerPageSettingForCustomer()->pricingConfiguration) < 1)
                    {

                        //Check to see if it is under the age requirements
                        if ($deviceInstance->getAge() < self::OLD_DEVICE_THRESHHOLD)
                        {
                            //Check to see if it is reporting toner levels
                            if ($deviceInstance->isCapableOfReportingTonerLevels() || $deviceInstance->getIsLeased())
                            {
                                /**
                                 * We are a fully optimized device!
                                 */
                                $deviceArray[] = $deviceInstance;
                            }

                        }
                    }
                }
            }
            $this->_optimizedDevices = $deviceArray;
        }

        return $this->_optimizedDevices;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getMostUsedDevices ()
    {
        if (!isset($this->MostUsedDevices))
        {
            $deviceArray = $this->getDevices()->allIncludedDeviceInstances;
            usort($deviceArray, array(
                                     $this,
                                     "descendingSortDevicesByUsage"
                                ));
            // returning only the first 2
            $deviceArray           = array(
                $deviceArray [0],
                $deviceArray [1]
            );
            $this->MostUsedDevices = $deviceArray;
        }

        return $this->MostUsedDevices;
    }

    /**
     * @return float
     */
    public function getAverageAgeOfDevices ()
    {
        if (!isset($this->AverageAgeOfDevices))
        {
            $totalAge = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                $totalAge += $deviceInstance->getAge();
            }
            $this->AverageAgeOfDevices = $totalAge / count($this->getDevices()->allIncludedDeviceInstances);
        }

        return $this->AverageAgeOfDevices;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getHighPowerConsumptionDevices ()
    {
        if (!isset($this->HighPowerConsumptionDevices))
        {
            $deviceArray = $this->getDevices()->allIncludedDeviceInstances;
            usort($deviceArray, array(
                                     $this,
                                     "ascendingSortDevicesByPowerConsumption"
                                ));
            $this->HighPowerConsumptionDevices = $deviceArray;
        }

        return $this->HighPowerConsumptionDevices;
    }

    /**
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getMonthlyHighCostPurchasedColorDevices (Proposalgen_Model_CostPerPageSetting $costPerPageSetting)
    {
        if (!isset($this->HighCostDevices))
        {
            $deviceArray = $this->getDevices()->purchasedDeviceInstances;
            $costArray   = array();
            /**@var $value Proposalgen_Model_DeviceInstance */
            foreach ($deviceArray as $key => $deviceInstance)
            {
                if ($deviceInstance->getMasterDevice()->isColor())
                {
                    $costArray[] = array($key, $deviceInstance->getAverageMonthlyColorPageCount() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->colorCostPerPage);
                }
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
            $this->HighCostDevices = $highCostDevices;
        }

        return $this->HighCostDevices;
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
    public function ascendingSortDevicesByPowerConsumption ($deviceA, $deviceB)
    {
        if ($deviceA->getAverageDailyPowerConsumption() == $deviceB->getAverageDailyPowerConsumption())
        {
            return 0;
        }

        return ($deviceA->getAverageDailyPowerConsumption() > $deviceB->getAverageDailyPowerConsumption()) ? -1 : 1;
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
     * @return string
     */
    public function getDateReportPrepared ()
    {
        if (!isset($this->DateReportPrepared))
        {
            $report_date              = new DateTime($this->healthcheck->reportDate);
            $this->DateReportPrepared = date_format($report_date, 'F jS, Y');
        }

        return $this->DateReportPrepared;
    }

    /**
     * Calculates the average cost per page for only toners that are Oem.
     *
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateAverageOemOnlyCostPerPage ()
    {
        if (!isset($this->_averageOemOnlyCostPerPage))
        {
            $costPerPageSetting                       = clone $this->getCostPerPageSettingForCustomer();
            $costPerPageSetting->pricingConfiguration = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find(Proposalgen_Model_PricingConfig::OEM);
            $costPerPage                              = new Proposalgen_Model_CostPerPage();
            $costPerPage->monochromeCostPerPage       = 0;
            $costPerPage->colorCostPerPage            = 0;
            $numberOfColorDevices                     = 0;
            foreach ($this->getDevices()->purchasedDeviceInstances as $deviceInstance)
            {
                $costPerPage->add($deviceInstance->getMasterDevice()->calculateCostPerPage($costPerPageSetting));
                if ($deviceInstance->getMasterDevice()->isColor())
                {
                    $numberOfColorDevices++;
                }
            }
            $numberOfDevices = count($this->getDevices()->purchasedDeviceInstances);
            if ($numberOfDevices > 0)
            {
                $costPerPage->monochromeCostPerPage = $costPerPage->monochromeCostPerPage / $numberOfDevices;
                if ($numberOfColorDevices > 0)
                {
                    $costPerPage->colorCostPerPage = $costPerPage->colorCostPerPage / $numberOfColorDevices;
                }
            }
            $costPerPage->monochromeCostPerPage = Tangent_Accounting::applyMargin($costPerPage->monochromeCostPerPage, $this->healthcheck->getHealthcheckSettings()->assessmentReportMargin);
            $costPerPage->colorCostPerPage      = Tangent_Accounting::applyMargin($costPerPage->colorCostPerPage, $this->healthcheck->getHealthcheckSettings()->assessmentReportMargin);
            $this->_averageOemOnlyCostPerPage   = $costPerPage;
        }

        return $this->_averageOemOnlyCostPerPage;
    }

    /**
     * Calculates the average cost per page for only toners that are Comp.
     *
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateAverageCompatibleOnlyCostPerPage ()
    {
        if (!isset($this->_averageCompatibleOnlyCostPerPage))
        {
            $costPerPageSetting                       = clone $this->getCostPerPageSettingForCustomer();
            $costPerPageSetting->pricingConfiguration = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find(Proposalgen_Model_PricingConfig::COMP);
            $costPerPage                              = new Proposalgen_Model_CostPerPage();
            $numberOfColorDevices                     = 0;
            foreach ($this->getDevices()->purchasedDeviceInstances as $deviceInstance)
            {
                $costPerPage->add($deviceInstance->getMasterDevice()->calculateCostPerPage($costPerPageSetting));
                if ($deviceInstance->getMasterDevice()->isColor())
                {
                    $numberOfColorDevices++;
                }
            }
            $numberOfDevices = count($this->getDevices()->purchasedDeviceInstances);
            if ($numberOfDevices > 0)
            {
                $costPerPage->monochromeCostPerPage = $costPerPage->monochromeCostPerPage / $numberOfDevices;
                if ($numberOfColorDevices > 0)
                {
                    $costPerPage->colorCostPerPage = $costPerPage->colorCostPerPage / $numberOfDevices;
                }
            }
            $costPerPage->monochromeCostPerPage      = Tangent_Accounting::applyMargin($costPerPage->monochromeCostPerPage, $this->healthcheck->getHealthcheckSettings()->assessmentReportMargin);
            $costPerPage->colorCostPerPage           = Tangent_Accounting::applyMargin($costPerPage->colorCostPerPage, $this->healthcheck->getHealthcheckSettings()->assessmentReportMargin);
            $this->_averageCompatibleOnlyCostPerPage = $costPerPage;
        }

        return $this->_averageCompatibleOnlyCostPerPage;
    }

    /**
     * @return float
     */
    public function getAveragePowerUsagePerMonth ()
    {
        if (!isset($this->AveragePowerUsagePerMonth))
        {
            $totalPowerUsage       = 0;
            $devicesReportingPower = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->getMasterDevice()->wattsPowerNormal > 0)
                {
                    $totalPowerUsage += $deviceInstance->getAverageMonthlyPowerConsumption();
                    $devicesReportingPower++;
                }
            }
            if ($devicesReportingPower < 1)
            {
                $totalPowerUsage = 0;
            }
            else
            {
                $totalPowerUsage = ($totalPowerUsage / $devicesReportingPower) * $this->getDeviceCount();
            }
            $this->NumberOfDevicesReportingPower = $devicesReportingPower;
            $this->AveragePowerUsagePerMonth     = $totalPowerUsage;
        }

        return $this->AveragePowerUsagePerMonth;
    }

    /**
     * @return float
     */
    public function getAveragePowerCostPerMonth ()
    {
        if (!isset($this->AveragePowerCostPerMonth))
        {
            $this->AveragePowerCostPerMonth = $this->getAveragePowerUsagePerMonth() * Proposalgen_Model_DeviceInstance::getKWH_Cost();

        }

        return $this->AveragePowerCostPerMonth;
    }

    /**
     * @return int
     */
    public function getLeastUsedDeviceCount ()
    {
        if (!isset($this->LeastUsedDeviceCount))
        {
            $this->LeastUsedDeviceCount = count($this->getLeastUsedDevices());
        }

        return $this->LeastUsedDeviceCount;
    }

    /**
     * @return int
     */
    public function getMostUsedDeviceCount ()
    {
        if (!isset($this->MostUsedDeviceCount))
        {
            $this->MostUsedDeviceCount = count($this->getMostUsedDevices());
        }

        return $this->MostUsedDeviceCount;
    }

    /**
     * @return float
     */
    public function getAverageDeviceAge ()
    {
        if (!isset($this->AverageDeviceAge))
        {
            $averageAge    = 0;
            $cumulativeAge = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                $cumulativeAge += $deviceInstance->getAge();
            }
            if ($cumulativeAge > 0)
            {
                $averageAge = $cumulativeAge / $this->getDeviceCount();
            }
            $this->AverageDeviceAge = $averageAge;
        }

        return $this->AverageDeviceAge;
    }

    /**
     * @return int
     */
    public function getNumberOfUniquePurchasedModels ()
    {
        if (!isset($this->NumberOfUniquePurchasedModels))
        {
            $numberOfModels   = 0;
            $uniqueModelArray = array();
            foreach ($this->getPurchasedDevices() as $device)
            {
                if (!in_array($device->getMasterDevice()->modelName, $uniqueModelArray))
                {
                    $numberOfModels++;
                    $uniqueModelArray [] = $device->getMasterDevice()->modelName;
                }
            }
            $this->NumberOfUniquePurchasedModels = $numberOfModels;
        }

        return $this->NumberOfUniquePurchasedModels;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getDevicesReportingTonerLevels ()
    {
        $devicesReportingTonerLevels = array();
        foreach ($this->getPurchasedDevices() as $device)
        {
            if ($device->isCapableOfReportingTonerLevels())
            {
                $devicesReportingTonerLevels[] = $device;
            }
        }

        return $devicesReportingTonerLevels;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getDevicesNotReportingTonerLevels ()
    {
        $devicesNotReportingTonerLevels = array();
        foreach ($this->getDevices()->purchasedDeviceInstances as $device)
        {
            if ($device->isCapableOfReportingTonerLevels() == false)
            {
                $devicesNotReportingTonerLevels[] = $device;
            }
        }

        return $devicesNotReportingTonerLevels;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getNumberOfDevicesReportingTonerLevels ()
    {
        if (!isset($this->_numberOfDevicesReportingTonerLevels))
        {
            $this->_numberOfDevicesReportingTonerLevels = count($this->getDevicesReportingTonerLevels());
        }

        return $this->_numberOfDevicesReportingTonerLevels;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getNumberOfDevicesNotReportingTonerLevels ()
    {
        if (!isset($this->_numberOfDevicesNotReportingTonerLevels))
        {
            $this->_numberOfDevicesNotReportingTonerLevels = count($this->getDevicesNotReportingTonerLevels());
        }

        return $this->_numberOfDevicesNotReportingTonerLevels;
    }

    /**
     * @return float
     */
    public function getPercentageOfDevicesReportingPower ()
    {
        if (!isset($this->PercentageOfDevicesReportingPower))
        {
            $this->PercentageOfDevicesReportingPower = $this->getNumberOfDevicesReportingPower() / $this->getDeviceCount();
        }

        return $this->PercentageOfDevicesReportingPower;
    }

    /**
     * @return mixed
     */
    public function getNumberOfDevicesReportingPower ()
    {
        // FIXME: This obviously doesn't do what it's supposed to.
        if (!isset($this->NumberOfDevicesReportingPower))
        {
            $this->getAveragePowerUsagePerMonth();
        }

        return $this->NumberOfDevicesReportingPower;
    }

    /**
     * @return int
     */
    public function getNumberOfUniquePurchasedToners ()
    {
        if (!isset($this->NumberOfUniquePurchasedToners))
        {
            $this->NumberOfUniquePurchasedToners = count($this->getUniquePurchasedTonerList());
        }

        return $this->NumberOfUniquePurchasedToners;
    }

    /**
     * Callback function for uSort when we want to sort devices based on age
     *
     * @param Proposalgen_Model_DeviceInstance $deviceA
     * @param Proposalgen_Model_DeviceInstance $deviceB
     *
     * @return int
     */
    public function sortDevicesByAge ($deviceA, $deviceB)
    {
        if ($deviceA->getAge() == $deviceB->getAge())
        {
            return 0;
        }

        return ($deviceA->getAge() < $deviceB->getAge()) ? -1 : 1;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getOldDevices ()
    {
        if (!isset($this->_oldDevices))
        {
            $devices = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances as $device)
            {
                if ($device->getAge() > self::OLD_DEVICE_THRESHHOLD)
                {
                    $devices[] = $device;
                }
            }
            usort($devices, array(
                                 $this,
                                 "sortDevicesByAge"
                            ));
            $this->_oldDevices = $devices;
        }

        return $this->_oldDevices;
    }

    /**
     * @return float
     */
    public function getWeeklyITHours ()
    {
        if (!isset($this->WeeklyITHours))
        {
            $this->WeeklyITHours = $this->healthcheck->getHealthcheckSettings()->hoursSpentOnIt;
            if (!$this->WeeklyITHours)
            {
                $this->WeeklyITHours = $this->getDeviceCount() * 0.25;
            }
        }

        return $this->WeeklyITHours;
    }

    /**
     * @return float
     */
    public function getAnnualITHours ()
    {
        if (!isset($this->AnnualITHours))
        {
            $this->AnnualITHours = $this->getWeeklyITHours() * 52;
        }

        return $this->AnnualITHours;
    }

    /**
     * @return float
     */
    public function getAverageITRate ()
    {
        if (!isset($this->AverageITRate))
        {
            $this->AverageITRate = $this->healthcheck->getHealthcheckSettings()->averageItHourlyRate;
        }

        return $this->AverageITRate;
    }

    /**
     * @return float
     */
    public function getAnnualITCost ()
    {
        if (!isset($this->AnnualITCost))
        {
            $this->AnnualITCost = $this->getAverageITRate() * $this->getAnnualITHours();
        }

        return $this->AnnualITCost;
    }

    /**
     * @return array
     */
    protected function _getOldGraphs ()
    {
        if (!isset($this->Graphs))
        {
            // Variables that could be settings
            $OD_AverageMonthlyPagesPerEmployee = 200;
            $OD_AverageMonthlyPages            = 4200;
            $OD_AverageEmployeesPerDevice      = 4.4;

            // Other variables used in several places
            $pageCounts    = $this->getPageCounts();
            $companyName   = $this->healthcheck->getClient()->companyName;
            $employeeCount = $this->healthcheck->getClient()->employeeCount;

            // Formatting variables
            $numberValueMarker = "N *sz0";
            //Graphs[2]
            $this->Graphs [] = "FILLER";

            /**
             * -- LeasedVsPurchasedBarGraph
             */
            $highest  = ($this->getLeasedDeviceCount() > $this->getPurchasedDeviceCount()) ? $this->getLeasedDeviceCount() : $this->getPurchasedDeviceCount();
            $barGraph = new gchart\gBarChart(225, 265);
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $this->getLeasedDeviceCount()
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $this->getPurchasedDeviceCount()
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(50, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      "Number of leased devices",
                                      "Number of purchased devices"
                                 ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[1]
            $this->Graphs [] = $barGraph->getUrl();

            /**
             * -- LeasedVsPurchasedPageCountBarGraph
             */
            $highest  = ($pageCounts->Leased->Combined->Monthly > $pageCounts->Purchased->Combined->Monthly) ? $pageCounts->Leased->Combined->Monthly : $pageCounts->Purchased->Combined->Monthly;
            $barGraph = new gchart\gBarChart(225, 265);

            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       round($pageCounts->Leased->Combined->Monthly)
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       round($pageCounts->Purchased->Combined->Monthly)
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.20);
            $barGraph->setDataRange(0, $highest * 1.20);
            $barGraph->setBarScale(50, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      "Monthly pages on leased devices",
                                      "Monthly pages on purchased devices"
                                 ));

            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[2]
            $this->Graphs [] = $barGraph->getUrl();

            /**
             * -- UniqueDevicesGraph
             */
            $uniqueModelArray = array();
            foreach ($this->getPurchasedDevices() as $device)
            {
                if (array_key_exists($device->getMasterDevice()->modelName, $uniqueModelArray))
                {
                    $uniqueModelArray [$device->getMasterDevice()->modelName] += 1;
                }
                else
                {
//                    $labels[$device->getMasterDevice()->modelName]            = $device->getMasterDevice()->modelName;
                    $uniqueModelArray [$device->getMasterDevice()->modelName] = 1;
                }
            }
            $uniqueDevicesGraph = new gchart\gPie3DChart(700, 270);
            $uniqueDevicesGraph->addDataSet($uniqueModelArray);
            $uniqueDevicesGraph->addColors(array(
                                                "E21736",
                                                "b0bb21",
                                                "5c3f9b",
                                                "0191d3",
                                                "f89428",
                                                "e4858f",
                                                "fcc223",
                                                "B3C6FF",
                                                "ECFFB3",
                                                "386AFF",
                                                "FFB3EC",
                                                "cccccc",
                                                "00ff00",
                                                "000000"
                                           ));
//             $uniqueDevicesGraph->setLegend($labels);
//            $uniqueDevicesGraph->setLabels($labels);
            // Graphs[3]
            $this->Graphs [] = $uniqueDevicesGraph->getUrl();

            /**
             * -- AverageMonthlyPagesBarGraph
             */
            $averagePageCount = round($pageCounts->Total->Combined->Monthly / $this->getDeviceCount(), 0);
            $highest          = ($averagePageCount > $OD_AverageMonthlyPages) ? $averagePageCount : $OD_AverageMonthlyPages;
            $barGraph         = new gchart\gBarChart(175, 300);
            $barGraph->setTitle("Average monthly pages|per networked printer");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $averagePageCount
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $OD_AverageMonthlyPages
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      $companyName,
                                      "Average"
                                 ));

            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[4]
            $this->Graphs [] = $barGraph->getUrl();

            /**
             * -- AverageMonthlyPagesPerEmployeeBarGraph
             */
            $pagesPerEmployee = round($pageCounts->Total->Combined->Monthly / $employeeCount);
            $highest          = ($OD_AverageMonthlyPagesPerEmployee > $pagesPerEmployee) ? $OD_AverageMonthlyPagesPerEmployee : $pagesPerEmployee;
            $barGraph         = new gchart\gBarChart(175, 300);
            $barGraph->setTitle("Average monthly pages|per employee");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $pagesPerEmployee
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $OD_AverageMonthlyPagesPerEmployee
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      $companyName,
                                      "Average"
                                 ));
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[5]
            $this->Graphs [] = $barGraph->getUrl();

            /**
             * -- AverageMonthlyPagesPerEmployeeBarGraph
             */
            $devicesPerEmployee = round($employeeCount / $this->getDeviceCount(), 2);
            $highest            = ($devicesPerEmployee > $OD_AverageEmployeesPerDevice) ? $devicesPerEmployee : $OD_AverageEmployeesPerDevice;
            $barGraph           = new gchart\gBarChart(175, 300);
            $barGraph->setTitle("Employees per|printing device");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $devicesPerEmployee
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $OD_AverageEmployeesPerDevice
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      $companyName,
                                      "Average"
                                 ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[6]
            $this->Graphs [] = $barGraph->getUrl();

            // Graphs[7]
            $this->Graphs [] = "FILLER";

            // Graphs[8]
            $this->Graphs [] = "FILLER";

            // Graphs[9]
            $this->Graphs [] = "FILLER";

            // Graphs[10]
            $this->Graphs [] = "FILLER";

            // Graphs[11]
            $this->Graphs [] = "FILLER";

            // Graphs[12]
            $this->Graphs [] = "FILLER";

            /**
             * -- DuplexCapableDevicesGraph
             */
            $duplexPercentage = 0;
            if ($this->getDeviceCount())
            {
                $duplexPercentage = round((($this->getNumberOfDuplexCapableDevices() / $this->getDeviceCount()) * 100), 2);
            }

            $notDuplexPercentage = 100 - $duplexPercentage;
            $duplexCapableGraph  = new gchart\gPie3DChart(305, 210);
            $duplexCapableGraph->setTitle("Duplex-Capable Printing Devices");
            $duplexCapableGraph->addDataSet(array(
                                                 $duplexPercentage,
                                                 $notDuplexPercentage
                                            ));
            $duplexCapableGraph->setLegend(array(
                                                "Duplex capable",
                                                "Not duplex capable"
                                           ));
            $duplexCapableGraph->setLabels(array(
                                                "$duplexPercentage%"
                                           ));
            $duplexCapableGraph->addColors(array(
                                                "E21736",
                                                "0194D2"
                                           ));
            $duplexCapableGraph->setLegendPosition("bv");
            // Graphs[13]
            $this->Graphs [] = $duplexCapableGraph->getUrl();

            /**
             * -- ScanCapableDevicesGraph
             */
            if ($this->getDeviceCount())
            {
                $scanPercentage = round((($this->getNumberOfScanCapableDevices() / $this->getDeviceCount()) * 100), 2);
            }
            else
            {
                $scanPercentage = 0;
            }
            $notScanPercentage = 100 - $scanPercentage;
            $scanCapableGraph  = new gchart\gPie3DChart(200, 160);
            $scanCapableGraph->setTitle("Scan-Capable Printing Devices");
            $scanCapableGraph->addDataSet(array(
                                               $scanPercentage,
                                               $notScanPercentage
                                          ));
            $scanCapableGraph->setLegend(array(
                                              "Scan capable",
                                              "Not scan capable"
                                         ));
            $scanCapableGraph->setLabels(array(
                                              "$scanPercentage%"
                                         ));
            $scanCapableGraph->addColors(array(
                                              "E21736",
                                              "0194D2"
                                         ));
            $scanCapableGraph->setLegendPosition("bv");
            $scanCapableGraph->setDimensions(305, 210);
            // Graphs[14]
            $this->Graphs [] = $scanCapableGraph->getUrl();


        }

        return $this->Graphs;
    }

    /**
     * @param array $Graphs
     *
     * @return Proposalgen_Model_Proposal_OfficeDepot
     */
    public function setGraphs ($Graphs)
    {
        $this->Graphs = $Graphs;

        return $this;
    }

    /**
     * @return float
     */
    public function getCostOfExecutingSuppliesOrders ()
    {
        if (!isset($this->CostOfExecutingSuppliesOrders))
        {
            $this->CostOfExecutingSuppliesOrders = $this->healthcheck->getHealthcheckSettings()->costToExecuteSuppliesOrder * $this->healthcheck->getHealthcheckSettings()->numberOfSupplyOrdersPerMonth * 12;
        }

        return $this->CostOfExecutingSuppliesOrders;
    }

    /**
     * @return float
     */
    public function getEstimatedAnnualSupplyRelatedExpense ()
    {
        if (!isset($this->EstimatedAnnualSupplyRelatedExpense))
        {
            $this->EstimatedAnnualSupplyRelatedExpense = $this->getCostOfInkAndToner($this->getCostPerPageSettingForCustomer()) + $this->getCostOfExecutingSuppliesOrders();
        }

        return $this->EstimatedAnnualSupplyRelatedExpense;
    }

    /**
     * @return float
     */
    public function getAnnualCostOfOutSourcing ()
    {
        if (!isset($this->AnnualCostOfOutSourcing))
        {
            $this->AnnualCostOfOutSourcing = $this->healthcheck->getHealthcheckSettings()->costOfLabor;
            if ($this->AnnualCostOfOutSourcing === null)
            {
                $this->AnnualCostOfOutSourcing = $this->getPurchasedDeviceCount() * 200;
            }
        }

        return $this->AnnualCostOfOutSourcing;
    }

    /**
     * @return float
     */
    public function getEstimatedAnnualCostOfService ()
    {
        if (!isset($this->EstimatedAnnualCostOfService))
        {
            $this->EstimatedAnnualCostOfService = $this->getAnnualCostOfOutSourcing() + $this->getAnnualITCost();
        }

        return $this->EstimatedAnnualCostOfService;
    }

    /**
     * @return float
     */
    public function getTotalPurchasedAnnualCost ()
    {
        if (!isset($this->TotalPurchasedAnnualCost))
        {
            $this->TotalPurchasedAnnualCost = $this->getEstimatedAnnualCostOfService() + $this->getAnnualCostOfHardwarePurchases() + $this->getEstimatedAnnualSupplyRelatedExpense();
        }

        return $this->TotalPurchasedAnnualCost;
    }

    /**
     * @return float
     */
    public function getEmployeeCount ()
    {
        if (!isset($this->EmployeeCount))
        {
            $this->EmployeeCount = $this->healthcheck->getClient()->employeeCount;
        }

        return $this->EmployeeCount;
    }

    /**
     * @return float
     */
    public function getAverageOperatingWatts ()
    {
        if (!isset($this->AverageOperatingWatts))
        {
            $totalWatts = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                $totalWatts += $deviceInstance->getMasterDevice()->wattsPowerNormal;
            }
            $this->AverageOperatingWatts = ($totalWatts > 0) ? $totalWatts / $this->getDeviceCount() : 0;
        }

        return $this->AverageOperatingWatts;
    }


    /**
     * @return float
     */
    public function getDevicesReportingPowerThreshold ()
    {
        if (!isset($this->DevicesReportingPowerThreshold))
        {
            $this->DevicesReportingPowerThreshold = 0.25;
        }

        return $this->DevicesReportingPowerThreshold;
    }

    /**
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function getUniquePurchasedDeviceList ()
    {
        if (!isset($this->UniquePurchasedDeviceList))
        {
            $masterDevices = array();
            foreach ($this->getPurchasedDevices() as $device)
            {
                if (!in_array($device->getMasterDevice(), $masterDevices))
                {
                    $masterDevices [] = $device->getMasterDevice();
                }
            }
            $this->UniquePurchasedDeviceList = $masterDevices;
        }

        return $this->UniquePurchasedDeviceList;
    }

    /**
     * @return Proposalgen_Model_Toner[]
     */
    public function getUniquePurchasedTonerList ()
    {
        if (!isset($this->UniquePurchasedTonerList))
        {
            $uniqueToners = array();
            foreach ($this->getUniquePurchasedDeviceList() as $masterDevice)
            {
                $deviceToners = $masterDevice->getTonersForHealthcheck();
                foreach ($deviceToners as $toner)
                {
                    if (!in_array($toner, $uniqueToners))
                    {
                        $uniqueToners [] = $toner;
                    }
                }
            }
            $this->UniquePurchasedTonerList = $uniqueToners;
        }

        return $this->UniquePurchasedTonerList;
    }

    /**
     * Calculates half the difference between Oem Total Cost Annually And Compatible
     *
     * @return float
     */
    public function calculateHalfDifferenceBetweenOemTotalCostAnnuallyAndCompAnnually ()
    {
        return $this->calculateDifferenceBetweenOemTotalCostAnnuallyAndCompAnnually() / 2;
    }

    /**
     * Calculates the difference between Oem Total Cost Annually And Compatible
     *
     * @return float
     */
    public function calculateDifferenceBetweenOemTotalCostAnnuallyAndCompAnnually ()
    {
        return $this->calculateEstimatedOemTonerCostAnnually() - $this->calculateEstimatedCompTonerCostAnnually();
    }

    /**
     * @return float
     */
    public function calculateEstimatedCompTonerCostAnnually ()
    {
        return ($this->calculateAverageCompatibleOnlyCostPerPage()->colorCostPerPage * $this->getPageCounts()->Purchased->Color->Monthly + ($this->calculateAverageCompatibleOnlyCostPerPage()->monochromeCostPerPage * $this->getPageCounts()->Purchased->BlackAndWhite->Monthly)) * 12;
    }

    /**
     * Calculates The Estimated Oem Toner Cost Annually.
     *
     * @return float
     */
    public function calculateEstimatedOemTonerCostAnnually ()
    {
        return ($this->calculateAverageOemOnlyCostPerPage()->colorCostPerPage * $this->getPageCounts()->Purchased->Color->Monthly + ($this->calculateAverageOemOnlyCostPerPage()->monochromeCostPerPage * $this->getPageCounts()->Purchased->BlackAndWhite->Monthly)) * 12;
    }

    /**
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function getUniqueDeviceList ()
    {
        if (!isset($this->UniqueDeviceList))
        {
            $masterDevices = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if (!in_array($deviceInstance->getMasterDevice(), $masterDevices))
                {
                    $masterDevices [] = $deviceInstance->getMasterDevice();
                }
            }
            $this->UniqueDeviceList = $masterDevices;
        }

        return $this->UniqueDeviceList;
    }

    /**
     * @return float
     */
    public function calculateTotalMonthlyCost ()
    {
        return ($this->getEstimatedAnnualCostOfLeaseMachines() + $this->getTotalPurchasedAnnualCost()) / 12;
    }

    /**
     * Calculates the percentage of the fleet which is capable of reporting toner levels. This includes both purchased and leased devices.
     *
     * @return float
     */
    public function calculatePercentageOfFleetReportingTonerLevels ()
    {
        $percentage       = 0;
        $totalDeviceCount = count($this->getDevices()->allIncludedDeviceInstances);
        if ($totalDeviceCount > 0)
        {
            $deviceCount = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->isCapableOfReportingTonerLevels())
                {
                    $deviceCount++;
                }
            }

            $percentage = $deviceCount / $totalDeviceCount * 100;
        }

        return $percentage;
    }

    /**
     * Calculates a score between 0 and 100 based on the number of supplies. 100 is bad, 0 is perfect
     *
     * @return float
     */
    public function calculateNumberOfSupplyTypeScore ()
    {
        $maximumNumberOfSupplyTypes = 0;
        $minimumNumberOfSupplyTypes = 0;
        $score                      = 0;

        $hasMonoDevices               = false;
        $hasColorDevices              = false;
        $hasThreeColorCombinedDevices = false;
        $hasFourColorCombinedDevices  = false;


        foreach ($this->getDevices()->purchasedDeviceInstances as $deviceInstance)
        {
            switch ($deviceInstance->getMasterDevice()->tonerConfigId)
            {
                case Proposalgen_Model_TonerConfig::BLACK_ONLY:
                    $maximumNumberOfSupplyTypes += 1;
                    $hasMonoDevices = true;
                    break;
                case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED:
                    $maximumNumberOfSupplyTypes += 4;
                    $hasColorDevices = true;
                    break;
                case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED:
                    $maximumNumberOfSupplyTypes += 2;
                    $hasThreeColorCombinedDevices = true;
                    break;
                case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED:
                    $maximumNumberOfSupplyTypes += 1;
                    $hasFourColorCombinedDevices = true;
                    break;

            }
        }

        /**
         * Determine the maximum number of supply types
         */

        $minimumNumberOfSupplyTypes += ($hasMonoDevices) ? 1 : 0;
        $minimumNumberOfSupplyTypes += ($hasColorDevices) ? 4 : 0;
        $minimumNumberOfSupplyTypes += ($hasThreeColorCombinedDevices) ? 2 : 0;
        $minimumNumberOfSupplyTypes += ($hasFourColorCombinedDevices) ? 1 : 0;

        $currentNumberOfSupplyTypes = $this->getNumberOfUniquePurchasedToners();

        if ($minimumNumberOfSupplyTypes > 0 && $maximumNumberOfSupplyTypes > 0 && $currentNumberOfSupplyTypes > 0)
        {
            $score = ($currentNumberOfSupplyTypes - $minimumNumberOfSupplyTypes) / ($maximumNumberOfSupplyTypes - $minimumNumberOfSupplyTypes);
        }

        return $score * 100;
    }

    /**
     * Gets the average age of the purchased devices
     *
     * @return float
     */
    public function calculateAverageAgeOfPurchasedDevices ()
    {
        $averageAge = 0;

        $totalAge    = 0;
        $deviceCount = $this->getPurchasedDeviceCount();
        if ($deviceCount > 0)
        {
            foreach ($this->getDevices()->purchasedDeviceInstances as $deviceInstance)
            {
                $totalAge += $deviceInstance->getAge();
            }
            $averageAge = $totalAge / $deviceCount;
        }

        return $averageAge;
    }

    /**
     * Calculates the average pages per device monthly
     *
     * @return float
     */
    public function calculateAveragePagesPerDeviceMonthly ()
    {
        return $this->getPageCounts()->Total->Combined->Monthly / $this->getDeviceCount();
    }

    /**
     * Calculates the percent of total volume of purchased devices that are color
     *
     * @return float
     */
    public function calculatePercentOfTotalVolumePurchasedColorMonthly ()
    {
        return ($this->getPageCounts()->Purchased->Color->Monthly / $this->getPageCounts()->Purchased->Combined->Monthly) * 100;
    }

    /**
     * Calculates the total Average Cost For Oem Monochrome Printers Monthly
     *
     * @return float
     */
    public function calculateAverageTotalCostOemMonochromeMonthly ()
    {
        return $this->calculateAverageOemOnlyCostPerPage()->monochromeCostPerPage * $this->getPageCounts()->Purchased->BlackAndWhite->Monthly;
    }

    /**
     * Calculates the total Average Cost For Compatible Monochrome Printers Monthly
     *
     * @return float
     */
    public function calculateAverageTotalCostCompatibleMonochromeMonthly ()
    {
        return $this->calculateAverageCompatibleOnlyCostPerPage()->monochromeCostPerPage * $this->getPageCounts()->Purchased->BlackAndWhite->Monthly;
    }

    /**
     * Calculates the total Average Cost For Compatible Color Printers Monthly
     *
     * @return float
     */
    public function calculateAverageTotalCostOemColorMonthly ()
    {
        return $this->calculateAverageOemOnlyCostPerPage()->colorCostPerPage * $this->getPageCounts()->Purchased->Color->Monthly;
    }

    /**
     * Calculates the total Average Cost For Oem Color Printers Monthly
     *
     * @return float
     */
    public function calculateAverageTotalCostCompatibleColorMonthly ()
    {
        return $this->calculateAverageCompatibleOnlyCostPerPage()->colorCostPerPage * $this->getPageCounts()->Purchased->Color->Monthly;
    }

    /**
     * Calculates the total Average Cost For Oem Combined Printers Monthly
     *
     * @return float
     */
    public function calculateAverageTotalCostOemCombinedMonthly ()
    {
        return $this->calculateAverageTotalCostOemMonochromeMonthly() + $this->calculateAverageTotalCostOemColorMonthly();
    }

    /**
     * Calculates the total Average Cost For Compatible Combined Printers Monthly
     *
     * @return float
     */
    public function calculateAverageTotalCostCompatibleCombinedMonthly ()
    {
        return $this->calculateAverageTotalCostCompatibleMonochromeMonthly() + $this->calculateAverageTotalCostCompatibleColorMonthly();
    }

    public function getGraphs ()
    {

        if ($this->Graphs == null)
        {
            // Fetch the old graphs
            $this->_getOldGraphs();

            $healthcheckGraphs                 = array();
            $numberValueMarker                 = "N *sz0";
            $pageCounts                        = $this->getPageCounts();
            $companyName                       = $this->healthcheck->getClient()->companyName;
            $OD_AverageMonthlyPagesPerEmployee = 200;
            $OD_AverageMonthlyPages            = 4200;
            $OD_AverageEmployeesPerDevice      = 4.4;
            $employeeCount                     = $this->healthcheck->getClient()->employeeCount;
            /**
             * -- PagesPrinterATRPieGraph
             */
            $deviceAges = array(
                "Pages Printed on JIT devices"     => 0,
                "Pages Printed on non-JIT devices" => 0
            );
            foreach ($this->getPurchasedDevices() as $device)
            {
                if ($device->isCapableOfReportingTonerLevels())
                {
                    $deviceAges ["Pages Printed on JIT devices"] += $device->getAverageMonthlyPageCount();
                }
                else
                {
                    $deviceAges ["Pages Printed on non-JIT devices"] += $device->getAverageMonthlyPageCount();
                }
            }
            $dataSet     = array();
            $legendItems = array();
            $labels      = array();

            foreach ($deviceAges as $legendItem => $count)
            {
                if ($count > 0)
                {
                    $dataSet []     = $count;
                    $legendItems [] = $legendItem;
                    $percentage     = round(($count / $this->getPageCounts()->Purchased->Combined->Monthly) * 100, 2);
                    $labels []      = "$percentage%";
                }
            }
            $deviceAgeGraph = new gchart\gPie3DChart(350, 200);
            $deviceAgeGraph->addDataSet($dataSet);
            $deviceAgeGraph->setLegend($legendItems);
            $deviceAgeGraph->setLabels($labels);
            $deviceAgeGraph->addColors(array(
                                            "0094cf",
                                            "E21736"
                                       ));
            $deviceAgeGraph->setLegendPosition("bv");

            // PagesPrinterATRPieGraph
            $healthcheckGraphs['PagesPrinterATRPieGraph'] = $deviceAgeGraph->getUrl();

            /**
             * -- HardwareUtilizationCapacityBar
             */
            $highest  = ($this->getMaximumMonthlyPrintVolume() > $pageCounts->Total->Combined->Monthly) ? $this->getMaximumMonthlyPrintVolume() : $pageCounts->Total->Combined->Monthly;
            $barGraph = new gchart\gGroupedBarChart(600, 160);
            $barGraph->setHorizontal(true);
            $barGraph->setVisibleAxes(array(
                                           'x'
                                      ));
            $barGraph->addDataSet(array(
                                       $pageCounts->Total->Combined->Monthly
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $this->getMaximumMonthlyPrintVolume()
                                  ));
            $barGraph->setLegend(array(
                                      "Estimated Actual Monthly Usage",
                                      "Maximum Monthly Fleet Capacity"
                                 ));
            $barGraph->addAxisRange(0, 0, $highest * 1.3);
            $barGraph->setDataRange(0, $highest * 1.3);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("b");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // HardwareUtilizationCapacityBar
            $healthcheckGraphs['HardwareUtilizationCapacityBar'] = $barGraph->getUrl();


            /**
             * -- ColorCapablePrintingDevices
             */
            $highest  = ($this->getPurchasedDeviceCount() - $this->getNumberOfColorCapablePurchasedDevices() > $this->getNumberOfColorCapablePurchasedDevices()) ? ($this->getPurchasedDeviceCount() - $this->getNumberOfColorCapablePurchasedDevices()) : $this->getNumberOfColorCapablePurchasedDevices();
            $barGraph = new gchart\gBarChart(280, 210);
            $barGraph->setTitle("Color-Capable Printing Devices");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $this->getNumberOfColorCapablePurchasedDevices()
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $this->getPurchasedDeviceCount() - $this->getNumberOfColorCapablePurchasedDevices()
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(70, 10);
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      "Color-capable",
                                      "Black-and-white only"
                                 ));
            $barGraph->setLegendPosition("bv");
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // ColorCapablePrintingDevices
            $healthcheckGraphs['ColorCapablePrintingDevices'] = $barGraph->getUrl();


            /**
             * -- ColorVSBWPagesGraph
             */
            $blackAndWhitePageCount = $pageCounts->Purchased->Combined->Monthly - $pageCounts->Purchased->Color->Monthly;

            $highest  = ($pageCounts->Purchased->Color->Monthly > $blackAndWhitePageCount) ? $pageCounts->Purchased->Color->Monthly : $blackAndWhitePageCount;
            $barGraph = new gchart\gBarChart(280, 210);
            $barGraph->setTitle("Color vs Black/White Pages");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $pageCounts->Purchased->Color->Monthly
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $blackAndWhitePageCount
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(70, 10);
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      "Color pages printed",
                                      "Black-and-white pages printed"
                                 ));
            $barGraph->setLegendPosition("bv");
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // ColorVSBWPagesGraph
            $healthcheckGraphs['ColorVSBWPagesGraph'] = $barGraph->getUrl();

            /**
             * -- colorCapablePieChart
             */
            $colorPercentage = 0;
            if ($this->getDeviceCount())
            {
                $colorPercentage = round((($this->getNumberOfColorCapableDevices() / $this->getDeviceCount()) * 100), 2);
            }

            $notColorPercentage = 100 - $colorPercentage;
            $colorCapableGraph  = new gchart\gPie3DChart(210, 150);
            $colorCapableGraph->setTitle("Color-Capable Printing Devices");
            $colorCapableGraph->addDataSet(array(
                                                $colorPercentage,
                                                $notColorPercentage
                                           ));
            $colorCapableGraph->setLegend(array(
                                               "Color capable",
                                               "Black and white only"
                                          ));
            $colorCapableGraph->setLabels(array(
                                               "$colorPercentage%"
                                          ));
            $colorCapableGraph->addColors(array(
                                               "E21736",
                                               "0194D2"
                                          ));
            $colorCapableGraph->setLegendPosition("bv");
            // colorCapablePieChart
            $healthcheckGraphs['colorCapablePieChart'] = $colorCapableGraph->getUrl();

            /**
             * -- CompatibleATRBarGraph
             */
            $highest  = ($this->getNumberOfDevicesReportingTonerLevels() > $this->getNumberOfDevicesNotReportingTonerLevels() ? $this->getNumberOfDevicesReportingTonerLevels() : ($this->getNumberOfDevicesNotReportingTonerLevels()));
            $barGraph = new gchart\gBarChart(220, 220);
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $this->getNumberOfDevicesReportingTonerLevels()
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       ($this->getNumberOfDevicesNotReportingTonerLevels())
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(70, 10);
            $barGraph->setLegendPosition("b");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      "Compatible with JIT",
                                      "Not compatible with JIT"
                                 ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // CompatibleATRBarGraph
            $healthcheckGraphs['CompatibleATRBarGraph'] = $barGraph->getUrl();

            $oemCost  = ($this->calculateAverageOemOnlyCostPerPage()->colorCostPerPage * $this->getPageCounts()->Purchased->Color->Monthly + ($this->calculateAverageOemOnlyCostPerPage()->monochromeCostPerPage * $this->getPageCounts()->Purchased->BlackAndWhite->Monthly)) * 12;
            $compCost = ($this->calculateAverageCompatibleOnlyCostPerPage()->colorCostPerPage * $this->getPageCounts()->Purchased->Color->Monthly + ($this->calculateAverageCompatibleOnlyCostPerPage()->monochromeCostPerPage * $this->getPageCounts()->Purchased->BlackAndWhite->Monthly)) * 12;
            /**
             * -- DifferenceBarGraph
             */
            $highest  = ($oemCost > $compCost) ? $oemCost : $compCost;
            $barGraph = new gchart\gBarChart(280, 230);
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $oemCost
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $compCost
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(70, 10);
            $barGraph->setLegendPosition("b");
            $barGraph->setProperty('chxs', '0N*cUSD*');
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      "OEM Toner",
                                      "Compatible Toner"
                                 ));
            // DifferenceBarGraph
            $healthcheckGraphs['DifferenceBarGraph'] = $barGraph->getUrl();

            /**
             * -- HardwareUtilizationCapacityPercent
             */
            $percentage = ($pageCounts->Total->Combined->Monthly / $this->getMaximumMonthlyPrintVolume());
            $highest    = 100;
            $barGraph   = new gchart\gStackedBarChart(600, 160);
            $barGraph->setHorizontal(true);
            $barGraph->setVisibleAxes(array(
                                           'x'
                                      ));
            $barGraph->addDataSet(array(
                                       0
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       0
                                  ));
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->addDataSet(array(
                                       30
                                  ));
            $barGraph->addColors(array(
                                      "FFFFFF"
                                 ));
            $barGraph->addDataSet(array(
                                       0
                                  ));

            $barGraph->addDataSet(array(
                                       20
                                  ));
            $barGraph->addColors(array(
                                      "FFFFFF"
                                 ));
            $barGraph->setLegend(array(
                                      "Your Estimated Monthly Usage (% of Capacity)",
                                      "Optimal Monthly Fleet Usage Range"
                                 ));
            $barGraph->addAxisRange(0, 0, $highest);
            $barGraph->setDataRange(0, $highest);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            //Create a circle object, color E21736,0,height, position on axis, size, | means another statement in this string
            $dotProperties = '@d,E21736,0,.5:' . number_format($percentage, 2) . ',15|';
            //Add onto the last property, @t = a text message, color,0,height,positon - halfish of the text width, size
            $dotProperties .= '@t' . number_format($percentage * 100) . '%,000000,0,-2.0:' . number_format($percentage - .01, 2) . ',10';
            $barGraph->setProperty('chm', $dotProperties);
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setProperty('chxs', '0N*sz0*');
            // HardwareUtilizationCapacityPercent
            $healthcheckGraphs['HardwareUtilizationCapacityPercent'] = $barGraph->getUrl();

            /**
             * -- AgeBarGraph
             */
            $deviceAges = array(
                "Less than 3 years old" => 0,
                "3-5 years old"         => 0,
                "6-8 years old"         => 0,
                "More than 8 years old" => 0
            );
            foreach ($this->getDevices()->allIncludedDeviceInstances as $device)
            {
                if ($device->getAge() < 3)
                {
                    $deviceAges ["Less than 3 years old"]++;
                }
                else if ($device->getAge() <= 5)
                {
                    $deviceAges ["3-5 years old"]++;
                }
                else if ($device->getAge() <= 8)
                {
                    $deviceAges ["6-8 years old"]++;
                }
                else
                {
                    $deviceAges ["More than 8 years old"]++;
                }
            }
            $highest = $deviceAges ["Less than 3 years old"];
            if ($highest < $deviceAges["3-5 years old"])
            {
                $highest = $deviceAges["3-5 years old"];
            }
            if ($highest < $deviceAges["6-8 years old"])
            {
                $highest = $deviceAges["6-8 years old"];
            }
            if ($highest < $deviceAges["More than 8 years old"])
            {
                $highest = $deviceAges["More than 8 years old"];
            }
            $barGraph = new gchart\gBarChart(320, 230);
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $deviceAges ["Less than 3 years old"]
                                  ));
            $barGraph->addColors(array(
                                      "0094cf"
                                 ));
            $barGraph->addDataSet(array(
                                       $deviceAges ["3-5 years old"]
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $deviceAges ["6-8 years old"]
                                  ));
            $barGraph->addColors(array(
                                      "adba1d"
                                 ));
            $barGraph->addDataSet(array(
                                       $deviceAges ["More than 8 years old"]
                                  ));
            $barGraph->addColors(array(
                                      "5c3f9b"
                                 ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(70, 5);
            $barGraph->setLegendPosition("bv");

            $barGraph->setLegend(array(
                                      "Less than 3 years old",
                                      "3-5 years old",
                                      "6-8 years old",
                                      "More than 8 years old"
                                 ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            // AgeBarGraph
            $healthcheckGraphs['AgeBarGraph'] = $barGraph->getUrl();

            /**
             * -- DuplexCapableDevicesGraph
             */
            $duplexPercentage = 0;
            if ($this->getDeviceCount())
            {
                $duplexPercentage = round((($this->getNumberOfDuplexCapableDevices() / $this->getDeviceCount()) * 100), 2);
            }

            $notDuplexPercentage = 100 - $duplexPercentage;
            $duplexCapableGraph  = new gchart\gPie3DChart(210, 150);
            $duplexCapableGraph->setTitle("Duplex-Capable Printing Devices");
            $duplexCapableGraph->addDataSet(array(
                                                 $duplexPercentage,
                                                 $notDuplexPercentage
                                            ));
            $duplexCapableGraph->setLegend(array(
                                                "Duplex capable",
                                                "Not duplex capable"
                                           ));
            $duplexCapableGraph->setLabels(array(
                                                "$duplexPercentage%"
                                           ));
            $duplexCapableGraph->addColors(array(
                                                "E21736",
                                                "0194D2"
                                           ));
            $duplexCapableGraph->setLegendPosition("bv");
            // DuplexCapableDevicesGraph
            $healthcheckGraphs['DuplexCapableDevicesGraph'] = $duplexCapableGraph->getUrl();

            /**
             * -- AverageMonthlyPagesBarGraph
             */
            $averagePageCount = round($pageCounts->Total->Combined->Monthly / $this->getDeviceCount(), 0);
            $highest          = ($averagePageCount > $OD_AverageMonthlyPages) ? $averagePageCount : $OD_AverageMonthlyPages;
            $barGraph         = new gchart\gBarChart(165, 300);
            $barGraph->setTitle("Average monthly pages|per networked printer");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $averagePageCount
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $OD_AverageMonthlyPages
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      $companyName,
                                      "Average"
                                 ));

            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[4]   //AverageMonthlyPagesBarGraph
            $healthcheckGraphs['AverageMonthlyPagesBarGraph'] = $barGraph->getUrl();

            /**
             * -- AverageMonthlyPagesPerEmployeeBarGraph
             */
            $pagesPerEmployee = round($pageCounts->Total->Combined->Monthly / $employeeCount);
            $highest          = ($OD_AverageMonthlyPagesPerEmployee > $pagesPerEmployee) ? $OD_AverageMonthlyPagesPerEmployee : $pagesPerEmployee;
            $barGraph         = new gchart\gBarChart(165, 300);
            $barGraph->setTitle("Average monthly pages|per employee");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $pagesPerEmployee
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $OD_AverageMonthlyPagesPerEmployee
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      $companyName,
                                      "Average"
                                 ));
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[5] //AverageMonthlyPagesPerEmployeeBarGraph
            $healthcheckGraphs ['AverageMonthlyPagesPerEmployeeBarGraph'] = $barGraph->getUrl();

            /**
             * -- EmployeesPerDeviceBarGraph
             */
            $devicesPerEmployee = round($employeeCount / $this->getDeviceCount(), 2);
            $highest            = ($devicesPerEmployee > $OD_AverageEmployeesPerDevice) ? $devicesPerEmployee : $OD_AverageEmployeesPerDevice;
            $barGraph           = new gchart\gBarChart(165, 300);
            $barGraph->setTitle("Employees per|printing device");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $devicesPerEmployee
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $OD_AverageEmployeesPerDevice
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      $companyName,
                                      "Average"
                                 ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[6] //EmployeesPerDeviceBarGraph
            $healthcheckGraphs['EmployeesPerDeviceBarGraph'] = $barGraph->getUrl();

            /**
             * -- CopyCapableDevicesGraph
             */
            if ($this->getDeviceCount())
            {
                $copyPercentage = round((($this->getNumberOfCopyCapableDevices() / $this->getDeviceCount()) * 100), 2);
            }
            else
            {
                $copyPercentage = 0;
            }
            $notScanPercentage = 100 - $copyPercentage;
            $copyCapableGraph  = new gchart\gPie3DChart(210, 150);
            $copyCapableGraph->setTitle("Copy-Capable Printing Devices");
            $copyCapableGraph->addDataSet(array(
                                               $copyPercentage,
                                               $notScanPercentage
                                          ));
            $copyCapableGraph->setLegend(array(
                                              "Copy capable",
                                              "Not copy capable"
                                         ));
            $copyCapableGraph->setLabels(array(
                                              "$copyPercentage%"
                                         ));
            $copyCapableGraph->addColors(array(
                                              "E21736",
                                              "0194D2"
                                         ));
            $copyCapableGraph->setLegendPosition("bv");
            // Graphs CopyCapableDevicesGraph
            $healthcheckGraphs['CopyCapableDevicesGraph'] = $copyCapableGraph->getUrl();

            $this->Graphs = array_merge($healthcheckGraphs, $this->Graphs);
        }

        return $this->Graphs;
    }

    /**
     * Calculates the number of trees used.
     *
     * @return float
     */
    public function calculateNumberOfTreesUsed ()
    {
        return $this->getPageCounts()->Total->Combined->Yearly / self::TREE_PER_PAGE;
    }

    /**
     * Calculates 25% of the number of trees used.
     *
     * @return float
     */
    public function calculateQuarterOfNumberOfTreesUsed ()
    {
        return $this->calculateNumberOfTreesUsed() * .25;
    }

    /**
     * Calculates the number of Gallons of water used.
     *
     * @return float
     */
    public function calculateNumberOfGallonsWaterUsed ()
    {
        return $this->getPageCounts()->Total->Combined->Yearly * self::GALLONS_WATER_PER_PAGE;
    }

    /**
     * Calculates 25% of the number of trees used.
     *
     * @return float
     */
    public function calculateQuarterOfNumberOfGallonsWaterUsed ()
    {
        return $this->calculateNumberOfGallonsWaterUsed() * .25;
    }
}
