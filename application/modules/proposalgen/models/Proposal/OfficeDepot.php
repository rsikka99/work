<?php
class Proposalgen_Model_Proposal_OfficeDepot extends Proposalgen_Model_Proposal_Abstract
{
    /**
     * All devices printing less than this are considered underutilized.
     */
    const UNDERUTILIZED_THRESHHOLD_PERCENTAGE = 0.05;
    /**
     * All devices that have ages older than this are considered old/
     */
    const OLD_DEVICE_THRESHHOLD = 10;

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

    /**
     * @param Proposalgen_Model_Report $report
     */
    public function __construct (Proposalgen_Model_Report $report)
    {
        parent::__construct($report);
        $this->DealerCompany = "Office Depot Inc.";

        if (isset(self::$Proposal))
        {
            self::$Proposal = $this;
        }

        // Get the report settings
        $reportSettings = $this->report->getReportSettings();

        // Set Page Coverage
        Proposalgen_Model_Toner::setESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE($this->getPageCoverageBlackAndWhite() / 100);
        Proposalgen_Model_Toner::setESTIMATED_PAGE_COVERAGE_COLOR($this->getPageCoverageColor() / 100);

        // Gross Margin Report Page Coverage
        Proposalgen_Model_Toner::setACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE($reportSettings->actualPageCoverageMono / 100);
        Proposalgen_Model_Toner::setACTUAL_PAGE_COVERAGE_COLOR($reportSettings->actualPageCoverageColor / 100);

        Proposalgen_Model_DeviceInstance::$KWH_Cost = $reportSettings->kilowattsPerHour;
        Proposalgen_Model_MasterDevice::setPricingConfig($reportSettings->getAssessmentPricingConfig());
        Proposalgen_Model_MasterDevice::setGrossMarginPricingConfig($reportSettings->getGrossMarginPricingConfig());
        Proposalgen_Model_MasterDevice::setReportMargin(1 - ((((int)$reportSettings->assessmentReportMargin)) / 100));

        Proposalgen_Model_DeviceInstance::$ITCostPerPage = (($this->getAnnualITCost() * 0.5 + $this->getAnnualCostOfOutSourcing()) / $this->getPageCounts()->Purchased->Combined->Yearly);
    }

    /**
     * @return Proposalgen_Model_Proposal_Ranking
     */
    public function getRanking ()
    {
        if (!isset($this->Ranking))
        {
            $this->Ranking = new Proposalgen_Model_Proposal_Ranking($this);
        }

        return $this->Ranking;
    }

    /**
     * @return float
     */
    public function getYearlyBlackAndWhitePercentage ()
    {
        if (!isset($this->YearlyBlackAndWhitePercentage))
        {
            $percentage = 0.0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
            {
                $percentage = $this->getPageCounts()->Total->BlackAndWhite->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            }
            $this->YearlyBlackAndWhitePercentage = $percentage;
        }

        return $this->YearlyBlackAndWhitePercentage;
    }

    /**
     * @return float
     */
    public function getYearlyColorPercentage ()
    {
        if (!isset($this->YearlyColorPercentage))
        {
            $percentage = 0.0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
            {
                $percentage = $this->getPageCounts()->Total->Color->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            }
            $this->YearlyColorPercentage = $percentage;
        }

        return $this->YearlyColorPercentage;
    }

    /**
     * @return float
     */
    public function getYearlyPurchasedBlackAndWhitePercentage ()
    {
        if (!isset($this->YearlyPurchasedBlackAndWhitePercentage))
        {
            $percentage = 0.0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
            {
                $percentage = $this->getPageCounts()->Purchased->BlackAndWhite->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            }
            $this->YearlyPurchasedBlackAndWhitePercentage = $percentage;
        }

        return $this->YearlyPurchasedBlackAndWhitePercentage;
    }

    /**
     * @return float
     */
    public function getYearlyPurchasedColorPercentage ()
    {
        if (!isset($this->YearlyPurchasedColorPercentage))
        {
            $percentage = 0.0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
            {
                $percentage = $this->getPageCounts()->Purchased->Color->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            }
            $this->YearlyPurchasedColorPercentage = $percentage;
        }

        return $this->YearlyPurchasedColorPercentage;
    }

    /**
     * @return float
     */
    public function getYearlyLeasedBlackAndWhitePercentage ()
    {
        if (!isset($this->YearlyLeasedBlackAndWhitePercentage))
        {
            $percentage = 0.0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
            {
                $percentage = $this->getPageCounts()->Leased->BlackAndWhite->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            }
            $this->YearlyLeasedBlackAndWhitePercentage = $percentage;
        }

        return $this->YearlyLeasedBlackAndWhitePercentage;
    }

    /**
     * @return float
     */
    public function getYearlyLeasedColorPercentage ()
    {
        if (!isset($this->YearlyLeasedColorPercentage))
        {
            $percentage = 0.0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
            {
                $percentage = $this->getPageCounts()->Leased->Color->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            }
            $this->YearlyLeasedColorPercentage = $percentage;
        }

        return $this->YearlyLeasedColorPercentage;
    }

    /**
     * @return Proposalgen_Model_Rms_Excluded_Row[]
     */
    public function getExcludedDevices ()
    {
        if (!isset($this->ExcludedDevices))
        {
            $this->ExcludedDevices = array_merge($this->getDevices()->unmappedDeviceInstances, $this->getDevices()->excludedDeviceInstances);
        }

        return $this->ExcludedDevices;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getLeasedDevices ()
    {
        if (!isset($this->LeasedDevices))
        {
            $this->LeasedDevices = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->getMasterDevice()->isLeased)
                {
                    $this->LeasedDevices [] = $deviceInstance;
                }
            }
        }

