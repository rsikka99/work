<?php
use CpChart\Services\pChartFactory;
use MPSToolbox\Legacy\Models\UserModel;
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DealerMasterDeviceAttributeModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\PageCountsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsExcludedRowModel;

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
    /**
     * Average Monthly Pages Per Employee
     */
    const AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE = 200;
    /**
     * Average Monthly Pages Per Device
     */
    const AVERAGE_MONTHLY_PAGES_PER_DEVICE = 1500;

    /**
     * AVERAGE NUMBER OF EMPLOYEES PER DEVICE
     */
    const AVERAGE_EMPLOYEES_PER_DEVICE = 4.4;

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
    protected $NumberOfOrdersPerMonth;
    protected $EmployeeCount;
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
    protected $_cachedGrossMarginTotalMonthlyCost;
    protected $_totalLeaseBuybackPrice;

    public $highCostPurchasedDevices;

    /**
     * Used for passing to phpWord docs as it requires full path to request image
     *
     * @var \PhpOffice\PhpWord\Shared\String
     */
    protected $absoluteGraphPaths;

    /**
     * Likely can get rid of this
     *
     * @var
     */
    protected $pImageGraphs = [];

    /**
     * @param AssessmentModel $report
     */
    public function __construct (AssessmentModel $report)
    {
        parent::__construct($report);

        if (isset(self::$_instance))
        {
            self::$_instance = $this;
        }

        // Get the report settings
        DeviceInstanceModel::$KWH_Cost = $this->assessment->getClient()->getClientSettings()->genericSettings->defaultEnergyCost;
        if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly() > 0)
        {
            DeviceInstanceModel::$ITCostPerPage = (($this->getAnnualITCost() * 0.5 + $this->getAnnualCostOfOutSourcing()) / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly());
        }
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly();
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly();
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly();
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly();
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->leasedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly();
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
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly() > 0)
            {
                $percentage = $this->getDevices()->leasedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly();
            }
            $this->YearlyLeasedColorPercentage = $percentage;
        }

        return $this->YearlyLeasedColorPercentage;
    }

    /**
     * @return RmsExcludedRowModel[]|DeviceInstanceModel[]
     */
    public function getExcludedDevices ()
    {
        if (!isset($this->ExcludedDevices))
        {
            $unmappedDevices = $this->getDevices()->unmappedDeviceInstances->getDeviceInstances();
            foreach ($unmappedDevices as $deviceInstance)
            {
                $deviceInstance->_exclusionReason = "Not Mapped";
            }

            $excludedDevices = $this->getDevices()->excludedDeviceInstances->getDeviceInstances();
            foreach ($excludedDevices as $deviceInstance)
            {
                $deviceInstance->_exclusionReason = "Manually Excluded";
            }

            $this->ExcludedDevices = array_merge($unmappedDevices, $excludedDevices);
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
    public function getCombinedAnnualLeasePayments ()
    {
        if (!isset($this->CombinedAnnualLeasePayments))
        {

            $this->CombinedAnnualLeasePayments = $this->assessment->getClient()->getClientSettings()->genericSettings->defaultMonthlyLeasePayment * $this->getDevices()->leasedDeviceInstances->getCount() * 12;
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
            if ($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly())
            {
                $this->PerPageLeaseCost = $this->getCombinedAnnualLeasePayments() / $this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly();
            }
        }

        return $this->PerPageLeaseCost;
    }

    /**
     * @return UserModel|null
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
     * @param UserModel $User
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
            $this->ReportMargin = $this->assessment->getClient()->getClientSettings()->genericSettings->tonerPricingMargin;
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
            $this->LeasedBlackAndWhiteCharge = $this->assessment->getClient()->getClientSettings()->genericSettings->leasedMonochromeCostPerPage;
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
            $this->LeasedColorCharge = $this->assessment->getClient()->getClientSettings()->genericSettings->leasedColorCostPerPage;
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
            $this->EstimatedAnnualCostOfLeaseMachines = $this->getCombinedAnnualLeasePayments() + ($this->getDevices()->leasedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly() * $this->getLeasedBlackAndWhiteCharge()) + ($this->getDevices()->leasedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly() * $this->getLeasedColorCharge());
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
                $this->AnnualCostOfHardwarePurchases = ($this->getDevices()->allIncludedDeviceInstances->getCount() / $averageAge) * $this->assessment->getClient()->getClientSettings()->genericSettings->defaultPrinterCost;
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
     * @param CostPerPageSettingModel $costPerPageSetting
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
                $totalCost += $device->getCostOfInkAndToner($costPerPageSetting);
            }
            $this->CostOfInkAndTonerMonthly = $totalCost;
        }

        return $this->CostOfInkAndTonerMonthly;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
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
                if ($deviceInstance->getMasterDevice()->isMfp())
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
                if ($deviceInstance->getMasterDevice()->isMfp())
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
                $maxVolume += $deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume;
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
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $maxVolume += $deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume;
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
                if ($device->getMasterDevice()->tonerConfigId != TonerConfigModel::BLACK_ONLY)
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
                if ($device->getMasterDevice()->tonerConfigId != TonerConfigModel::BLACK_ONLY)
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
     * @return PageCountsModel
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
            $this->AverageCostOfDevices = $this->assessment->getClient()->getClientSettings()->genericSettings->defaultPrinterCost;
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
                if ($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() < ($deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE))
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
                if ($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() > $deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume)
                {
                    $devicesOverusedCount++;
                }
            }
            $this->PercentDevicesOverused = ($devicesOverusedCount / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100;
        }

        return $this->PercentDevicesOverused;
    }

    /**
     * @return DeviceInstanceModel[]
     */
    public function getUnderutilizedDevices ()
    {
        if (!isset($this->_underutilizedDevices))
        {
            $devicesArray = [];
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                if ($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() < ($deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE))
                {
                    $devicesArray[] = $deviceInstance;
                }
            }
            $this->_underutilizedDevices = $devicesArray;
        }

        return $this->_underutilizedDevices;
    }

    /**
     * @return \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel[]
     */
    public function getLeastUsedDevices ()
    {
        if (!isset($this->LeastUsedDevices))
        {
            $deviceArray = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
            usort($deviceArray, [
                $this,
                "ascendingSortDevicesByUsage"
            ]);
            $numberOfDevices = count($deviceArray);
            if ($numberOfDevices > 1)
            {
                $deviceArray = [
                    $deviceArray [0],
                    $deviceArray [1]
                ];
            }
            else if ($numberOfDevices > 0)
            {
                $deviceArray = [
                    $deviceArray [0]
                ];
            }
            else
            {
                $deviceArray = [];
            }

            $this->LeastUsedDevices = $deviceArray;
        }

        return $this->LeastUsedDevices;
    }

    /**
     * Callback function for uSort when we want to sort a device based on usage
     *
     * @param $deviceA \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel
     * @param $deviceB \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel
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
     * @param \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel $deviceA
     * @param \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel $deviceB
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
     * @return DeviceInstanceModel[]
     */
    public function getOptimizedDevices ()
    {
        if (!isset($this->_optimizedDevices))
        {
            $deviceArray = [];
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {

                //Check to see if it is not underutilized
                if (($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() < ($deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE)) == false)
                {
                    //Check to see if it is not overUtilized
                    if ($deviceInstance->getUsage($this->getCostPerPageSettingForCustomer()) < 1)
                    {

                        //Check to see if it is under the age requirements
                        if ($deviceInstance->getAge() < self::OLD_DEVICE_THRESHOLD)
                        {
                            //Check to see if it is reporting toner levels
                            if ($deviceInstance->isCapableOfReportingTonerLevels || $deviceInstance->isLeased)
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
     * @return DeviceInstanceModel[]
     */
    public function getMostUsedDevices ()
    {
        if (!isset($this->MostUsedDevices))
        {
            $deviceArray = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
            usort($deviceArray, [
                $this,
                "descendingSortDevicesByUsage"
            ]);

            $numberOfDevices = count($deviceArray);
            if ($numberOfDevices > 1)
            {
                $deviceArray = [
                    $deviceArray [0],
                    $deviceArray [1]
                ];
            }
            else if ($numberOfDevices > 0)
            {
                $deviceArray = [
                    $deviceArray [0]
                ];
            }
            else
            {
                $deviceArray = [];
            }
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
     * @return DeviceInstanceModel[]
     */
    public function getHighPowerConsumptionDevices ()
    {
        if (!isset($this->HighPowerConsumptionDevices))
        {
            $deviceArray = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
            usort($deviceArray, [
                $this,
                "ascendingSortDevicesByPowerConsumption"
            ]);
            $this->HighPowerConsumptionDevices = $deviceArray;
        }

        return $this->HighPowerConsumptionDevices;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return DeviceInstanceModel[]
     */
    public function getMonthlyHighCostColorDevices ($costPerPageSetting)
    {
        if (!isset($this->HighCostDevices))
        {
            $deviceArray = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
            $costArray   = [];
            /**@var $value DeviceInstanceModel */
            foreach ($deviceArray as $key => $deviceInstance)
            {
                if ($deviceInstance->getMasterDevice()->isColor())
                {
                    $costArray[] = [$key, $deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->colorCostPerPage];
                }
            }

            usort($costArray, [
                $this,
                "descendingSortDevicesByColorCost"
            ]);
            $highCostDevices = [];
            foreach ($costArray as $costs)
            {
                $highCostDevices[] = $deviceArray[$costs[0]];
            }
            $this->HighCostDevices = $highCostDevices;
        }

        return $this->HighCostDevices;
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
                "descendingSortDevicesByColorCost"
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
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return DeviceInstanceModel[]
     */
    public function getMonthlyHighCostPurchasedColorDevices (CostPerPageSettingModel $costPerPageSetting)
    {
        if (!isset($this->HighCostDevices))
        {
            $deviceArray = $this->getDevices()->purchasedDeviceInstances->getDeviceInstances();
            $costArray   = [];
            /**@var $value DeviceInstanceModel */
            foreach ($deviceArray as $key => $deviceInstance)
            {
                if ($deviceInstance->getMasterDevice()->isColor())
                {
                    $costArray[] = [$key, $deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->colorCostPerPage];
                }
            }

            usort($costArray, [
                $this,
                "descendingSortDevicesByColorCost"
            ]);
            $highCostDevices = [];
            foreach ($costArray as $costs)
            {
                $highCostDevices[] = $deviceArray[$costs[0]];
            }
            $this->HighCostDevices = $highCostDevices;
        }

        return $this->HighCostDevices;
    }

    /**
     * @param CostPerPageSettingModel $costPerPageSetting
     *
     * @return DeviceInstanceModel[]
     */
    public function getMonthlyHighCostMonochromeDevices (CostPerPageSettingModel $costPerPageSetting)
    {
        if (!isset($this->HighCostMonochromeDevices))
        {
            $deviceArray = $this->getDevices()->purchasedDeviceInstances->getDeviceInstances();
            $costArray   = [];
            /**@var $value DeviceInstanceModel */
            foreach ($deviceArray as $key => $deviceInstance)
            {
                $costArray[] = [$key, $deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->monochromeCostPerPage];
            }

            usort($costArray, [
                $this,
                "descendingSortDevicesByColorCost"
            ]);
            $highCostDevices = [];
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
     * @param DeviceInstanceModel $deviceA
     * @param DeviceInstanceModel $deviceB
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
     * @return DeviceInstanceModel[]
     */
    public function getMostExpensiveDevices ()
    {
        if (!isset($this->MostExpensiveDevices))
        {

            $deviceArray = $this->getDevices()->purchasedDeviceInstances->getDeviceInstances();
            usort($deviceArray, [
                $this,
                "ascendingSortDevicesByMonthlyCost"
            ]);
            $this->MostExpensiveDevices = $deviceArray;
        }

        return $this->MostExpensiveDevices;
    }

    /**
     * Callback function for uSort when we want to sort a device based on
     * monthly cost
     *
     * @param DeviceInstanceModel $deviceA
     * @param DeviceInstanceModel $deviceB
     *
     * @return int
     */
    public function ascendingSortDevicesByMonthlyCost ($deviceA, $deviceB)
    {
        if ($deviceA->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin()) == $deviceB->getMonthlyRate($this->getCostPerPageSettingForCustomer()))
        {
            return 0;
        }

        return ($deviceA->getMonthlyRate($this->getCostPerPageSettingForCustomer(), $this->getReportMargin()) > $deviceB->getMonthlyRate($this->getCostPerPageSettingForCustomer())) ? -1 : 1;
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
            $this->AveragePowerCostPerMonth = $this->getAveragePowerUsagePerMonth() * DeviceInstanceModel::getKWH_Cost();

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
            $uniqueModelArray = [];
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
     * @return DeviceInstanceModel[]
     */
    public function getDevicesReportingTonerLevels ()
    {
        $devicesReportingTonerLevels = [];
        foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
        {
            if ($device->isCapableOfReportingTonerLevels)
            {
                $devicesReportingTonerLevels[] = $device;
            }
        }

        return $devicesReportingTonerLevels;
    }

    /**
     * @return DeviceInstanceModel[]
     */
    public function getDevicesNotReportingTonerLevels ()
    {
        $devicesNotReportingTonerLevels = [];
        foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
        {
            if ($device->isCapableOfReportingTonerLevels == false)
            {
                $devicesNotReportingTonerLevels[] = $device;
            }
        }

        return $devicesNotReportingTonerLevels;
    }

    /**
     * @return DeviceInstanceModel[]
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
     * @return DeviceInstanceModel[]
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
     * @param null|CostPerPageSettingModel $costPerPageSetting
     *
     * @return stdClass
     */
    public function getGrossMarginTotalMonthlyCost ($costPerPageSetting = null)
    {
        $costPerPageSetting = ($costPerPageSetting == null ? $this->getCostPerPageSettingForDealer() : $costPerPageSetting);

        if (!isset($this->_cachedGrossMarginTotalMonthlyCost))
        {
            $this->_cachedGrossMarginTotalMonthlyCost = [];
        }

        $cacheKey = $costPerPageSetting->createCacheKey();

        if (!array_key_exists($cacheKey, $this->_cachedGrossMarginTotalMonthlyCost))
        {
            $totalCost                = new stdClass();
            $totalCost->BlackAndWhite = 0;
            $totalCost->Color         = 0;
            $totalCost->Combined      = 0;
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
            {
                // Total cost += monochrome cost
                $totalCost->BlackAndWhite += $device->calculateMonthlyMonoCost(($costPerPageSetting == null ? $this->getCostPerPageSettingForDealer() : $costPerPageSetting));
                $totalCost->Color += $device->calculateMonthlyColorCost(($costPerPageSetting == null ? $this->getCostPerPageSettingForDealer() : $costPerPageSetting));
            }
            $totalCost->Combined                                  = $totalCost->BlackAndWhite + $totalCost->Color;
            $this->_cachedGrossMarginTotalMonthlyCost [$cacheKey] = $totalCost;
        }

        return $this->_cachedGrossMarginTotalMonthlyCost [$cacheKey];
    }

    /**
     * @return stdClass
     */
    public function getGrossMarginTotalMonthlyRevenue ()
    {
        if (!isset($this->GrossMarginTotalMonthlyRevenue))
        {
            $totalCost = new stdClass();
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly() > 0)
            {
                $totalCost->BlackAndWhite = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly() * $this->getMPSBlackAndWhiteCPP();
            }
            else
            {
                $totalCost->BlackAndWhite = 0;
            }
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() > 0)
            {
                $totalCost->Color = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() * $this->getMPSColorCPP();
            }
            else
            {
                $totalCost->Color = 0;
            }
            $totalCost->Combined = $totalCost->BlackAndWhite + $totalCost->Color;

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
            $this->NumberOfRepairs = $this->assessment->getClient()->getSurvey()->averageMonthlyBreakdowns;
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
            $this->AverageTimeBetweenBreakdownAndFix = $this->assessment->getClient()->getSurvey()->averageRepairTime;
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
            $this->PercentPrintingDoneOnInkjet = $this->assessment->getClient()->getSurvey()->percentageOfInkjetPrintVolume;
        }

        return $this->PercentPrintingDoneOnInkjet;
    }

    /**
     * @return DeviceInstanceModel[]
     */
    public function getHighRiskDevices ()
    {
        if (!isset($this->HighRiskDevices))
        {
            $deviceArraySortedByUsage       = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
            $deviceArraySortedByAge         = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
            $deviceArraySortedByRiskRanking = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
            usort($deviceArraySortedByUsage, [
                $this,
                "sortDevicesByLifeUsage"
            ]);
            usort($deviceArraySortedByAge, [
                $this,
                "sortDevicesByAge"
            ]);
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
            usort($deviceArraySortedByRiskRanking, [
                $this,
                "sortDevicesByRiskRanking"
            ]);
            $this->HighRiskDevices = $deviceArraySortedByRiskRanking;
        }

        return $this->HighRiskDevices;
    }

    /**
     * Callback function for uSort when we want to sort devices based on life
     * usage
     *
     * @param DeviceInstanceModel $deviceA
     * @param DeviceInstanceModel $deviceB
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
     * @param DeviceInstanceModel $deviceA
     * @param DeviceInstanceModel $deviceB
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
     * @param DeviceInstanceModel $deviceA
     * @param DeviceInstanceModel $deviceB
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
            $devices = [];
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                if ($device->getAge() > self::OLD_DEVICE_THRESHOLD)
                {
                    $devices[] = $device;
                }
            }
            usort($devices, [
                $this,
                "sortDevicesByAge"
            ]);
            $this->_oldDevices = $devices;
        }

        return $this->_oldDevices;
    }

    /**
     * Gets the devices sorted by ascending age
     *
     * @return DeviceInstanceModel[]
     */
    public function getIncludedDevicesSortedAscendingByAge ()
    {
        if (!isset($this->_includedDevicesSortedAscendingByAge))
        {
            $devices = [];
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                if ($device->getAge() >= self::OLD_DEVICE_LIST)
                {
                    $devices[] = $device;
                }
            }
            usort($devices, [
                $this,
                "sortDevicesByAge"
            ]);
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
            $devices = [];
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                if ($device->getAge() >= self::OLD_DEVICE_LIST)
                {
                    $devices[] = $device;
                }
            }
            usort($devices, [
                $this,
                "sortDevicesByAge"
            ]);
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
            $this->WeeklyITHours = $this->assessment->getClient()->getSurvey()->hoursSpentOnIt;
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
            $this->AverageITRate = $this->assessment->getClient()->getSurvey()->averageItHourlyRate;
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
     * @return \CpChart\Classes\pImage
     * @throws Exception
     */
    public function getThisGraph ()
    {
        $dealerBranding    = My_Brand::getDealerBranding();
        $hexToRGBConverter = new \Tangent\Format\HexToRGB();
        $highest           = 100;
        $factory           = new pChartFactory();

        $MyData = $factory->newData();

        $MyData->addPoints([$this->getDevices()->leasedDeviceInstances->getCount()], "Number of leased devices");
        $MyData->addPoints([$this->getDevices()->purchasedDeviceInstances->getCount()], "Number of purchased devices");

        //Fixes x access scale appearing - hacky - needs fixing
        $MyData->addPoints([""], "Printer Types");
        $MyData->setSerieDescription("Printer Types", "Type");
        $MyData->setAbscissa("Printer Types");

        $leasedColor           = $dealerBranding->graphLeasedDeviceColor;
        $purchasedColor        = $dealerBranding->graphPurchasedDeviceColor;
        $leasedRGB             = $hexToRGBConverter->hexToRgb($leasedColor);
        $purchasedRGB          = $hexToRGBConverter->hexToRgb($purchasedColor);
        $leasedColorSetting    = ['R' => $leasedRGB['r'], 'G' => $leasedRGB['g'], 'B' => $leasedRGB['b']];
        $purchasedColorSetting = ['R' => $purchasedRGB['r'], 'G' => $purchasedRGB['g'], 'B' => $purchasedRGB['b']];
        $MyData->setPalette("Number of leased devices", $leasedColorSetting);
        $MyData->setPalette("Number of purchased devices", $purchasedColorSetting);

        $myPicture = $factory->newImage(680, 260, $MyData);

        $myPicture->Antialias = false;

        $myPicture->setFontProperties(["FontName" => "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);

        $myPicture->setGraphArea(60, 40, 200, 200);

        $AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
        $ScaleSettings  = ["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "DrawSubTicks" => false, "DrawArrows" => false, "ArrowSize" => 6];

        $myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

        /* Write the chart legend - this sets the x/y position */
        $myPicture->drawLegend(60, 220, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);

        /* Draw the chart */
        $myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);

        return $myPicture;
    }

    /**
     * @return array|\CpChart\Classes\pImage
     */
    public function getNewGraphs ()
    {
        $newGraphs[] = $this->getThisGraph();
        $newGraphs[] = $this->getThisGraph();

        return $newGraphs;

    }

    /**
     * This function will eventually be renamed getGraphs() and take its place
     * It will use the new pChart library to create pImages and returns an array
     */
    public function getTheGraphs ()
    {
        $dealerBranding    = My_Brand::getDealerBranding();
        $hexToRGBConverter = new \Tangent\Format\HexToRGB();
        $factory           = new pChartFactory();

        /**
         * -- PrintIQSavingsBarGraph
         */
        $MyData  = $factory->newData();
        $highest = ($this->getPrintIQTotalCost() > $this->getTotalPurchasedAnnualCost() ? $this->getPrintIQTotalCost() : $this->getTotalPurchasedAnnualCost());

        $MyData->addPoints([$this->getTotalPurchasedAnnualCost(), $this->getPrintIQTotalCost()], "Annual Printing Costs for Purchased Hardware");
        $MyData->setAxisPosition(0, AXIS_POSITION_BOTTOM);
        $MyData->setAxisName(0, "");
        $MyData->addPoints(["Current", My_Brand::getDealerBranding()->mpsProgramName], "Costs");
        $MyData->setSerieDescription("Costs", "Costs");
        $MyData->setAxisDisplay(0, AXIS_FORMAT_CUSTOM, "formatDisplayCurrency");
        $MyData->setAbscissa("Costs");

        $myPicture = new \CpChart\Classes\pImage(650, 160, $MyData);
        $myPicture->setFontProperties(["FontName" => "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);

        /* Turn off Antialiasing */
        $myPicture->Antialias = false;

        $myPicture->drawText(225, 35, "Annual Printing Costs for Purchased Hardware", ["FontSize" => 11, "R" => 0, "G" => 0, "B" => 0]);

        $axisBoundaries = [0 => ["Min" => 0, "Max" => $highest], 1 => ["Min" => 0, "Max" => $highest]];

        $myPicture->setGraphArea(100, 70, 750, 175);
        $myPicture->drawScale(["DrawSubTicks" => false, "Pos" => SCALE_POS_TOPBOTTOM, "Mode" => SCALE_MODE_MANUAL, "ManualScale" => $axisBoundaries, "TickR" => 127, "TickG" => 127, "TickB" => 127, "MinDivHeight" => 100, "OuterTickWidth" => 0, "InnerTickWidth" => 3, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127]);

        // Set the colors of the graph bars
        $negColor = $hexToRGBConverter->hexToRgb($dealerBranding->graphNegativeColor);
        $posColor = $hexToRGBConverter->hexToRgb($dealerBranding->graphPositiveColor);
        $palette  = ["0" => ['R' => $negColor['r'], 'G' => $negColor['g'], 'B' => $negColor['b']], ['R' => $posColor['r'], 'G' => $posColor['g'], 'B' => $posColor['b']],];

        /* Draw the chart */
        $myPicture->drawBarChart(["DisplayPos" => LABEL_POS_RIGHT, "DisplayValues" => true, "OverrideColors" => $palette, "Surrounding" => 0]);

        $this->pImageGraphs[] = $myPicture;


        /*********** Leased Vs Purchased Bar Graph ****************************/

        $highest = ($this->getDevices()->leasedDeviceInstances->getCount() > $this->getDevices()->purchasedDeviceInstances->getCount()) ? $this->getDevices()->leasedDeviceInstances->getCount() : $this->getDevices()->purchasedDeviceInstances->getCount();
        $MyData  = $factory->newData();
        $MyData->addPoints([$this->getDevices()->leasedDeviceInstances->getCount()], "Number of leased devices");
        $MyData->addPoints([$this->getDevices()->purchasedDeviceInstances->getCount()], "Number of purchased devices");

        //Fixes x access scale appearing - hacky - needs fixing
        $MyData->addPoints([""], "Printer Types");
        $MyData->setSerieDescription("Printer Types", "Type");
        $MyData->setAbscissa("Printer Types");

        $leasedRGB             = $hexToRGBConverter->hexToRgb($dealerBranding->graphLeasedDeviceColor);
        $purchasedRGB          = $hexToRGBConverter->hexToRgb($dealerBranding->graphPurchasedDeviceColor);
        $leasedColorSetting    = ['R' => $leasedRGB['r'], 'G' => $leasedRGB['g'], 'B' => $leasedRGB['b']];
        $purchasedColorSetting = ['R' => $purchasedRGB['r'], 'G' => $purchasedRGB['g'], 'B' => $purchasedRGB['b']];
        $MyData->setPalette("Number of leased devices", $leasedColorSetting);
        $MyData->setPalette("Number of purchased devices", $purchasedColorSetting);

        $myPicture            = $factory->newImage(265, 265, $MyData);
        $myPicture->Antialias = false;
        $myPicture->setFontProperties(["FontName" => "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
        $myPicture->setGraphArea(60, 10, 200, 200);
        $AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
        $myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

        /* Write the chart legend - this sets the x/y position */
        $myPicture->drawLegend(40, 220, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
        $myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);

        $this->pImageGraphs[] = $myPicture;

        /************ Leased Vs Purchased Page Count Bar Graph ****************/

        $highest = ($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() > $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) ? $this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() : $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
        $MyData  = $factory->newData();
        $MyData->addPoints([round($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())], "Monthly pages on leased devices");
        $MyData->addPoints([round($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())], 'Monthly pages on purchased devices');

        //Fixes x access scale appearing - hacky - needs fixing
        $MyData->addPoints([""], "Printer Types");
        $MyData->setSerieDescription("Printer Types", "Type");
        $MyData->setAbscissa("Printer Types");

        $leasedRGB             = $hexToRGBConverter->hexToRgb($dealerBranding->graphLeasedDeviceColor);
        $purchasedRGB          = $hexToRGBConverter->hexToRgb($dealerBranding->graphPurchasedDeviceColor);
        $leasedColorSetting    = ['R' => $leasedRGB['r'], 'G' => $leasedRGB['g'], 'B' => $leasedRGB['b']];
        $purchasedColorSetting = ['R' => $purchasedRGB['r'], 'G' => $purchasedRGB['g'], 'B' => $purchasedRGB['b']];
        $MyData->setPalette("Monthly pages on leased devices", $leasedColorSetting);
        $MyData->setPalette("Monthly pages on purchased devices", $purchasedColorSetting);

        $myPicture            = $factory->newImage(265, 265, $MyData);
        $myPicture->Antialias = false;
        $myPicture->setFontProperties(["FontName" => "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
        $myPicture->setGraphArea(60, 10, 200, 200);
        $AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
        $myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);
        /* Write the chart legend - this sets the x/y position */
        $myPicture->drawLegend(40, 220, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
        $myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);

        $this->pImageGraphs[] = $myPicture;

        /*********** Number of Printing Device Models and Supply Types ********/

        $uniqueModelArray = [];
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
            $abscissaArray[] = $device->assetId;
        }
        $MyData = $factory->newData();
        $MyData->addPoints($uniqueModelArray, 'Unique models');
        $MyData->setSerieDescription("ScoreA", "Application A");

        /* Define the absissa serie */
        $MyData->addPoints($uniqueModelArray, "Labels");
        $MyData->setAbscissa("Labels");

        $myPicture = $factory->newImage(700, 270, $MyData, true);
        $myPicture->setFontProperties(["FontName" => "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);

        $colorArray = ["E21736", "b0bb21", "5c3f9b", "0191d3", "f89428", "e4858f", "fcc223", "B3C6FF", "ECFFB3", "386AFF", "FFB3EC", "cccccc", "00ff00", "000000", "E21736", "b0bb21", "5c3f9b", "0191d3", "f89428",
                       "e4858f", "fcc223", "B3C6FF", "ECFFB3", "386AFF", "FFB3EC", "cccccc", "00ff00", "000000"];

        $PieChart = $factory->newChart("pie", $myPicture, $MyData);

        for ($i = 0; $i < count($uniqueModelArray); $i++)
        {
            $hexColor = $hexToRGBConverter->hexToRgb($colorArray[$i]);
            $PieChart->setSliceColor(0, ["R" => $hexColor['r'], "G" => $hexColor['g'], "B" => $hexColor['b']]);
        }

        $PieChart->draw3DPie(280, 125, ["SecondPass" => true, "Radius" => 170]);

        $this->pImageGraphs[] = $myPicture;


        // Return the pImages[]
        return $this->pImageGraphs;

    }

    /**
     * @return array
     */
    public function getGraphs ()
    {

        $this->getThisGraph();

        if (!isset($this->Graphs))
        {
            $dealerBranding = My_Brand::getDealerBranding();

            // Other variables used in several places
            $companyName   = mb_strimwidth($this->assessment->getClient()->companyName, 0, 23, "...");
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
            $barGraph->setVisibleAxes([
                'x'
            ]);
            $barGraph->addDataSet([
                $this->getTotalPurchasedAnnualCost()
            ]);
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphNegativeColor),
            ]);
            $barGraph->addDataSet([
                $this->getPrintIQTotalCost()
            ]);
            $barGraph->setLegend([
                "Current",
                My_Brand::getDealerBranding()->mpsProgramName
            ]);
            $barGraph->addAxisRange(0, 0, $highest * 1.3);
            $barGraph->setDataRange(0, $highest * 1.3);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("b");
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphPositiveColor),
            ]);
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
            $barGraph->setVisibleAxes([
                'y'
            ]);
            $barGraph->addDataSet([
                $this->getDevices()->leasedDeviceInstances->getCount()
            ]);
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphLeasedDeviceColor),
            ]);
            $barGraph->addDataSet([
                $this->getDevices()->purchasedDeviceInstances->getCount()
            ]);
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(50, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphPurchasedDeviceColor),
            ]);
            $barGraph->setLegend([
                "Number of leased devices",
                "Number of purchased devices"
            ]);
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[1]
            $this->Graphs [] = $barGraph->getUrl();

            /**
             * -- LeasedVsPurchasedPageCountBarGraph
             */
            $highest  = ($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() > $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) ? $this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() : $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            $barGraph = new gchart\gBarChart(225, 265);

            $barGraph->setVisibleAxes([
                'y'
            ]);
            $barGraph->addDataSet([
                round($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())
            ]);
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphLeasedDeviceColor),
            ]);
            $barGraph->addDataSet([
                round($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())
            ]);
            $barGraph->addAxisRange(0, 0, $highest * 1.20);
            $barGraph->setDataRange(0, $highest * 1.20);
            $barGraph->setBarScale(50, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphPurchasedDeviceColor),
            ]);
            $barGraph->setLegend([
                "Monthly pages on leased devices",
                "Monthly pages on purchased devices"
            ]);

            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[2]
            $this->Graphs [] = $barGraph->getUrl();

            /**
             * -- UniqueDevicesGraph
             */
            $uniqueModelArray = [];
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
            $uniqueDevicesGraph->addColors([
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
            ]);
//             $uniqueDevicesGraph->setLegend($labels);
//            $uniqueDevicesGraph->setLabels($labels);
            // Graphs[3]
            $this->Graphs [] = $uniqueDevicesGraph->getUrl();

            /**
             * -- AverageMonthlyPagesBarGraph
             */
            $averagePageCount = round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getCount(), 0);
            $highest          = ($averagePageCount > self::AVERAGE_MONTHLY_PAGES_PER_DEVICE) ? $averagePageCount : self::AVERAGE_MONTHLY_PAGES_PER_DEVICE;
            $barGraph         = new gchart\gBarChart(175, 300);
            $barGraph->setTitle("Average Monthly Pages|per Networked Printer");
            $barGraph->setVisibleAxes([
                'y'
            ]);
            $barGraph->addDataSet([
                $averagePageCount
            ]);
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphCustomerColor),
            ]);
            $barGraph->addDataSet([
                self::AVERAGE_MONTHLY_PAGES_PER_DEVICE
            ]);
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphIndustryAverageColor),
            ]);
            $barGraph->setLegend([
                $companyName,
                "Average"
            ]);

            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[4]
            $this->Graphs [] = $barGraph->getUrl();

            /**
             * -- AverageMonthlyPagesPerEmployeeBarGraph
             */
            $pagesPerEmployee = ($employeeCount > 0) ? round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $employeeCount) : 0;
            $highest          = (Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE > $pagesPerEmployee) ? Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE : $pagesPerEmployee;
            $barGraph         = new gchart\gBarChart(175, 300);
            $barGraph->setTitle("Average Monthly Pages|per Employee");
            $barGraph->setVisibleAxes([
                'y'
            ]);
            $barGraph->addDataSet([
                $pagesPerEmployee
            ]);
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphCustomerColor),
            ]);
            $barGraph->addDataSet([
                Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE
            ]);
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphIndustryAverageColor),
            ]);
            $barGraph->setLegend([
                $companyName,
                "Average"
            ]);
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[5]
            $this->Graphs [] = $barGraph->getUrl();

            /**
             * -- AverageMonthlyPagesPerEmployeeBarGraph
             */
            $devicesPerEmployee = round($employeeCount / $this->getDevices()->allIncludedDeviceInstances->getCount(), 2);
            $highest            = ($devicesPerEmployee > self::AVERAGE_EMPLOYEES_PER_DEVICE) ? $devicesPerEmployee : self::AVERAGE_EMPLOYEES_PER_DEVICE;
            $barGraph           = new gchart\gBarChart(175, 300);
            $barGraph->setTitle("Employees per|Printing Device");
            $barGraph->setVisibleAxes([
                'y'
            ]);
            $barGraph->addDataSet([
                $devicesPerEmployee
            ]);
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphCustomerColor),
            ]);
            $barGraph->addDataSet([
                self::AVERAGE_EMPLOYEES_PER_DEVICE
            ]);
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors([
                str_replace('#', '', $dealerBranding->graphIndustryAverageColor),
            ]);
            $barGraph->setLegend([
                $companyName,
                "Average"
            ]);
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
            $colorCapableGraph->addDataSet([
                $colorPercentage,
                $notColorPercentage
            ]);
            $colorCapableGraph->setLegend([
                "Color-capable",
                "Black-and-white only"
            ]);
            $colorCapableGraph->setLabels([
                "$colorPercentage%"
            ]);
            $colorCapableGraph->addColors([
                str_replace('#', '', $dealerBranding->graphColorDeviceColor),
                str_replace('#', '', $dealerBranding->graphMonoDeviceColor),
            ]);
            $colorCapableGraph->setLegendPosition("bv");
            // Graphs[7]
            $this->Graphs [] = $colorCapableGraph->getUrl();

            /**
             * -- ColorVSBWPagesGraph
             */
            $colorPercentage = 0;
            if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() > 0)
            {
                $colorPercentage = round((($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) * 100), 2);
            }

            $bwPercentage        = 100 - $colorPercentage;
            $colorVSBWPagesGraph = new gchart\gPie3DChart(305, 210);
            $colorVSBWPagesGraph->setTitle("Color vs Black/White Pages");
            $colorVSBWPagesGraph->addDataSet([
                $colorPercentage,
                $bwPercentage
            ]);
            $colorVSBWPagesGraph->setLegend([
                "Color pages printed",
                "Black-and-white pages printed"
            ]);
            $colorVSBWPagesGraph->setLabels([
                "$colorPercentage%",
                "$bwPercentage%"
            ]);
            $colorVSBWPagesGraph->addColors([
                str_replace('#', '', $dealerBranding->graphColorDeviceColor),
                str_replace('#', '', $dealerBranding->graphMonoDeviceColor),
            ]);
            $colorVSBWPagesGraph->setLegendPosition("bv");
            // Graphs[8]
            $this->Graphs [] = $colorVSBWPagesGraph->getUrl();

            /**
             * -- Device Ages Graph
             */
            $deviceAges = [
                "Less than 5 years old" => 0,
                "5-6 years old"         => 0,
                "7-8 years old"         => 0,
                "More than 8 years old" => 0
            ];
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
            $dataSet     = [];
            $legendItems = [];
            $labels      = [];

            foreach ($deviceAges as $legendItem => $count)
            {
                $legendItems [] = $legendItem;
                $dataSet []     = $count;
                if ($count > 0)
                {
                    $percentage = round(($count / $this->getDevices()->purchasedDeviceInstances->getCount()) * 100, 2);
                    $labels []  = "$percentage%";
                }
            }
            $deviceAgeGraph = new gchart\gPie3DChart(350, 200);
            $deviceAgeGraph->addDataSet($dataSet);
            $deviceAgeGraph->setLegend($legendItems);
            $deviceAgeGraph->setLabels($labels);
            $deviceAgeGraph->addColors([
                str_replace('#', '', $dealerBranding->graphAgeOfDevices1),
                str_replace('#', '', $dealerBranding->graphAgeOfDevices2),
                str_replace('#', '', $dealerBranding->graphAgeOfDevices3),
                str_replace('#', '', $dealerBranding->graphAgeOfDevices4),
            ]);
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
            $scanCapableGraph->addDataSet([
                $scanPercentage,
                $notScanPercentage
            ]);
            $scanCapableGraph->setLegend([
                "Scan-capable",
                "Not scan-capable"
            ]);
            $scanCapableGraph->setLabels([
                "$scanPercentage%"
            ]);
            $scanCapableGraph->addColors([
                str_replace('#', '', $dealerBranding->graphCopyCapableDeviceColor),
                str_replace('#', '', $dealerBranding->graphNotCompatibleDeviceColor),
            ]);
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
            $faxCapable->addDataSet([
                $faxPercentage,
                $notFaxPercentage
            ]);
            $faxCapable->setLegend([
                "Fax-capable",
                "Not fax-capable"
            ]);
            $faxCapable->setLabels([
                "$faxPercentage%"
            ]);
            $faxCapable->addColors([
                str_replace('#', '', $dealerBranding->graphFaxCapableDeviceColor),
                str_replace('#', '', $dealerBranding->graphNotCompatibleDeviceColor),
            ]);
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
            $duplexCapableGraph->addDataSet([
                $duplexPercentage,
                $notDuplexPercentage
            ]);
            $duplexCapableGraph->setLegend([
                "Duplex-capable",
                "Not duplex-capable"
            ]);
            $duplexCapableGraph->setLabels([
                "$duplexPercentage%"
            ]);
            $duplexCapableGraph->addColors([
                str_replace('#', '', $dealerBranding->graphDuplexCapableDeviceColor),
                str_replace('#', '', $dealerBranding->graphNotCompatibleDeviceColor),
            ]);
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
     * @param $newGraphs
     *
     * @return $this
     */
    public function setNewGraphs ($newGraphs)
    {
        $this->newGraphs = $newGraphs;

        return $this;
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
            $this->CostOfExecutingSuppliesOrders = $this->assessment->getClient()->getSurvey()->costToExecuteSuppliesOrder * $this->assessment->getClient()->getSurvey()->numberOfSupplyOrdersPerMonth * 12;
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
            $this->AnnualCostOfOutSourcing = $this->assessment->getClient()->getSurvey()->costOfLabor;
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
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly())
            {
                $workingCPP = $costWithNoInkToner / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly();
            }
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
            {
                $costOfBlackAndWhiteInkAndToner += $device->getCostOfBlackAndWhiteInkAndToner($this->getCostPerPageSettingForCustomer());
            }
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly())
            {
                $monochromeCostPerPage = $workingCPP + ($costOfBlackAndWhiteInkAndToner / ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly() / 12));
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
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly())
            {
                $workingCPP = $costWithNoInkToner / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly();
            }
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
            {
                $costOfColorInkAndToner += $device->getCostOfColorInkAndToner($this->getCostPerPageSettingForCustomer());
            }
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly())
            {
                $ColorCPP = $workingCPP + ($costOfColorInkAndToner / ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly() / 12));
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
            $this->MPSBlackAndWhiteCPP = $this->assessment->getClient()->getClientSettings()->genericSettings->mpsMonochromeCostPerPage;
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
            $this->MPSColorCPP = $this->assessment->getClient()->getClientSettings()->genericSettings->mpsColorCostPerPage;
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
            $this->InternalAdminCost = $this->assessment->getClient()->getSurvey()->costToExecuteSuppliesOrder * 12;
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
            $this->PrintIQTotalCost = $this->getInternalAdminCost() + ($this->getAnnualITCost() * 0.5) + ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly() * $this->getMPSColorCPP()) + ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly() * $this->getMPSBlackAndWhiteCPP()) + $this->getAnnualCostOfHardwarePurchases();
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
            $this->NumberOfOrdersPerMonth = $this->assessment->getClient()->getSurvey()->numberOfSupplyOrdersPerMonth;
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
     * @return float
     */
    public function getCostOfExecutingSuppliesOrder ()
    {
        if (!isset($this->CostOfExecutingSuppliesOrder))
        {
            $this->CostOfExecutingSuppliesOrder = $this->assessment->getClient()->getSurvey()->costToExecuteSuppliesOrder * $this->getNumberOfAnnualInkTonerOrders();
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
     * @param null|CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function getGrossMarginMonthlyProfit ($costPerPageSetting = null)
    {
        if (!isset($this->GrossMarginMonthlyProfit))
        {
            $this->GrossMarginMonthlyProfit = $this->getGrossMarginTotalMonthlyRevenue()->Combined - $this->getGrossMarginTotalMonthlyCost($costPerPageSetting)->Combined;
        }

        return $this->GrossMarginMonthlyProfit;
    }

    /**
     * @param null|CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function getGrossMarginOverallMargin ($costPerPageSetting = null)
    {
        if (!isset($this->GrossMarginOverallMargin))
        {
            if ($this->getGrossMarginTotalMonthlyRevenue()->Combined > 0)
            {

                $this->GrossMarginOverallMargin = \Tangent\Accounting::reverseEngineerMargin($this->getGrossMarginTotalMonthlyCost($costPerPageSetting)->Combined, $this->getGrossMarginTotalMonthlyRevenue()->Combined);
            }
            else
            {
                $this->GrossMarginOverallMargin = 0;
            }
        }

        return $this->GrossMarginOverallMargin;
    }

    /**
     * @param null|CostPerPageSettingModel $costPerPageSetting
     *
     * @return stdClass
     */
    public function getGrossMarginWeightedCPP ($costPerPageSetting = null)
    {
        if (!isset($this->GrossMarginWeightedCPP))
        {
            $this->GrossMarginWeightedCPP                = new stdClass();
            $this->GrossMarginWeightedCPP->BlackAndWhite = 0;
            $this->GrossMarginWeightedCPP->Color         = 0;
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly() > 0)
            {
                $this->GrossMarginWeightedCPP->BlackAndWhite = $this->getGrossMarginTotalMonthlyCost($costPerPageSetting)->BlackAndWhite / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly();
            }
            if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() > 0)
            {
                $this->GrossMarginWeightedCPP->Color = $this->getGrossMarginTotalMonthlyCost()->Color / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly();
            }
        }

        return $this->GrossMarginWeightedCPP;
    }

    /**
     * @param null|CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function getGrossMarginBlackAndWhiteMargin ($costPerPageSetting = null)
    {
        if (!isset($this->GrossMarginBlackAndWhiteMargin))
        {
            $this->GrossMarginBlackAndWhiteMargin = 0;

            if ($this->getMPSBlackAndWhiteCPP() > 0)
            {
                $this->GrossMarginBlackAndWhiteMargin = \Tangent\Accounting::reverseEngineerMargin($this->getGrossMarginWeightedCPP($costPerPageSetting)->BlackAndWhite, $this->getMPSBlackAndWhiteCPP());

            }
        }

        return $this->GrossMarginBlackAndWhiteMargin;
    }

    /**
     * @param null|CostPerPageSettingModel $costPerPageSetting
     *
     * @return float
     */
    public function getGrossMarginColorMargin ($costPerPageSetting = null)
    {
        if (!isset($this->GrossMarginColorMargin))
        {
            $this->GrossMarginColorMargin = 0;

            if ($this->getMPSColorCPP() > 0)
            {
                $this->GrossMarginColorMargin = Tangent\Accounting::reverseEngineerMargin($this->getGrossMarginWeightedCPP($costPerPageSetting)->Color, $this->getMPSColorCPP());
            }
        }

        return $this->GrossMarginColorMargin;
    }

    /**
     * @return TonerModel[]
     */
    public function getUniqueTonerList ()
    {
        if (!isset($this->UniqueTonerList))
        {
            $uniqueToners = [];
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
     * @return MasterDeviceModel[]
     */
    public function getUniquePurchasedDeviceList ()
    {
        if (!isset($this->UniquePurchasedDeviceList))
        {
            $masterDevices = [];
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
     * @return TonerModel[]
     */
    public function getUniquePurchasedTonerList ()
    {
        if (!isset($this->UniquePurchasedTonerList))
        {
            $uniqueToners = [];
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
     * @return MasterDeviceModel[]
     */
    public function getUniqueDeviceList ()
    {
        if (!isset($this->UniqueDeviceList))
        {
            $masterDevices = [];
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
                if ($deviceInstance->isCapableOfReportingTonerLevels)
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
        $score                      = 1;

        $hasMonoDevices               = false;
        $hasColorDevices              = false;
        $hasThreeColorCombinedDevices = false;
        $hasFourColorCombinedDevices  = false;


        foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
        {
            switch ($deviceInstance->getMasterDevice()->tonerConfigId)
            {
                case TonerConfigModel::BLACK_ONLY:
                    $maximumNumberOfSupplyTypes += 1;
                    $hasMonoDevices = true;
                    break;
                case TonerConfigModel::THREE_COLOR_SEPARATED:
                    $maximumNumberOfSupplyTypes += 4;
                    $hasColorDevices = true;
                    break;
                case TonerConfigModel::THREE_COLOR_COMBINED:
                    $maximumNumberOfSupplyTypes += 2;
                    $hasThreeColorCombinedDevices = true;
                    break;
                case TonerConfigModel::FOUR_COLOR_COMBINED:
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
            $trueMax = $maximumNumberOfSupplyTypes - $minimumNumberOfSupplyTypes;
            if ($trueMax > 0)
            {
                $score = ($currentNumberOfSupplyTypes - $minimumNumberOfSupplyTypes) / ($maximumNumberOfSupplyTypes - $minimumNumberOfSupplyTypes);
            }
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
     * @return CostPerPageModel
     */
    public function calculateDealerWeightedAverageMonthlyCostPerPage ()
    {
        if (!isset($this->_dealerWeightedAverageMonthlyCostPerPage))
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
                $monoCpp += ($deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly() / $totalMonthlyMonoPagesPrinted) * $costPerPage->monochromeCostPerPage;
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
     * The weighted average monthly cost per page for customers
     *
     * @var CostPerPageModel
     */
    protected $_customerWeightedAverageMonthlyCostPerPage;

    /**
     * Calculates the weighted average monthly cost per page of the current fleet
     *
     * @return CostPerPageModel
     */
    public function calculateCustomerWeightedAverageMonthlyCostPerPage ()
    {
        if (!isset($this->_customerWeightedAverageMonthlyCostPerPage))
        {
            $this->_customerWeightedAverageMonthlyCostPerPage = new CostPerPageModel();

            $costPerPageSetting            = $this->getCostPerPageSettingForCustomer();
            $totalMonthlyMonoPagesPrinted  = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly();
            $totalMonthlyColorPagesPrinted = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly();
            $colorCpp                      = 0;
            $monoCpp                       = 0;

            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $costPerPage = $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage();
                if ($totalMonthlyMonoPagesPrinted > 0)
                {
                    $monoCpp += ($deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly() / $totalMonthlyMonoPagesPrinted) * $costPerPage->monochromeCostPerPage;
                }
                if ($totalMonthlyColorPagesPrinted > 0 && $deviceInstance->getMasterDevice()->isColor())
                {
                    $colorCpp += ($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() / $totalMonthlyColorPagesPrinted) * $costPerPage->colorCostPerPage;
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
     * The dealers monthly cost with replacements
     *
     * @var number
     */
    protected $_dealerMonthlyCostWithReplacements;

    /**
     * The weighted average monthly cost per page when using replacements
     *
     * @var CostPerPageModel
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
        return $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getYearly() * $this->getMPSBlackAndWhiteCPP();
    }

    /**
     * Calculates Total Cost Of Color pages for purchased devices
     *
     * @return float
     */
    public function calculateTotalCostOfColorPagesAnnually ()
    {
        return $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getYearly() * $this->getMPSColorCPP();
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
        return $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getCount();
    }

    /**
     * Calculates the total price of devices with a lease buyback
     *
     * @return float
     */
    public function getTotalLeaseBuybackPrice ()
    {
        if (!isset($this->_totalLeaseBuybackPrice))
        {

            $totalBuybackPrice = 0;

            /**
             * @var $deviceInstance DeviceInstanceModel
             */
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $dealerMasterDeviceAttributes = $deviceInstance->getMasterDevice()->getDealerAttributes();
                if ($dealerMasterDeviceAttributes instanceof DealerMasterDeviceAttributeModel && $dealerMasterDeviceAttributes->leaseBuybackPrice != null && $dealerMasterDeviceAttributes->leaseBuybackPrice >= 0)
                {
                    $totalBuybackPrice += $dealerMasterDeviceAttributes->leaseBuybackPrice;
                }
            }

            $this->_totalLeaseBuybackPrice = $totalBuybackPrice;
        }

        return $this->_totalLeaseBuybackPrice;
    }

    /**
     * Used by pCharts for formatting axis
     *
     * @param $value
     *
     * @return string
     */
    function formatDisplayCurrency ($value)
    {
        return "$" . number_format($value, 0, null, ",");
    }

}