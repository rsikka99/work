<?php
/**
 * Class Assessment_ViewModel_Assessment
 */
class Assessment_ViewModel_Assessment extends Assessment_ViewModel_Abstract
{
    /**
     * All devices that have ages older or equal to this are in the old device list report
     */
    const OLD_DEVICE_LIST = 5;
    /**
     * All devices printing less than this are considered underutilized.
     */
    const UNDERUTILIZED_THRESHOLD_PERCENTAGE = 0.05;
    /**
     * All devices that have ages older than this are considered old/
     */
    const OLD_DEVICE_THRESHOLD = 10;

    public static $_instance;

    // New Separated Proposal
    protected $Ranking;
    protected $ReportId;
    protected $DefaultToners;
    protected $Devices;
    protected $ExcludedDevices;
    protected $LeasedDevices;
    protected $PurchasedDevices;
    protected $User;
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
    protected $_numberOfDevicesReportingTonerLevels;
    protected $_numberOfColorCapablePurchasedDevices;
    protected $_maximumMonthlyPurchasedPrintVolume;
    protected $_optimizedDevices;
    protected $_numberOfDevicesNotReportingTonerLevels;
    protected $_numberOfCopyCapableDevices;
    protected $_includedDevicesSortedAscendingByAge;
    protected $_includedDevicesSortedDescendingByAge;
    protected $_pageCounts;

    public $highCostPurchasedDevices;

    /**
     * @param Assessment_Model_Assessment $report
     */
    public function __construct (Assessment_Model_Assessment $report)
    {
        parent::__construct($report);

        if (isset(self::$_instance))
        {
            self::$_instance = $this;
        }

        // Get the report settings
        $assessmentSettings = $this->assessment->getAssessmentSettings();

        Proposalgen_Model_DeviceInstance::$KWH_Cost = $assessmentSettings->kilowattsPerHour;


        Proposalgen_Model_DeviceInstance::$ITCostPerPage = (($this->getAnnualITCost() * 0.5 + $this->getAnnualCostOfOutSourcing()) / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombined()->getYearly());
    }

    /**
     * @return Assessment_ViewModel_Ranking
     */
    public function getRanking ()
    {
        if (!isset($this->Ranking))
        {
            $this->Ranking = new Assessment_ViewModel_Ranking($this);
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->monochrome->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly();
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->color->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly();
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->monochrome->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly();
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->color->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly();
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->leasedDeviceInstances->getPageCounts()->monochrome->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly();
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->leasedDeviceInstances->getPageCounts()->color->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getYearly();
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
            $this->ExcludedDevices = array_merge($this->getDevices()->unmappedDeviceInstances->getDeviceInstances(), $this->getDevices()->excludedDeviceInstances->getDeviceInstances());
        }

        return $this->ExcludedDevices;
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

            $this->CombinedAnnualLeasePayments = $this->assessment->getAssessmentSettings()->monthlyLeasePayment * $this->getDevices()->leasedDeviceInstances->getCount() * 12;
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
            if ($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombined()->getYearly())
            {
                $this->PerPageLeaseCost = $this->getCombinedAnnualLeasePayments() / $this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombined()->getYearly();
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
     * @return Assessment_ViewModel_Assessment
     */
    public function setUser ($User)
    {
        $this->User = $User;

        return $this;
    }


    /**
     * @return float
     */
    public function getReportMargin ()
    {
        if (!isset($this->ReportMargin))
        {
            $this->ReportMargin = $this->assessment->getAssessmentSettings()->assessmentReportMargin;
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
            $this->LeasedBlackAndWhiteCharge = $this->assessment->getAssessmentSettings()->leasedBwCostPerPage;
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
            $this->LeasedColorCharge = $this->assessment->getAssessmentSettings()->leasedColorCostPerPage;
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
            $this->EstimatedAnnualCostOfLeaseMachines = $this->getCombinedAnnualLeasePayments() + ($this->getDevices()->leasedDeviceInstances->getPageCounts()->monochrome->getYearly() * $this->getLeasedBlackAndWhiteCharge()) + ($this->getDevices()->leasedDeviceInstances->getPageCounts()->color->getYearly() * $this->getLeasedColorCharge());
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

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
            {
                $totalAge += $device->getAge();
            }

            if ($this->getDevices()->purchasedDeviceInstances->getCount())
            {
                $averageAge                          = $totalAge / $this->getDevices()->purchasedDeviceInstances->getCount();
                $this->AnnualCostOfHardwarePurchases = ($this->getDevices()->allIncludedDeviceInstances->getCount() / $averageAge) * $this->assessment->getAssessmentSettings()->defaultPrinterCost;
            }
            else
            {
                $this->AnnualCostOfHardwarePurchases = 0;
            }
        }

        return $this->AnnualCostOfHardwarePurchases;
    }


    /**
     * Calculates the cost of ink and toner per month
     *
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return float
     */
    public function getCostOfInkAndTonerMonthly ($costPerPageSetting)
    {
        if (!isset($this->CostOfInkAndTonerMonthly))
        {
            // Calculate
            $totalCost = 0;
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
            {
                $totalCost += $device->getCostOfInkAndToner($costPerPageSetting, $this->getReportMargin());
            }
            $this->CostOfInkAndTonerMonthly = $totalCost;
        }

        return $this->CostOfInkAndTonerMonthly;
    }

    /**
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $maxVolume += $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer());
            }
            $this->MaximumMonthlyPrintVolume = $maxVolume;
        }

        return $this->MaximumMonthlyPrintVolume;
    }

    /**
     * @return int
     */
    public function calculateMaximumMonthlyPrintVolumeWithReplacements ()
    {
        $maxVolume = 0;
        foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
        {
            if ($deviceInstance->getReplacementMasterDevice())
            {
                $maxVolume += $deviceInstance->getReplacementMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer());
            }
            else
            {
                $maxVolume += $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer());
            }
        }

        return $maxVolume;
    }