        return $this->LeasedDevices;
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
            $this->PurchasedDevices = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->getMasterDevice()->isLeased === 0)
                {
                    $this->PurchasedDevices [] = $deviceInstance;
                }
            }
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
    public function getLeasedEstimatedBlackAndWhiteCPP ()
    {
        if (!isset($this->LeasedEstimatedBlackAndWhiteCPP))
        {
            $this->LeasedEstimatedBlackAndWhiteCPP = $this->getLeasedBlackAndWhiteCharge() + $this->getPerPageLeaseCost();
        }

        return $this->LeasedEstimatedBlackAndWhiteCPP;
    }

    /**
     * @return float
     */
    public function getLeasedEstimatedColorCPP ()
    {
        if (!isset($this->LeasedEstimatedColorCPP))
        {
            $this->LeasedEstimatedColorCPP = $this->getLeasedColorCharge() + $this->getPerPageLeaseCost();
        }

        return $this->LeasedEstimatedColorCPP;
    }

    /**
     * @return float
     */
    public function getPurchasedEstimatedBlackAndWhiteCPP ()
    {
        if (!isset($this->PurchasedEstimatedBlackAndWhiteCPP))
        {
            // FIXME: hard coding for now
            $this->PurchasedEstimatedBlackAndWhiteCPP = 0.05;
        }

        return $this->PurchasedEstimatedBlackAndWhiteCPP;
    }

    /**
     * @return float
     */
    public function getPurchasedEstimatedColorCPP ()
    {
        if (!isset($this->PurchasedEstimatedColorCPP))
        {
            // FIXME: hard coding for now
            $this->PurchasedEstimatedColorCPP = 0.08;
        }

        return $this->PurchasedEstimatedColorCPP;
    }

    /**
     * @return float
     */
    public function getCombinedAnnualLeasePayments ()
    {
        if (!isset($this->CombinedAnnualLeasePayments))
        {

            $this->CombinedAnnualLeasePayments = $this->report->getReportSettings()->monthlyLeasePayment * $this->getLeasedDeviceCount() * 12;
        }

        return $this->CombinedAnnualLeasePayments;
    }

    /**
     * @return float
     */
    public function getPerPageLeaseCost ()
    {
        if (!isset($this->PerPageLeaseCost))
        {
            if ($this->getPageCounts()->Leased->Combined->Yearly)
            {
                $this->PerPageLeaseCost = $this->getCombinedAnnualLeasePayments() / $this->getPageCounts()->Leased->Combined->Yearly;
            }
        }

        return $this->PerPageLeaseCost;
    }

    /**
     * @return Application_Model_User|null
     */
    public function getUser ()
    {
        if (!isset($this->User))
        {

            $this->User = null;
        }

        return $this->User;
    }

    /**
     * @param Application_Model_User $User
     *
     * @return Proposalgen_Model_Proposal_OfficeDepot
     */
    public function setUser ($User)
    {
        $this->User = $User;

        return $this;
    }

    /**
     * @deprecated
     *
     * @return null
     */
    public function getDealerCompany ()
    {
        if (!isset($this->DealerCompany))
        {

            $this->DealerCompany = null;
        }

        return $this->DealerCompany;
    }

    /**
     * @return float
     */
    public function getReportMargin ()
    {
        if (!isset($this->ReportMargin))
        {
            $this->ReportMargin = 1 - ((((int)$this->report->getReportSettings()->assessmentReportMargin)) / 100);
        }

        return $this->ReportMargin;
    }

    /**
     * @return float
     */
    public function getLeasedBlackAndWhiteCharge ()
    {
        if (!isset($this->LeasedBlackAndWhiteCharge))
        {
            $this->LeasedBlackAndWhiteCharge = $this->report->getReportSettings()->leasedBwCostPerPage;
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
            $this->LeasedColorCharge = $this->report->getReportSettings()->leasedColorCostPerPage;
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
            $this->EstimatedAnnualCostOfLeaseMachines = $this->getCombinedAnnualLeasePayments() + ($this->getLeasedBlackAndWhiteCharge() * $this->getPageCounts()->Leased->BlackAndWhite->Yearly) + ($this->getPageCounts()->Leased->Color->Yearly * $this->getLeasedColorCharge());
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
                $this->AnnualCostOfHardwarePurchases = ($this->getDeviceCount() / $averageAge) * $this->report->getReportSettings()->defaultPrinterCost;
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
    public function getCostOfInkAndTonerMonthly ()
    {
        if (!isset($this->CostOfInkAndTonerMonthly))
        {
            // Calculate
            $totalCost = 0;
            foreach ($this->getPurchasedDevices() as $device)
            {
                $totalCost += $device->getCostOfInkAndToner();
            }
            $this->CostOfInkAndTonerMonthly = $totalCost;
        }

        return $this->CostOfInkAndTonerMonthly;
    }

    /**
     * @return float
     */
    public function getCostOfInkAndToner ()
    {
        if (!isset($this->CostOfInkAndToner))
        {
            $this->CostOfInkAndToner = $this->getCostOfInkAndTonerMonthly() * 12;
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
     * @return int
     */
    public function getNumberOfUniqueToners ()
    {
        if (!isset($this->NumberOfUniqueToners))
        {
            $this->NumberOfUniqueToners = count($this->getUniqueTonerList());
        }

        return $this->NumberOfUniqueToners;
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
                $maxVolume += $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume();
            }
            $this->MaximumMonthlyPrintVolume = $maxVolume;
        }

        return $this->MaximumMonthlyPrintVolume;
    }

    /**
     * @return int
     */
    public function getMaximumMonthlyPurchasedPrintVolume ()
    {
        if (!isset($this->_maximumMonthlyPurchasedPrintVolume))
        {
            $maxVolume = 0;
            foreach ($this->getDevices()->purchasedDeviceInstances as $deviceInstance)
            {
                $maxVolume += $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume();
            }
            $this->_maximumMonthlyPurchasedPrintVolume = $maxVolume;
        }

        return $this->_maximumMonthlyPurchasedPrintVolume;
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
     * @return int
     */
    public function getNumberOfBlackAndWhiteCapableDevices ()
    {
        if (!isset($this->NumberOfBlackAndWhiteCapableDevices))
        {
            $this->NumberOfBlackAndWhiteCapableDevices = $this->getDeviceCount() - $this->getNumberOfColorCapableDevices();
        }

        return $this->NumberOfBlackAndWhiteCapableDevices;
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
            if ($this->getPageCounts()->Total->Combined->Monthly)
            {
                $Percentages->TotalColorPercentage = $this->getPageCounts()->Total->Color->Monthly / $this->getPageCounts()->Total->Combined->Monthly;
            }
            if ($this->getPageCounts()->Total->BlackAndWhite->Yearly)
            {
                $Percentages->PurchasedVsLeasedBlackAndWhite->Leased    = $this->getPageCounts()->Leased->BlackAndWhite->Yearly / $this->getPageCounts()->Total->BlackAndWhite->Yearly;
                $Percentages->PurchasedVsLeasedBlackAndWhite->Purchased = $this->getPageCounts()->Purchased->BlackAndWhite->Yearly / $this->getPageCounts()->Total->BlackAndWhite->Yearly;
            }
            if ($this->getPageCounts()->Total->Color->Yearly)
            {
                $Percentages->PurchasedVsLeasedColor->Leased    = $this->getPageCounts()->Leased->Color->Yearly / $this->getPageCounts()->Total->Color->Yearly;
                $Percentages->PurchasedVsLeasedColor->Purchased = $this->getPageCounts()->Purchased->Color->Yearly / $this->getPageCounts()->Total->Color->Yearly;
            }
            $this->Percentages = $Percentages;
        }

        return $this->Percentages;
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
    public function getAverageCostOfDevices ()
    {
        if (!isset($this->AverageCostOfDevices))
        {
            $this->AverageCostOfDevices = $this->report->getReportSettings()->defaultPrinterCost;
        }

        return $this->AverageCostOfDevices;
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
                if ($deviceInstance->getAverageMonthlyPageCount() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume() * self::UNDERUTILIZED_THRESHHOLD_PERCENTAGE))
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
                if ($deviceInstance->getAverageMonthlyPageCount() > $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume())
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
                if ($deviceInstance->getAverageMonthlyPageCount() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume() * self::UNDERUTILIZED_THRESHHOLD_PERCENTAGE))
                {
                    $devicesArray[] = $deviceInstance;
                }
            }
            $this->_underutilizedDevices = $devicesArray;
        }

        return $this->_underutilizedDevices;
    }

    public function getOverutilizedDevices ()
    {
        if (!isset($this->_overutilizedDevices))
        {
            $devicesArray = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                if ($deviceInstance->getUsage() > 1)
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
        if ($deviceA->getUsage() == $deviceB->getUsage())
        {
            return 0;
        }

        return ($deviceA->getUsage() < $deviceB->getUsage()) ? -1 : 1;
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
        if ($deviceA->getUsage() == $deviceB->getUsage())
        {
            return 0;
        }

        return ($deviceA->getUsage() > $deviceB->getUsage()) ? -1 : 1;
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
                if (($deviceInstance->getAverageMonthlyPageCount() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume() * self::UNDERUTILIZED_THRESHHOLD_PERCENTAGE)) == false)
                {
                    //Check to see if it is not overUtilized
                    if ($deviceInstance->getUsage() < 1)
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
    public function getPercentColorDevices ()
    {
        if (!isset($this->PercentColorDevices))
        {
            $this->PercentColorDevices = $this->getNumberOfColorCapableDevices() / count($this->getDevices()->allIncludedDeviceInstances);
        }

        return $this->PercentColorDevices;
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
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getMonthlyHighCostColorDevices (Proposalgen_Model_CostPerPageSetting $costPerPageSetting)
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
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getMonthlyHighCostMonochromeDevices (Proposalgen_Model_CostPerPageSetting $costPerPageSetting)
    {
        if (!isset($this->HighCostMonochromeDevices))
        {
            $deviceArray = $this->getDevices()->purchasedDeviceInstances;
            $costArray   = array();
            /**@var $value Proposalgen_Model_DeviceInstance */
            foreach ($deviceArray as $key => $deviceInstance)
            {
                $costArray[] = array($key, $deviceInstance->getAverageMonthlyBlackAndWhitePageCount() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->monochromeCostPerPage);
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
            $this->HighCostMonochromeDevices = $highCostDevices;
        }

        return $this->HighCostMonochromeDevices;
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
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getMostExpensiveDevices ()
    {
        if (!isset($this->MostExpensiveDevices))
        {

            $deviceArray = $this->getPurchasedDevices();
            usort($deviceArray, array(
                                     $this,
                                     "ascendingSortDevicesByMonthlyCost"
                                ));
            $this->MostExpensiveDevices = $deviceArray;
        }

        return $this->MostExpensiveDevices;
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
        if ($deviceA->getMonthlyRate() == $deviceB->getMonthlyRate())
        {
            return 0;
        }

        return ($deviceA->getMonthlyRate() > $deviceB->getMonthlyRate()) ? -1 : 1;
    }

    /**
     * @return string
     */
    public function getDateReportPrepared ()
    {
        if (!isset($this->DateReportPrepared))
        {
            $report_date              = new DateTime($this->report->reportDate);
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
            $this->_averageOemOnlyCostPerPage = $costPerPage;
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
     * @return float
     */
    public function getLeastUsedDevicePercentage ()
    {
        if (!isset($this->LeastUsedDevicePercentage))
        {
            $this->LeastUsedDevicePercentage = $this->getLeastUsedDeviceCount() / $this->getDeviceCount() * 100;
        }

        return $this->LeastUsedDevicePercentage;
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
    public function getMostUsedDevicePercentage ()
    {
        if (!isset($this->MostUsedDevicePercentage))
        {
            $this->MostUsedDevicePercentage = $this->getMostUsedDeviceCount() / $this->getDeviceCount() * 100;
        }

        return $this->MostUsedDevicePercentage;
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
            $_numberOfDevicesReportingTonerLevels = count($this->getDevicesReportingTonerLevels());
        }

        return $_numberOfDevicesReportingTonerLevels;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getNumberOfDevicesNotReportingTonerLevels ()
    {
        if (!isset($this->_numberOfDevicesNotReportingTonerLevels))
        {
            $_numberOfDevicesNotReportingTonerLevels = count($this->getDevicesNotReportingTonerLevels());
        }

        return $_numberOfDevicesNotReportingTonerLevels;
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
     * @return stdClass
     */
    public function getGrossMarginTotalMonthlyCost ()
    {
        if (!isset($this->GrossMarginTotalMonthlyCost))
        {
            $totalCost                = new stdClass();
            $totalCost->BlackAndWhite = 0;
            $totalCost->Color         = 0;
            $totalCost->Combined      = 0;
            foreach ($this->getPurchasedDevices() as $device)
            {
                $totalCost->BlackAndWhite += $device->getGrossMarginMonthlyBlackAndWhiteCost();
                $totalCost->Color += $device->getGrossMarginMonthlyColorCost();
            }
            $totalCost->Combined               = $totalCost->BlackAndWhite + $totalCost->Color;
            $this->GrossMarginTotalMonthlyCost = $totalCost;
        }

        return $this->GrossMarginTotalMonthlyCost;
    }

    /**
     * @return stdClass
     */
    public function getGrossMarginTotalMonthlyRevenue ()
    {
        if (!isset($this->GrossMarginTotalMonthlyRevenue))
        {
            $totalCost                = new stdClass();
            $totalCost->BlackAndWhite = $this->getPageCounts()->Purchased->BlackAndWhite->Monthly * $this->getMPSBlackAndWhiteCPP();
            $totalCost->Color         = $this->getPageCounts()->Purchased->Color->Monthly * $this->getMPSColorCPP();
            $totalCost->Combined      = $totalCost->BlackAndWhite + $totalCost->Color;

            $this->GrossMarginTotalMonthlyRevenue = $totalCost;
        }

        return $this->GrossMarginTotalMonthlyRevenue;
    }

    /**
     * @return float
     */
    public function getNumberOfRepairs ()
    {
        if (!isset($this->NumberOfRepairs))
        {
            $this->NumberOfRepairs = $this->report->getSurvey()->averageMonthlyBreakdowns;
            if (!$this->NumberOfRepairs)
            {
                $this->NumberOfRepairs = $this->getDeviceCount() * 0.05;
            }
        }

        return $this->NumberOfRepairs;
    }

    /**
     * @return float
     */
    public function getAverageTimeBetweenBreakdownAndFix ()
    {
        if (!isset($this->AverageTimeBetweenBreakdownAndFix))
        {
            $this->AverageTimeBetweenBreakdownAndFix = $this->report->getSurvey()->averageRepairTime;
        }

        return $this->AverageTimeBetweenBreakdownAndFix;
    }

    /**
     * @return float
     */
    public function getAnnualDowntimeFromBreakdowns ()
    {
        if (!isset($this->AnnualDowntimeFromBreakdowns))
        {
            // convert to hours (8hrs = 1day : 4hrs = 1/2day) breakdowns *
            // (repair time * 8)
            $downtime                           = $this->getNumberOfRepairs() * ($this->getAverageTimeBetweenBreakdownAndFix() * 8) * 12;
            $this->AnnualDowntimeFromBreakdowns = $downtime;
        }

        return $this->AnnualDowntimeFromBreakdowns;
    }

    /**
     * @return float
     */
    public function getNumberOfAnnualInkTonerOrders ()
    {
        if (!isset($this->NumberOfAnnualInkTonerOrders))
        {
            $this->NumberOfAnnualInkTonerOrders = $this->getNumberOfOrdersPerMonth() * 12;
        }

        return $this->NumberOfAnnualInkTonerOrders;
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
     * @return float
     */
    public function getPercentPrintingDoneOnInkjet ()
    {
        if (!isset($this->PercentPrintingDoneOnInkjet))
        {
            $this->PercentPrintingDoneOnInkjet = $this->report->getSurvey()->percentageOfInkjetPrintVolume;
        }

        return $this->PercentPrintingDoneOnInkjet;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getHighRiskDevices ()
    {
        if (!isset($this->HighRiskDevices))
        {
            $deviceArraySortedByUsage       = $this->getDevices()->allIncludedDeviceInstances;
            $deviceArraySortedByAge         = $this->getDevices()->allIncludedDeviceInstances;
            $deviceArraySortedByRiskRanking = $this->getDevices()->allIncludedDeviceInstances;
            usort($deviceArraySortedByUsage, array(
                                                  $this,
                                                  "sortDevicesByLifeUsage"
                                             ));
            usort($deviceArraySortedByAge, array(
                                                $this,
                                                "sortDevicesByAge"
                                           ));
            // setting the age rank for each device
            $ctr = 1;
            foreach ($deviceArraySortedByAge as $deviceInstance)
            {
                $deviceInstance->setAgeRank($ctr);
                $ctr++;
            }

            // setting the life usage rank for each device
            $ctr = 1;
            foreach ($deviceArraySortedByAge as $deviceInstance)
            {
                $deviceInstance->setLifeUsageRank($ctr);
                $ctr++;
            }
            // setting the risk ranking based on age and life usage rank
            foreach ($this->getDevices()->allIncludedDeviceInstances as $deviceInstance)
            {
                $deviceInstance->setRiskRank($deviceInstance->getLifeUsageRank() + $deviceInstance->getAgeRank());
            }

            // sorting devices based on risk ranking
            usort($deviceArraySortedByRiskRanking, array(
                                                        $this,
                                                        "sortDevicesByRiskRanking"
                                                   ));
            $this->HighRiskDevices = $deviceArraySortedByRiskRanking;
        }

        return $this->HighRiskDevices;
    }

    /**
     * Callback function for uSort when we want to sort devices based on life
     * usage
     *
     * @param Proposalgen_Model_DeviceInstance $deviceA
     * @param Proposalgen_Model_DeviceInstance $deviceB
     *
     * @return int
     */
    public function sortDevicesByLifeUsage ($deviceA, $deviceB)
    {
        if ($deviceA->getLifeUsage() == $deviceB->getLifeUsage())
        {
            return 0;
        }

        return ($deviceA->getLifeUsage() < $deviceB->getLifeUsage()) ? -1 : 1;
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
     * Callback function for uSort when we want to sort devices based their risk
     * ranking
     *
     * @param Proposalgen_Model_DeviceInstance $deviceA
     * @param Proposalgen_Model_DeviceInstance $deviceB
     *
     * @return int
     */
    public function sortDevicesByRiskRanking ($deviceA, $deviceB)
    {
        if ($deviceA->getRiskRank() == $deviceB->getRiskRank())
        {
            return 0;
        }

        return ($deviceA->getRiskRank() > $deviceB->getRiskRank()) ? -1 : 1;
    }

    /**
     *
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
            $this->WeeklyITHours = $this->report->getSurvey()->hoursSpentOnIt;
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
            $this->AverageITRate = $this->report->getSurvey()->averageItHourlyRate;
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
    public function getGraphs ()
    {
        if (!isset($this->Graphs))
        {
            // Variables that could be settings
            $OD_AverageMonthlyPagesPerEmployee = 200;
            $OD_AverageMonthlyPages            = 4200;
            $OD_AverageEmployeesPerDevice      = 4.4;

            // Other variables used in several places
            $pageCounts    = $this->getPageCounts();
            $companyName   = $this->report->getClient()->companyName;
            $employeeCount = $this->report->getClient()->employeeCount;

            // Formatting variables
            $numberValueMarker                          = "N *sz0";
            $PrintIQSavingsBarGraph_currencyValueMarker = "N $*sz0";

            /**
             * -- PrintIQSavingsBarGraph
             */
            $highest  = ($this->getPrintIQTotalCost() > $this->getTotalPurchasedAnnualCost()) ? $this->getPrintIQTotalCost() : $this->getTotalPurchasedAnnualCost();
            $barGraph = new gchart\gGroupedBarChart(600, 160);
            $barGraph->setHorizontal(true);
            $barGraph->setTitle("Annual Printing Costs for Purchased Hardware");
            $barGraph->setVisibleAxes(array(
                                           'x'
                                      ));
            $barGraph->addDataSet(array(
                                       $this->getTotalPurchasedAnnualCost()
                                  ));
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->addDataSet(array(
                                       $this->getPrintIQTotalCost()
                                  ));
            $barGraph->setLegend(array(
                                      "Current",
                                      "PrintIQ"
                                 ));
            $barGraph->addAxisRange(0, 0, $highest * 1.3);
            $barGraph->setDataRange(0, $highest * 1.3);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("r");
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($PrintIQSavingsBarGraph_currencyValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($PrintIQSavingsBarGraph_currencyValueMarker, "000000", "1", "-1", "11");
            // Graphs[0]
            $this->Graphs [] = $barGraph->getUrl();

            /**
             * -- LeasedVsPurchasedBarGraph
             */
            $highest  = ($this->getLeasedDeviceCount() > $this->getPurchasedDeviceCount()) ? $this->getLeasedDeviceCount() : $this->getPurchasedDeviceCount();
            $barGraph = new gchart\gBarChart(280, 230);
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
            $barGraph->setBarScale(70, 10);
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
            $barGraph = new gchart\gBarChart(280, 230);

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
            $barGraph->setBarScale(70, 10);
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
                    // $legendItems[] =
                    $uniqueModelArray [$device->getMasterDevice()->modelName] = 1;
                }
            }
            $uniqueDevicesGraph = new gchart\gPie3DChart(350, 270);
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
            // $uniqueDevicesGraph->setLegend($legendItems);
            // Graphs[3]
            $this->Graphs [] = $uniqueDevicesGraph->getUrl();

            /**
             * -- AverageMonthlyPagesBarGraph
             */
            $averagePageCount = round($pageCounts->Total->Combined->Monthly / $this->getDeviceCount(), 0);
            $highest          = ($averagePageCount > $OD_AverageMonthlyPages) ? $averagePageCount : $OD_AverageMonthlyPages;
            $barGraph         = new gchart\gBarChart(200, 300);
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
            $barGraph         = new gchart\gBarChart(200, 300);
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
            $barGraph           = new gchart\gBarChart(200, 300);
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

            /**
             * -- Color Capable Devices Graph
             */
            $colorPercentage = 0;
            if ($this->getDeviceCount())
            {
                $colorPercentage = round((($this->getNumberOfColorCapableDevices() / $this->getDeviceCount()) * 100), 2);
            }

            $notColorPercentage = 100 - $colorPercentage;
            $colorCapableGraph  = new gchart\gPie3DChart(305, 210);
            $colorCapableGraph->setTitle("Color-Capable Printing Devices");
            $colorCapableGraph->addDataSet(array(
                                                $colorPercentage,
                                                $notColorPercentage
                                           ));
            $colorCapableGraph->setLegend(array(
                                               "Color-capable",
                                               "Black-and-white only"
                                          ));
            $colorCapableGraph->setLabels(array(
                                               "$colorPercentage%"
                                          ));
            $colorCapableGraph->addColors(array(
                                               "E21736",
                                               "0194D2"
                                          ));
            $colorCapableGraph->setLegendPosition("bv");
            // Graphs[7]
            $this->Graphs [] = $colorCapableGraph->getUrl();

            /**
             * -- ColorVSBWPagesGraph
             */
            $colorPercentage = 0;
            if ($pageCounts->Total->Combined->Monthly > 0)
            {
                $colorPercentage = round((($pageCounts->Total->Color->Monthly / $pageCounts->Total->Combined->Monthly) * 100), 2);
            }

            $bwPercentage        = 100 - $colorPercentage;
            $colorVSBWPagesGraph = new gchart\gPie3DChart(305, 210);
            $colorVSBWPagesGraph->setTitle("Color vs Black/White Pages");
            $colorVSBWPagesGraph->addDataSet(array(
                                                  $colorPercentage,
                                                  $bwPercentage
                                             ));
            $colorVSBWPagesGraph->setLegend(array(
                                                 "Color pages printed",
                                                 "Black-and-white pages printed"
                                            ));
            $colorVSBWPagesGraph->setLabels(array(
                                                 "$colorPercentage%",
                                                 "$bwPercentage%"
                                            ));
            $colorVSBWPagesGraph->addColors(array(
                                                 "E21736",
                                                 "0194D2"
                                            ));
            $colorVSBWPagesGraph->setLegendPosition("bv");
            // Graphs[8]
            $this->Graphs [] = $colorVSBWPagesGraph->getUrl();

            /**
             * -- Device Ages Graph
             */
            $deviceAges = array(
                "Less than 5 years old" => 0,
                "5-6 years old"         => 0,
                "7-8 years old"         => 0,
                "More than 8 years old" => 0
            );
            foreach ($this->getPurchasedDevices() as $device)
            {
                if ($device->getAge() < 5)
                {
                    $deviceAges ["Less than 5 years old"]++;
                }
                else if ($device->getAge() <= 6)
                {
                    $deviceAges ["5-6 years old"]++;
                }
                else if ($device->getAge() <= 8)
                {
                    $deviceAges ["7-8 years old"]++;
                }
                else
                {
                    $deviceAges ["More than 8 years old"]++;
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
                    $percentage     = round(($count / $this->getPurchasedDeviceCount()) * 100, 2);
                    $labels []      = "$percentage%";
                }
            }
            $deviceAgeGraph = new gchart\gPie3DChart(350, 200);
            $deviceAgeGraph->addDataSet($dataSet);
            $deviceAgeGraph->setLegend($legendItems);
            $deviceAgeGraph->setLabels($labels);
            $deviceAgeGraph->addColors(array(
                                            "E21736",
                                            "0094cf",
                                            "5c3f9b",
                                            "adba1d"
                                       ));
            $deviceAgeGraph->setLegendPosition("bv");
            // Graphs[9]
            $this->Graphs [] = $deviceAgeGraph->getUrl();

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
            // Graphs[10]
            $this->Graphs [] = $scanCapableGraph->getUrl();

            /**
             * -- FaxCapableDevicesGraph
             */
            $faxPercentage = 0;
            if ($this->getDeviceCount())
            {
                $faxPercentage = round((($this->getNumberOfFaxCapableDevices() / $this->getDeviceCount()) * 100), 2);
            }

            $notFaxPercentage = 100 - $faxPercentage;
            $faxCapable       = new gchart\gPie3DChart(200, 160);
            $faxCapable->setTitle("Fax-Capable Printing Devices");
            $faxCapable->addDataSet(array(
                                         $faxPercentage,
                                         $notFaxPercentage
                                    ));
            $faxCapable->setLegend(array(
                                        "Fax capable",
                                        "Not fax capable"
                                   ));
            $faxCapable->setLabels(array(
                                        "$faxPercentage%"
                                   ));
            $faxCapable->addColors(array(
                                        "E21736",
                                        "0194D2"
                                   ));
            $faxCapable->setLegendPosition("bv");
            // Graphs[11]
            $this->Graphs [] = $faxCapable->getUrl();

            /**
             * -- SmallColorCapableDevicesGraph
             */
            $colorCapableGraph->setDimensions(200, 160);
            // Graphs[12]
            $this->Graphs [] = $colorCapableGraph->getUrl();

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
             * -- BigScanCapableDevicesGraph
             */
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
            $this->CostOfExecutingSuppliesOrders = $this->report->getSurvey()->costToExecuteSuppliesOrder * $this->report->getSurvey()->numberOfSupplyOrdersPerMonth * 12;
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
            $this->EstimatedAnnualSupplyRelatedExpense = $this->getCostOfInkAndToner() + $this->getCostOfExecutingSuppliesOrders();
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
            $this->AnnualCostOfOutSourcing = $this->report->getSurvey()->costOfLabor;
            if (!$this->AnnualCostOfOutSourcing)
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
    public function getEstimatedAllInBlackAndWhiteCPP ()
    {
        if (!isset($this->EstimatedAllInBlackAndWhiteCPP))
        {
            $workingCPP                     = 0;
            $monochromeCostPerPage          = 0;
            $costOfBlackAndWhiteInkAndToner = 0;
            $costWithNoInkToner             = $this->getTotalPurchasedAnnualCost() - $this->getCostOfInkAndToner();
            if ($this->getPageCounts()->Purchased->Combined->Yearly)
            {
                $workingCPP = $costWithNoInkToner / $this->getPageCounts()->Purchased->Combined->Yearly;
            }
            foreach ($this->getPurchasedDevices() as $device)
            {
                $costOfBlackAndWhiteInkAndToner += $device->getCostOfBlackAndWhiteInkAndToner();
            }
            if ($this->getPageCounts()->Purchased->BlackAndWhite->Yearly)
            {
                $monochromeCostPerPage = $workingCPP + ($costOfBlackAndWhiteInkAndToner / ($this->getPageCounts()->Purchased->BlackAndWhite->Yearly / 12));
            }
            $this->EstimatedAllInBlackAndWhiteCPP = $monochromeCostPerPage;
        }

        return $this->EstimatedAllInBlackAndWhiteCPP;
    }

    /**
     * @return float
     */
    public function getEstimatedAllInColorCPP ()
    {
        if (!isset($this->EstimatedAllInColorCPP))
        {
            $workingCPP             = 0;
            $costOfColorInkAndToner = 0;
            $ColorCPP               = 0;
            $costWithNoInkToner     = $this->getTotalPurchasedAnnualCost() - $this->getCostOfInkAndToner();
            if ($this->getPageCounts()->Purchased->Combined->Yearly)
            {
                $workingCPP = $costWithNoInkToner / $this->getPageCounts()->Purchased->Combined->Yearly;
            }
            foreach ($this->getPurchasedDevices() as $device)
            {
                $costOfColorInkAndToner += $device->getCostOfColorInkAndToner();
            }
            if ($this->getPageCounts()->Purchased->Color->Yearly)
            {
                $ColorCPP = $workingCPP + ($costOfColorInkAndToner / ($this->getPageCounts()->Purchased->Color->Yearly / 12));
            }
            $this->EstimatedAllInColorCPP = $ColorCPP;
        }

        return $this->EstimatedAllInColorCPP;
    }

    /**
     * @return float
     */
    public function getMPSBlackAndWhiteCPP ()
    {
        if (!isset($this->MPSBlackAndWhiteCPP))
        {
            $this->MPSBlackAndWhiteCPP = $this->report->getReportSettings()->mpsBwCostPerPage;
        }

        return $this->MPSBlackAndWhiteCPP;
    }

    /**
     * @return float
     */
    public function getMPSColorCPP ()
    {
        if (!isset($this->MPSColorCPP))
        {
            $this->MPSColorCPP = $this->report->getReportSettings()->mpsColorCostPerPage;
        }

        return $this->MPSColorCPP;
    }

    /**
     * @return float
     */
    public function getInternalAdminCost ()
    {
        if (!isset($this->InternalAdminCost))
        {
            $this->InternalAdminCost = $this->report->getSurvey()->costToExecuteSuppliesOrder * 12;
        }

        return $this->InternalAdminCost;
    }

    /**
     * @return float
     */
    public function getPrintIQTotalCost ()
    {
        if (!isset($this->PrintIQTotalCost))
        {
            $this->PrintIQTotalCost = $this->getInternalAdminCost() + ($this->getAnnualITCost() * 0.5) + ($this->getPageCounts()->Purchased->Color->Yearly * $this->getMPSColorCPP()) + ($this->getPageCounts()->Purchased->BlackAndWhite->Yearly * $this->getMPSBlackAndWhiteCPP()) + $this->getAnnualCostOfHardwarePurchases();
        }

        return $this->PrintIQTotalCost;
    }

    /**
     * @return float
     */
    public function getPrintIQSavings ()
    {
        if (!isset($this->PrintIQSavings))
        {
            $this->PrintIQSavings = $this->getTotalPurchasedAnnualCost() - $this->getPrintIQTotalCost();
        }

        return $this->PrintIQSavings;
    }

    /**
     * @return float
     */
    public function getNumberOfOrdersPerMonth ()
    {
        if (!isset($this->NumberOfOrdersPerMonth))
        {
            $this->NumberOfOrdersPerMonth = $this->report->getSurvey()->numberOfSupplyOrdersPerMonth;
        }

        return $this->NumberOfOrdersPerMonth;
    }

    /**
     * @return float
     */
    public function getEmployeeCount ()
    {
        if (!isset($this->EmployeeCount))
        {
            $this->EmployeeCount = $this->report->getClient()->employeeCount;
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
     * @return Proposalgen_Model_ReplacementDevice[]
     */
    public function getReplacementDevices ()
    {
        if (!isset($this->ReplacementDevices))
        {
            $this->ReplacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->fetchCheapestForEachCategory();
        }

        return $this->ReplacementDevices;
    }

    /**
     * @return int
     */
    public function getReplacementDeviceCount ()
    {
        if (!isset($this->ReplacementDeviceCount))
        {
            $this->ReplacementDeviceCount = count($this->getReplacementDevices());
        }

        return $this->ReplacementDeviceCount;
    }

    /**
     * @param $type
     *
     * @return Proposalgen_Model_ReplacementDevice
     */
    public function getReplacement ($type)
    {
        $replacementDevice = null;
        $monthlyRate       = 0;
        foreach ($this->getReplacementDevices() as $device)
        {
            if ($device->replacementCategory == $type && ($device->monthlyRate < $monthlyRate || $monthlyRate == 0))
            {
                $replacementDevice = $device;
                $monthlyRate       = $device->monthlyRate;
            }
        }

        return $replacementDevice;
    }

    /**
     * @return stdClass
     */
    public function getDevicesToBeReplaced ()
    {
        if (!isset($this->DevicesToBeReplaced))
        {
            $minimumSavings = 20;
            $ampvThreshold  = 7000;

            $replacedDevices                   = new stdClass();
            $replacedDevices->BlackAndWhite    = array();
            $replacedDevices->BlackAndWhiteMFP = array();
            $replacedDevices->Color            = array();
            $replacedDevices->ColorMFP         = array();
            $replacedDevices->NoTonerLevels    = array();

            // It's over 9000! (Really we mean over 10,000 pages printed)
            $replacedDevices->OverMaxLeasedCapacity = array();
            $replacedDevices->LeftOver              = array();

            $replacementDevices = $this->getReplacementDevices();
            foreach ($this->getPurchasedDevices() as $device)
            {
                $deviceReplaced = false;
                if ($device->getAverageMonthlyPageCount() >= $ampvThreshold)
                {
                    $replacedDevices->OverMaxLeasedCapacity [] = $device;
                }
                else
                {
                    // If we are here the device is with in the page count and supports JIT?
                    switch ($device->getMasterDevice()->tonerConfigId)
                    {
                        case Proposalgen_Model_TonerConfig::BLACK_ONLY :
                            if ($device->getMasterDevice()->isFax || $device->getMasterDevice()->isScanner || $device->getMasterDevice()->isCopier)
                            {
                                $savings = $device->getMonthlyRate() - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_BWMFP]->monthlyRate;
                                // MFP
                                if ($savings >= $minimumSavings)
                                {
                                    $replacedDevices->BlackAndWhiteMFP [] = $device;
                                    $deviceReplaced                       = true;
                                }
                            }
                            else
                            {
                                $savings = $device->getMonthlyRate() - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_BW]->monthlyRate;
                                if ($savings >= $minimumSavings)
                                {
                                    $replacedDevices->BlackAndWhite [] = $device;
                                    $deviceReplaced                    = true;
                                }
                            }
                            break;
                        case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED :
                        case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED :
                        case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED :
                            if ($device->getMasterDevice()->isFax || $device->getMasterDevice()->isScanner || $device->getMasterDevice()->isCopier)
                            {
                                // MFP
                                $savings = $device->getMonthlyRate() - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLORMFP]->monthlyRate;
                                if ($savings >= $minimumSavings)
                                {
                                    $replacedDevices->ColorMFP [] = $device;
                                    $deviceReplaced               = true;
                                }
                            }
                            else
                            {

                                $savings = $device->getMonthlyRate() - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR]->monthlyRate;
                                if ($savings >= $minimumSavings)
                                {
                                    $replacedDevices->Color [] = $device;
                                    $deviceReplaced            = true;
                                }
                            }
                            break;
                    }
                }
                if (!$deviceReplaced)
                {
                    if (!$device->reportsTonerLevels)
                    {
                        $replacedDevices->NoTonerLevels [] = $device;
                    }
                    $replacedDevices->LeftOver [] = $device;
                }
            }
            $this->DevicesToBeReplaced = $replacedDevices;
        }

        return $this->DevicesToBeReplaced;
    }

    /**
     * @return int
     */
    public function getLeftOverBlackAndWhitePageCount ()
    {
        if (!isset($this->LeftOverBlackAndWhitePageCount))
        {
            $pageCount = 0;
            /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
            foreach ($this->getDevicesToBeReplaced()->LeftOver as $deviceInstance)
            {
                $pageCount += $deviceInstance->getAverageMonthlyBlackAndWhitePageCount();
            }
            $this->LeftOverBlackAndWhitePageCount = $pageCount * 12;
        }

        return $this->LeftOverBlackAndWhitePageCount;
    }

    /**
     * @return int
     */
    public function getLeftOverColorPageCount ()
    {
        if (!isset($this->LeftOverColorPageCount))
        {
            $pageCount = 0;
            /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
            foreach ($this->getDevicesToBeReplaced()->LeftOver as $deviceInstance)
            {
                $pageCount += $deviceInstance->getAverageMonthlyColorPageCount();
            }
            $this->LeftOverColorPageCount = $pageCount * 12;
        }

        return $this->LeftOverColorPageCount;
    }

    /**
     * @return float
     */
    public function getLeftOverPrintIQCost ()
    {
        if (!isset($this->LeftOverPrintIQCost))
        {
            $this->LeftOverPrintIQCost = ($this->getLeftOverColorPageCount() * $this->getMPSColorCPP()) + ($this->getLeftOverBlackAndWhitePageCount() * $this->getMPSBlackAndWhiteCPP()) + $this->getInternalAdminCost() + ($this->getAnnualITCost() * 0.5);
        }

        return $this->LeftOverPrintIQCost;
    }

    /**
     * @return float
     */
    public function getLeftOverCostOfColorDevices ()
    {
        if (!isset($this->LeftOverCostOfColorDevices))
        {
            $cost = 0;
            /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
            foreach ($this->getDevicesToBeReplaced()->BlackAndWhite as $deviceInstance)
            {
                $cost += $deviceInstance->getMonthlyRate();
            }

            foreach ($this->getDevicesToBeReplaced()->BlackAndWhiteMFP as $deviceInstance)
            {
                $cost += $deviceInstance->getMonthlyRate();
            }
            $this->LeftOverCostOfColorDevices = $cost * 12;
        }

        return $this->LeftOverCostOfColorDevices;
    }

    /**
     * @return float
     */
    public function getLeftOverCostOfBlackAndWhiteDevices ()
    {
        if (!isset($this->LeftOverCostOfBlackAndWhiteDevices))
        {
            $cost = 0;
            /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
            foreach ($this->getDevicesToBeReplaced()->Color as $deviceInstance)
            {
                $cost += $deviceInstance->getMonthlyRate();
            }
            foreach ($this->getDevicesToBeReplaced()->ColorMFP as $deviceInstance)
            {
                $cost += $deviceInstance->getMonthlyRate();
            }
            $this->LeftOverCostOfBlackAndWhiteDevices = $cost * 12;
        }

        return $this->LeftOverCostOfBlackAndWhiteDevices;
    }

    /**
     * @return float
     */
    public function getCostOfRemainingDevices ()
    {
        if (!isset($this->CostOfRemainingDevices))
        {
            $this->CostOfRemainingDevices = $this->getTotalPurchasedAnnualCost() - $this->getCurrentCostOfReplacedBlackAndWhitePrinters() - $this->getCurrentCostOfReplacedBlackAndWhiteMFPPrinters() - $this->getCurrentCostOfReplacedColorMFPPrinters() - $this->getCurrentCostOfReplacedColorPrinters();
        }

        return $this->CostOfRemainingDevices;
    }

    /**
     * @return float
     */
    public function getCurrentCostOfReplacedColorMFPPrinters ()
    {
        if (!isset($this->CurrentCostOfReplacedColorMFPPrinters))
        {
            $cost = 0;
            /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
            foreach ($this->getDevicesToBeReplaced()->ColorMFP as $deviceInstance)
            {
                $cost += $deviceInstance->getMonthlyRate();
            }
            $this->CurrentCostOfReplacedColorMFPPrinters = $cost * 12;
        }

        return $this->CurrentCostOfReplacedColorMFPPrinters;
    }

    /**
     * @return float
     */
    public function getCurrentCostOfReplacedBlackAndWhiteMFPPrinters ()
    {
        if (!isset($this->CurrentCostOfReplacedBlackAndWhiteMFPPrinters))
        {
            $cost = 0;
            /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
            foreach ($this->getDevicesToBeReplaced()->BlackAndWhiteMFP as $deviceInstance)
            {
                $cost += $deviceInstance->getMonthlyRate();
            }
            $this->CurrentCostOfReplacedBlackAndWhiteMFPPrinters = $cost * 12;
        }

        return $this->CurrentCostOfReplacedBlackAndWhiteMFPPrinters;
    }

    /**
     * @return float
     */
    public function getProposedCostOfReplacedBlackAndWhiteMFPPrinters ()
    {
        if (!isset($this->ProposedCostOfReplacedBlackAndWhiteMFPPrinters))
        {
            $countOfReplacedDevices                               = count($this->getDevicesToBeReplaced()->BlackAndWhiteMFP);
            $cost                                                 = $countOfReplacedDevices * $this->getReplacement('BLACK & WHITE MFP')->monthlyRate;
            $this->ProposedCostOfReplacedBlackAndWhiteMFPPrinters = $cost * 12;
        }

        return $this->ProposedCostOfReplacedBlackAndWhiteMFPPrinters;
    }

    /**
     * @return float
     */
    public function getProposedCostOfReplacedColorMFPPrinters ()
    {
        if (!isset($this->ProposedCostOfReplacedColorMFPPrinters))
        {
            $countOfReplacedDevices                       = count($this->getDevicesToBeReplaced()->ColorMFP);
            $cost                                         = $countOfReplacedDevices * $this->getReplacement('COLOR MFP')->monthlyRate;
            $this->ProposedCostOfReplacedColorMFPPrinters = $cost * 12;
        }

        return $this->ProposedCostOfReplacedColorMFPPrinters;
    }

    /**
     * @return float
     */
    public function getCurrentCostOfReplacedColorPrinters ()
    {
        if (!isset($this->CurrentCostOfReplacedColorPrinters))
        {
            $cost = 0;
            /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
            foreach ($this->getDevicesToBeReplaced()->Color as $deviceInstance)
            {
                $cost += $deviceInstance->getMonthlyRate();
            }
            $this->CurrentCostOfReplacedColorPrinters = $cost * 12;
        }

        return $this->CurrentCostOfReplacedColorPrinters;
    }

    /**
     * @return float
     */
    public function getCurrentCostOfReplacedBlackAndWhitePrinters ()
    {
        if (!isset($this->CurrentCostOfReplacedBlackAndWhitePrinters))
        {
            $cost = 0;
            /* @var $deviceInstance Proposalgen_Model_DeviceInstance */
            foreach ($this->getDevicesToBeReplaced()->BlackAndWhite as $deviceInstance)
            {
                $cost += $deviceInstance->getMonthlyRate();
            }

            $this->CurrentCostOfReplacedBlackAndWhitePrinters = $cost * 12;
        }

        return $this->CurrentCostOfReplacedBlackAndWhitePrinters;
    }

    /**
     * @return float
     */
    public function getProposedCostOfReplacedBlackAndWhitePrinters ()
    {
        if (!isset($this->ProposedCostOfReplacedBlackAndWhitePrinters))
        {
            $countOfReplacedDevices                            = count($this->getDevicesToBeReplaced()->BlackAndWhite);
            $cost                                              = $countOfReplacedDevices * $this->getReplacement('BLACK & WHITE')->monthlyRate;
            $this->ProposedCostOfReplacedBlackAndWhitePrinters = $cost * 12;
        }

        return $this->ProposedCostOfReplacedBlackAndWhitePrinters;
    }

    /**
     * @return float
     */
    public function getProposedCostOfReplacedColorPrinters ()
    {
        if (!isset($this->ProposedCostOfReplacedColorPrinters))
        {
            $countOfReplacedDevices                    = count($this->getDevicesToBeReplaced()->Color);
            $cost                                      = $countOfReplacedDevices * $this->getReplacement('COLOR')->monthlyRate;
            $this->ProposedCostOfReplacedColorPrinters = $cost * 12;
        }

        return $this->ProposedCostOfReplacedColorPrinters;
    }

    /**
     * @return float
     */
    public function getTotalProposedAnnualCost ()
    {
        if (!isset($this->TotalProposedAnnualCost))
        {
            $this->TotalProposedAnnualCost = $this->getProposedCostOfReplacedColorPrinters() + $this->getProposedCostOfReplacedColorMFPPrinters() + $this->getProposedCostOfReplacedBlackAndWhitePrinters() + $this->getProposedCostOfReplacedBlackAndWhiteMFPPrinters() + $this->getLeftOverPrintIQCost();
        }

        return $this->TotalProposedAnnualCost;
    }

    /**
     * @return float
     */
    public function getTotalAnnualSavings ()
    {
        if (!isset($this->TotalAnnualSavings))
        {
            $this->TotalAnnualSavings = ($this->getCurrentCostOfReplacedBlackAndWhitePrinters() - $this->getProposedCostOfReplacedBlackAndWhitePrinters()) + ($this->getCurrentCostOfReplacedBlackAndWhiteMFPPrinters() - $this->getProposedCostOfReplacedBlackAndWhiteMFPPrinters()) + ($this->getCurrentCostOfReplacedColorPrinters() - $this->getProposedCostOfReplacedColorPrinters()) + ($this->getCurrentCostOfReplacedColorMFPPrinters() - $this->getProposedCostOfReplacedColorMFPPrinters()) + ($this->getCostOfRemainingDevices() - $this->getLeftOverPrintIQCost());
        }

        return $this->TotalAnnualSavings;
    }

    /**
     * @return float
     */
    public function getCostOfExecutingSuppliesOrder ()
    {
        if (!isset($this->CostOfExecutingSuppliesOrder))
        {
            $this->CostOfExecutingSuppliesOrder = $this->report->getSurvey()->costToExecuteSuppliesOrder * $this->getNumberOfAnnualInkTonerOrders();
        }

        return $this->CostOfExecutingSuppliesOrder;
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
     * @return float
     */
    public function getGrossMarginMonthlyProfit ()
    {
        if (!isset($this->GrossMarginMonthlyProfit))
        {
            $this->GrossMarginMonthlyProfit = $this->getGrossMarginTotalMonthlyRevenue()->Combined - $this->getGrossMarginTotalMonthlyCost()->Combined;
        }

        return $this->GrossMarginMonthlyProfit;
    }

    /**
     * @return float
     */
    public function getGrossMarginOverallMargin ()
    {
        if (!isset($this->GrossMarginOverallMargin))
        {
            $this->GrossMarginOverallMargin = $this->getGrossMarginMonthlyProfit() / $this->getGrossMarginTotalMonthlyRevenue()->Combined * 100;
        }

        return $this->GrossMarginOverallMargin;
    }

    /**
     * @return stdClass
     */
    public function getGrossMarginWeightedCPP ()
    {
        if (!isset($this->GrossMarginWeightedCPP))
        {
            $this->GrossMarginWeightedCPP                = new stdClass();
            $this->GrossMarginWeightedCPP->BlackAndWhite = 0;
            $this->GrossMarginWeightedCPP->Color         = 0;
            if ($this->PageCounts->Purchased->BlackAndWhite->Monthly > 0)
            {
                $this->GrossMarginWeightedCPP->BlackAndWhite = $this->getGrossMarginTotalMonthlyCost()->BlackAndWhite / $this->getPageCounts()->Purchased->BlackAndWhite->Monthly;
            }
            if ($this->PageCounts->Purchased->Color->Monthly > 0)
            {
                $this->GrossMarginWeightedCPP->Color = $this->getGrossMarginTotalMonthlyCost()->Color / $this->getPageCounts()->Purchased->Color->Monthly;
            }
        }

        return $this->GrossMarginWeightedCPP;
    }

    /**
     * @return float
     */
    public function getGrossMarginBlackAndWhiteMargin ()
    {
        if (!isset($this->GrossMarginBlackAndWhiteMargin))
        {
            $this->GrossMarginBlackAndWhiteMargin = ($this->getMPSBlackAndWhiteCPP() - $this->getGrossMarginWeightedCPP()->BlackAndWhite) / $this->getMPSBlackAndWhiteCPP() * 100;
        }

        return $this->GrossMarginBlackAndWhiteMargin;
    }

    /**
     * @return float
     */
    public function getGrossMarginColorMargin ()
    {
        if (!isset($this->GrossMarginColorMargin))
        {
            $this->GrossMarginColorMargin = ($this->getMPSColorCPP() - $this->getGrossMarginWeightedCPP()->Color) / $this->getMPSColorCPP() * 100;
            ;
        }

        return $this->GrossMarginColorMargin;
    }

    /**
     * @return Proposalgen_Model_Toner[]
     */
    public function getUniqueTonerList ()
    {
        if (!isset($this->UniqueTonerList))
        {
            $uniqueToners = array();
            foreach ($this->getUniqueDeviceList() as $masterDevice)
            {
                $deviceToners = $masterDevice->getTonersForAssessment();
                foreach ($deviceToners as $toner)
                {
                    if (!in_array($toner, $uniqueToners))
                    {
                        $uniqueToners [] = $toner;
                    }
                }
            }
            $this->UniqueTonerList = $uniqueToners;
        }

        return $this->UniqueTonerList;
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
                $deviceToners = $masterDevice->getTonersForAssessment();
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return float
     */
    public function calculatePurchasedTotalMonthlyCost (Proposalgen_Model_CostPerPageSetting $costPerPageSetting)
    {
        if (!isset($this->_purchasedTotalMonthlyCost))
        {
            $total = 0;
            foreach ($this->getPurchasedDevices() as $deviceInstance)
            {
                $total += $deviceInstance->calculateMonthlyCost($costPerPageSetting, $deviceInstance->getMasterDevice());
            }
            $this->_purchasedTotalMonthlyCost = $total;
        }

        return $this->_purchasedTotalMonthlyCost;
    }

    /**
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return float
     */
    public function calculatePurchasedColorMonthlyCost (Proposalgen_Model_CostPerPageSetting $costPerPageSetting)
    {
        if (!isset($this->_purchasedColorMonthlyCost))
        {
            $total = 0;
            foreach ($this->getPurchasedDevices() as $deviceInstance)
            {
                $total += $deviceInstance->calculateMonthlyColorCost($costPerPageSetting, $deviceInstance->getMasterDevice());
            }
            $this->_purchasedColorMonthlyCost = $total;
        }

        return $this->_purchasedColorMonthlyCost;
    }

    /**
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return float
     */
    public function calculatePurchasedMonochromeMonthlyCost (Proposalgen_Model_CostPerPageSetting $costPerPageSetting)
    {
        if (!isset($this->_purchasedMonochromeMonthlyCost))
        {
            $total = 0;
            foreach ($this->getPurchasedDevices() as $deviceInstance)
            {
                $total += $deviceInstance->calculateMonthlyMonoCost($costPerPageSetting, $deviceInstance->getMasterDevice());
            }
            $this->_purchasedMonochromeMonthlyCost = $total;
        }

        return $this->_purchasedMonochromeMonthlyCost;
    }

    /**
     * @return float
     */
    public function calculateTotalMonthlyCost ()
    {
        return ($this->getEstimatedAnnualCostOfLeaseMachines() + $this->getTotalPurchasedAnnualCost()) / 12;
    }

    /**
     * @return float
     */
    public function calculateEstimatedCompTonerCostAnnually ()
    {
        return ($this->calculateAverageCompatibleOnlyCostPerPage()->colorCostPerPage * $this->getPageCounts()->Purchased->Color->Monthly + ($this->calculateAverageCompatibleOnlyCostPerPage()->monochromeCostPerPage * $this->getPageCounts()->Purchased->BlackAndWhite->Monthly)) * 12;
    }

    /**
     * @return float
     */
    public function calculateEstimatedOemTonerCostAnnually ()
    {
        return ($this->calculateAverageOemOnlyCostPerPage()->colorCostPerPage * $this->getPageCounts()->Purchased->Color->Monthly + ($this->calculateAverageOemOnlyCostPerPage()->monochromeCostPerPage * $this->getPageCounts()->Purchased->BlackAndWhite->Monthly)) * 12;
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
}