    /**
     * @return int
     */
    public function getMaximumMonthlyPurchasedPrintVolume ()
    {
        if (!isset($this->_maximumMonthlyPurchasedPrintVolume))
        {
            $maxVolume = 0;
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $maxVolume += $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer());
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
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
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
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
     * Gets the amount of color capable devices with replacement devices
     *
     * @return int
     */
    public function getNumberOfColorCapableDevicesWithReplacements ()
    {
        $numberOfDevices = 0;
        foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
        {
            $replacementDevice = $device->getReplacementMasterDevice();
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
     * @return int
     */
    public function getNumberOfBlackAndWhiteCapableDevices ()
    {
        if (!isset($this->NumberOfBlackAndWhiteCapableDevices))
        {
            $this->NumberOfBlackAndWhiteCapableDevices = $this->getDevices()->allIncludedDeviceInstances->getCount() - $this->getNumberOfColorCapableDevices();
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
     * @return float
     */
    public function getAverageCostOfDevices ()
    {
        if (!isset($this->AverageCostOfDevices))
        {
            $this->AverageCostOfDevices = $this->assessment->getAssessmentSettings()->defaultPrinterCost;
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                if ($deviceInstance->getPageCounts()->getCombined()->getMonthly() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()) * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE))
                {
                    $devicesUnderusedCount++;
                }
            }
            $this->PercentDevicesUnderused = ($devicesUnderusedCount / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100;
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                if ($deviceInstance->getPageCounts()->getCombined()->getMonthly() > $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()))
                {
                    $devicesOverusedCount++;
                }
            }
            $this->PercentDevicesOverused = ($devicesOverusedCount / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100;
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                if ($deviceInstance->getPageCounts()->getCombined()->getMonthly() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()) * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE))
                {
                    $devicesArray[] = $deviceInstance;
                }
            }
            $this->_underutilizedDevices = $devicesArray;
        }

        return $this->_underutilizedDevices;
    }

    /**
     * @return \Proposalgen_Model_DeviceInstance[]
     */
    public function getLeastUsedDevices ()
    {
        if (!isset($this->LeastUsedDevices))
        {
            $deviceArray = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
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
        if ($deviceA->getUsage($this->getCostPerPageSettingForCustomer()) == $deviceB->getUsage($this->getCostPerPageSettingForCustomer()))
        {
            return 0;
        }

        return ($deviceA->getUsage($this->getCostPerPageSettingForCustomer()) < $deviceB->getUsage($this->getCostPerPageSettingForCustomer())) ? -1 : 1;
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
        if ($deviceA->getUsage($this->getCostPerPageSettingForCustomer()) == $deviceB->getUsage($this->getCostPerPageSettingForCustomer()))
        {
            return 0;
        }

        return ($deviceA->getUsage($this->getCostPerPageSettingForCustomer()) > $deviceB->getUsage($this->getCostPerPageSettingForCustomer())) ? -1 : 1;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getOptimizedDevices ()
    {
        if (!isset($this->_optimizedDevices))
        {
            $deviceArray = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {

                //Check to see if it is not underutilized
                if (($deviceInstance->getPageCounts()->getCombined()->getMonthly() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()) * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE)) == false)
                {
                    //Check to see if it is not overUtilized
                    if ($deviceInstance->getUsage($this->getCostPerPageSettingForCustomer()) < 1)
                    {

                        //Check to see if it is under the age requirements
                        if ($deviceInstance->getAge() < self::OLD_DEVICE_THRESHOLD)
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
            $deviceArray = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
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
            $this->PercentColorDevices = $this->getNumberOfColorCapableDevices() / $this->getDevices()->allIncludedDeviceInstances->getCount();
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $totalAge += $deviceInstance->getAge();
            }
            $this->AverageAgeOfDevices = $totalAge / $this->getDevices()->allIncludedDeviceInstances->getCount();
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
            $deviceArray = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
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
    public function getMonthlyHighCostColorDevices ($costPerPageSetting)
    {
        if (!isset($this->HighCostDevices))
        {
            $deviceArray = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
            $costArray   = array();
            /**@var $value Proposalgen_Model_DeviceInstance */
            foreach ($deviceArray as $key => $deviceInstance)
            {
                if ($deviceInstance->getMasterDevice()->isColor())
                {
                    $costArray[] = array($key, $deviceInstance->getPageCounts()->color->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->colorCostPerPage);
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getMonthlyHighCostPurchasedColorDevices (Proposalgen_Model_CostPerPageSetting $costPerPageSetting)
    {
        if (!isset($this->HighCostDevices))
        {
            $deviceArray = $this->getDevices()->purchasedDeviceInstances->getDeviceInstances();
            $costArray   = array();
            /**@var $value Proposalgen_Model_DeviceInstance */
            foreach ($deviceArray as $key => $deviceInstance)
            {
                if ($deviceInstance->getMasterDevice()->isColor())
                {
                    $costArray[] = array($key, $deviceInstance->getPageCounts()->color->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->colorCostPerPage);
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
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     *
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getMonthlyHighCostMonochromeDevices (Proposalgen_Model_CostPerPageSetting $costPerPageSetting)
    {
        if (!isset($this->HighCostMonochromeDevices))
        {
            $deviceArray = $this->getDevices()->purchasedDeviceInstances->getDeviceInstances();
            $costArray   = array();
            /**@var $value Proposalgen_Model_DeviceInstance */
            foreach ($deviceArray as $key => $deviceInstance)
            {
                $costArray[] = array($key, $deviceInstance->getPageCounts()->monochrome->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->monochromeCostPerPage);
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

            $deviceArray = $this->getDevices()->purchasedDeviceInstances->getDeviceInstances();
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
        if ($deviceA->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin()) == $deviceB->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin()))
        {
            return 0;
        }

        return ($deviceA->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin()) > $deviceB->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin())) ? -1 : 1;
    }

    /**
     * @return string
     */
    public function getDateReportPrepared ()
    {
        if (!isset($this->DateReportPrepared))
        {
            $report_date              = new DateTime($this->assessment->reportDate);
            $this->DateReportPrepared = date_format($report_date, 'F jS, Y');
        }

        return $this->DateReportPrepared;
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
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
                $totalPowerUsage = ($totalPowerUsage / $devicesReportingPower) * $this->getDevices()->allIncludedDeviceInstances->getCount();
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
            $this->LeastUsedDevicePercentage = $this->getLeastUsedDeviceCount() / $this->getDevices()->allIncludedDeviceInstances->getCount() * 100;
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
            $this->MostUsedDevicePercentage = $this->getMostUsedDeviceCount() / $this->getDevices()->allIncludedDeviceInstances->getCount() * 100;
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $cumulativeAge += $deviceInstance->getAge();
            }

            if ($cumulativeAge > 0)
            {
                $averageAge = $cumulativeAge / $this->getDevices()->allIncludedDeviceInstances->getCount();
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
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
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
        foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
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
        foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
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
            $this->PercentageOfDevicesReportingPower = $this->getNumberOfDevicesReportingPower() / $this->getDevices()->allIncludedDeviceInstances->getCount();
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
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
            {
                // Total cost += monochrome cost
                $totalCost->BlackAndWhite += $device->calculateMonthlyMonoCost($this->getCostPerPageSettingForDealer());
                $totalCost->Color += $device->calculateMonthlyColorCost($this->getCostPerPageSettingForDealer());
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
            $totalCost->BlackAndWhite = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->monochrome->getMonthly() * $this->getMPSBlackAndWhiteCPP();
            $totalCost->Color         = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->color->getMonthly() * $this->getMPSColorCPP();
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
            $this->NumberOfRepairs = $this->assessment->getSurvey()->averageMonthlyBreakdowns;
            if (!$this->NumberOfRepairs)
            {
                $this->NumberOfRepairs = $this->getDevices()->allIncludedDeviceInstances->getCount() * 0.05;
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
            $this->AverageTimeBetweenBreakdownAndFix = $this->assessment->getSurvey()->averageRepairTime;
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
            $this->PercentPrintingDoneOnInkjet = $this->assessment->getSurvey()->percentageOfInkjetPrintVolume;
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
            $deviceArraySortedByUsage       = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
            $deviceArraySortedByAge         = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
            $deviceArraySortedByRiskRanking = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
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
        if ($deviceA->getLifeUsage($this->getCostPerPageSettingForCustomer()) == $deviceB->getLifeUsage($this->getCostPerPageSettingForCustomer()))
        {
            return 0;
        }

        return ($deviceA->getLifeUsage($this->getCostPerPageSettingForCustomer()) < $deviceB->getLifeUsage($this->getCostPerPageSettingForCustomer())) ? -1 : 1;
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                if ($device->getAge() > self::OLD_DEVICE_THRESHOLD)
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
     * Gets the devices sorted by ascending age
     */
    public function getIncludedDevicesSortedAscendingByAge ()
    {
        if (!isset($this->_includedDevicesSortedAscendingByAge))
        {
            $devices = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                if ($device->getAge() >= self::OLD_DEVICE_LIST)
                {
                    $devices[] = $device;
                }
            }
            usort($devices, array(
                                 $this,
                                 "sortDevicesByAge"
                            ));
            $this->_includedDevicesSortedAscendingByAge = $devices;
        }

        return $this->_includedDevicesSortedAscendingByAge;
    }

    /**
     * Gets the devices sorted by descending age
     */
    public function getIncludedDevicesSortedDescendingByAge ()
    {
        if (!isset($this->_includedDevicesSortedDescendingByAge))
        {
            $devices = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                if ($device->getAge() >= self::OLD_DEVICE_LIST)
                {
                    $devices[] = $device;
                }
            }
            usort($devices, array(
                                 $this,
                                 "sortDevicesByAge"
                            ));
            $this->_includedDevicesSortedDescendingByAge = $devices;
        }

        return $this->_includedDevicesSortedDescendingByAge;
    }

    /**
     * @return float
     */
    public function getWeeklyITHours ()
    {
        if (!isset($this->WeeklyITHours))
        {
            $this->WeeklyITHours = $this->assessment->getSurvey()->hoursSpentOnIt;
            if (!$this->WeeklyITHours)
            {
                $this->WeeklyITHours = $this->getDevices()->allIncludedDeviceInstances->getCount() * 0.25;
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
            $this->AverageITRate = $this->assessment->getSurvey()->averageItHourlyRate;
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
            $companyName   = $this->assessment->getClient()->companyName;
            $employeeCount = $this->assessment->getClient()->employeeCount;

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
                                      "MPSToolbox"
                                 ));
            $barGraph->addAxisRange(0, 0, $highest * 1.3);
            $barGraph->setDataRange(0, $highest * 1.3);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("b");
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
            $highest  = ($this->getDevices()->leasedDeviceInstances->getCount() > $this->getDevices()->purchasedDeviceInstances->getCount()) ? $this->getDevices()->leasedDeviceInstances->getCount() : $this->getDevices()->purchasedDeviceInstances->getCount();
            $barGraph = new gchart\gBarChart(225, 265);
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $this->getDevices()->leasedDeviceInstances->getCount()
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $this->getDevices()->purchasedDeviceInstances->getCount()
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
            $highest  = ($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombined()->getMonthly() > $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombined()->getMonthly()) ? $this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombined()->getMonthly() : $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombined()->getMonthly();
            $barGraph = new gchart\gBarChart(225, 265);

            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       round($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombined()->getMonthly())
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       round($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombined()->getMonthly())
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
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
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
            $averagePageCount = round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getCount(), 0);
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
            $pagesPerEmployee = round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getMonthly() / $employeeCount);
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
            $devicesPerEmployee = round($employeeCount / $this->getDevices()->allIncludedDeviceInstances->getCount(), 2);
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

            /**
             * -- Color Capable Devices Graph
             */
            $colorPercentage = 0;
            if ($this->getDevices()->allIncludedDeviceInstances->getCount())
            {
                $colorPercentage = round((($this->getNumberOfColorCapableDevices() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getMonthly() > 0)
            {
                $colorPercentage = round((($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->color->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getMonthly()) * 100), 2);
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
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
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
                    $percentage     = round(($count / $this->getDevices()->purchasedDeviceInstances->getCount()) * 100, 2);
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
            if ($this->getDevices()->allIncludedDeviceInstances->getCount())
            {
                $scanPercentage = round((($this->getNumberOfScanCapableDevices() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
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
            if ($this->getDevices()->allIncludedDeviceInstances->getCount())
            {
                $faxPercentage = round((($this->getNumberOfFaxCapableDevices() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
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
            if ($this->getDevices()->allIncludedDeviceInstances->getCount())
            {
                $duplexPercentage = round((($this->getNumberOfDuplexCapableDevices() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
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
     * @return Assessment_ViewModel_Assessment
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
            $this->CostOfExecutingSuppliesOrders = $this->assessment->getSurvey()->costToExecuteSuppliesOrder * $this->assessment->getSurvey()->numberOfSupplyOrdersPerMonth * 12;
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
            $this->AnnualCostOfOutSourcing = $this->assessment->getSurvey()->costOfLabor;
            if ($this->AnnualCostOfOutSourcing === null)
            {
                $this->AnnualCostOfOutSourcing = $this->getDevices()->purchasedDeviceInstances->getCount() * 200;
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
            $costWithNoInkToner             = $this->getTotalPurchasedAnnualCost() - $this->getCostOfInkAndToner($this->getCostPerPageSettingForCustomer());
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombined()->getYearly())
            {
                $workingCPP = $costWithNoInkToner / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombined()->getYearly();
            }
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
            {
                $costOfBlackAndWhiteInkAndToner += $device->getCostOfBlackAndWhiteInkAndToner($this->getCostPerPageSettingForCustomer(), $this->getReportMargin());
            }
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->monochrome->getYearly())
            {
                $monochromeCostPerPage = $workingCPP + ($costOfBlackAndWhiteInkAndToner / ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->monochrome->getYearly() / 12));
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
            $costWithNoInkToner     = $this->getTotalPurchasedAnnualCost() - $this->getCostOfInkAndToner($this->getCostPerPageSettingForCustomer());
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombined()->getYearly())
            {
                $workingCPP = $costWithNoInkToner / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombined()->getYearly();
            }
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
            {
                $costOfColorInkAndToner += $device->getCostOfColorInkAndToner($this->getCostPerPageSettingForCustomer(), $this->getReportMargin());
            }
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->color->getYearly())
            {
                $ColorCPP = $workingCPP + ($costOfColorInkAndToner / ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->color->getYearly() / 12));
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
            $this->MPSBlackAndWhiteCPP = $this->assessment->getAssessmentSettings()->mpsBwCostPerPage;
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
            $this->MPSColorCPP = $this->assessment->getAssessmentSettings()->mpsColorCostPerPage;
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
            $this->InternalAdminCost = $this->assessment->getSurvey()->costToExecuteSuppliesOrder * 12;
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
            $this->PrintIQTotalCost = $this->getInternalAdminCost() + ($this->getAnnualITCost() * 0.5) + ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->color->getYearly() * $this->getMPSColorCPP()) + ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->monochrome->getYearly() * $this->getMPSBlackAndWhiteCPP()) + $this->getAnnualCostOfHardwarePurchases();
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
            $this->NumberOfOrdersPerMonth = $this->assessment->getSurvey()->numberOfSupplyOrdersPerMonth;
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
            $this->EmployeeCount = $this->assessment->getClient()->employeeCount;
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $totalWatts += $deviceInstance->getMasterDevice()->wattsPowerNormal;
            }
            $this->AverageOperatingWatts = ($totalWatts > 0) ? $totalWatts / $this->getDevices()->allIncludedDeviceInstances->getCount() : 0;
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
            $this->ReplacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->fetchCheapestForEachCategory($this->assessment->getClient()->dealerId);
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
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
            {
                $deviceReplaced = false;
                if ($device->getPageCounts()->getCombined()->getMonthly() >= $ampvThreshold)
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
                                $savings = $device->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin()) - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_BW_MFP]->monthlyRate;
                                // MFP
                                if ($savings >= $minimumSavings)
                                {
                                    $replacedDevices->BlackAndWhiteMFP [] = $device;
                                    $deviceReplaced                       = true;
                                }
                            }
                            else
                            {
                                $savings = $device->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin()) - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_BW]->monthlyRate;
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
                                $savings = $device->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin()) - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR_MFP]->monthlyRate;
                                if ($savings >= $minimumSavings)
                                {
                                    $replacedDevices->ColorMFP [] = $device;
                                    $deviceReplaced               = true;
                                }
                            }
                            else
                            {

                                $savings = $device->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin()) - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACEMENT_COLOR]->monthlyRate;
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
                $pageCount += $deviceInstance->getPageCounts()->monochrome->getMonthly();
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
                $pageCount += $deviceInstance->getPageCounts()->color->getMonthly();
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
                $cost += $deviceInstance->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin());
            }

            foreach ($this->getDevicesToBeReplaced()->BlackAndWhiteMFP as $deviceInstance)
            {
                $cost += $deviceInstance->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin());
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
                $cost += $deviceInstance->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin());
            }
            foreach ($this->getDevicesToBeReplaced()->ColorMFP as $deviceInstance)
            {
                $cost += $deviceInstance->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin());
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
                $cost += $deviceInstance->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin());
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
                $cost += $deviceInstance->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin());
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
                $cost += $deviceInstance->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin());
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
                $cost += $deviceInstance->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin());
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
            $this->CostOfExecutingSuppliesOrder = $this->assessment->getSurvey()->costToExecuteSuppliesOrder * $this->getNumberOfAnnualInkTonerOrders();
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
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->monochrome->getMonthly() > 0)
            {
                $this->GrossMarginWeightedCPP->BlackAndWhite = $this->getGrossMarginTotalMonthlyCost()->BlackAndWhite / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->monochrome->getMonthly();
            }
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->color->getMonthly() > 0)
            {
                $this->GrossMarginWeightedCPP->Color = $this->getGrossMarginTotalMonthlyCost()->Color / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->color->getMonthly();
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
            $this->GrossMarginColorMargin = ($this->getMPSColorCPP() - $this->getGrossMarginWeightedCPP()->Color) / $this->getMPSColorCPP() * 100;;
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
                $deviceToners = $masterDevice->getTonersForAssessment($this->getCostPerPageSettingForCustomer());
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
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
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
                $deviceToners = $masterDevice->getTonersForAssessment($this->getCostPerPageSettingForCustomer());
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
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
        $totalDeviceCount = $this->getDevices()->allIncludedDeviceInstances->getCount();
        if ($totalDeviceCount > 0)
        {
            $deviceCount = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
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


        foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
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
        $deviceCount = $this->getDevices()->purchasedDeviceInstances->getCount();
        if ($deviceCount > 0)
        {
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $totalAge += $deviceInstance->getAge();
            }
            $averageAge = $totalAge / $deviceCount;
        }

        return $averageAge;
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
     * The weighted average monthly cost per page for customers
     *
     * @var Proposalgen_Model_CostPerPage
     */
    protected $_customerWeightedAverageMonthlyCostPerPage;

    /**
     * Calculates the weighted average monthly cost per page of the current fleet
     *
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateCustomerWeightedAverageMonthlyCostPerPage ()
    {
        if (!isset($this->_customerWeightedAverageMonthlyCostPerPage))
        {
            $this->_customerWeightedAverageMonthlyCostPerPage = new Proposalgen_Model_CostPerPage();

            $costPerPageSetting            = $this->getCostPerPageSettingForCustomer();
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

            $this->_customerWeightedAverageMonthlyCostPerPage->monochromeCostPerPage = $monoCpp;
            $this->_customerWeightedAverageMonthlyCostPerPage->colorCostPerPage      = $colorCpp;
        }

        return $this->_customerWeightedAverageMonthlyCostPerPage;
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
                $this->_dealerMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getPageCounts()->monochrome->getMonthly() * $this->assessment->getAssessmentSettings()->targetMonochromeCostPerPage;
                $this->_dealerMonthlyRevenueUsingTargetCostPerPage += $deviceInstance->getPageCounts()->color->getMonthly() * $this->assessment->getAssessmentSettings()->targetColorCostPerPage;
            }
        }

        return $this->_dealerMonthlyRevenueUsingTargetCostPerPage;
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
     * Calculates the dealers monthly profit when using a target cost per page schema and replacement devices
     *
     * @return number
     */
    public function calculateDealerMonthlyProfitUsingTargetCostPerPageAndReplacements ()
    {
        return $this->calculateDealerMonthlyRevenueUsingTargetCostPerPage() - $this->calculateDealerMonthlyCostWithReplacements();
    }

    /**
     * The dealers monthly cost with replacements
     *
     * @var number
     */
    protected $_dealerMonthlyCostWithReplacements;

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
                $this->_dealerMonthlyCostWithReplacements += $deviceInstance->calculateMonthlyCost($costPerPageSetting, $deviceInstance->getReplacementMasterDevice());
            }
        }

        return $this->_dealerMonthlyCostWithReplacements;
    }

    /**
     * The weighted average monthly cost per page when using replacements
     *
     * @var Proposalgen_Model_CostPerPage
     */
    protected $_dealerWeightedAverageMonthlyCostPerPageWithReplacements;

    /**
     * calculate Estimated Annual Cost Of Printing
     *
     * @return float
     */
    public function calculateEstimatedAnnualCostOfPrinting ()
    {
        return $this->getEstimatedAnnualCostOfLeaseMachines() + $this->getTotalPurchasedAnnualCost();
    }

    /**
     * Calculates Total Cost Of Monochrome pages for purchased devices
     *
     * @return float
     */
    public function calculateTotalCostOfMonochromePagesAnnually ()
    {
        return $this->getDevices()->purchasedDeviceInstances->getPageCounts()->monochrome->getYearly() * $this->getMPSBlackAndWhiteCPP();
    }

    /**
     * Calculates Total Cost Of Color pages for purchased devices
     *
     * @return float
     */
    public function calculateTotalCostOfColorPagesAnnually ()
    {
        return $this->getDevices()->purchasedDeviceInstances->getPageCounts()->color->getYearly() * $this->getMPSColorCPP();
    }

    /**
     * Calculates half of annual it cost
     *
     * @return float
     */
    public function getHalfOfAnnualITCost ()
    {
        return $this->getAnnualITCost() * .5;
    }

    /**
     * Calculates the average pages per device monthly
     *
     * @return float
     */
    public function calculateAveragePagesPerDeviceMonthly ()
    {
        return $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombined()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getCount();
    }
}