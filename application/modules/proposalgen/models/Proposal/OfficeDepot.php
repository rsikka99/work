<?php

/**
 * Proposalgen_Model_Proposal_OfficeDepot
 *
 * @author Lee Robert
 * @version v1.0
 */
class Proposalgen_Model_Proposal_OfficeDepot extends Tangent_Model_Abstract
{
    public static $Proposal;
    
    // New Separated Proposal
    protected $Ranking;
    protected $Report;
    protected $ReportId;
    protected $ReportQuestions;
    protected $DefaultToners;
    protected $Devices;
    protected $ExcludedDevices;
    protected $LeasedDevices;
    protected $PurchasedDevices;
    protected $User;
    protected $DealerCompany;
    protected $CompanyMargin;
    protected $ReportMargin;
    protected $PageCoverageBlackAndWhite;
    protected $PageCoverageColor;
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
    protected $MostExpensiveDevices;
    protected $DateReportPrepared;
    protected $AveragePowerUsagePerMonth;
    protected $AveragePowerCostPerMonth;
    protected $AverageDeviceAge;
    protected $PercentageOfDevicesReportingPower;
    protected $NumberOfDevicesReportingPower;
    protected $GrossMarginTotalMonthlyCost;
    protected $GrossMarginTotalMonthlyRevenue;
    protected $DevicesReportingPowerThreshold;
    protected $NumberOfRepairs;
    protected $AverageTimeBetweenBreakdownAndFix;
    protected $AnnualDowntimeFromBreakdowns;
    protected $NumberOfVendors;
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

    /**
     * ALWAYS LEAVE THIS AT THE TOP OF THE FUNCTION
     *
     * @see Proposalgen_Model_Abstract::echoAll()
     */
    public function echoAll ()
    {
        echo "<pre style='font-size: 14px;'>";
        $methods = get_class_methods($this);
        foreach ( $methods as $method )
        {
            if (strpos($method, "get") === 0 && strpos($method, "getDebug") !== 0)
            {
                $key = substr($method, 3);
                $value = $this->$method();
                if ($key == "PageCounts" || $key == "Percentages")
                {
                    echo "$key = ";
                    print_r($value);
                    echo "\n";
                    continue;
                }
                if (isset($value))
                {
                    echo "<strong>";
                }
                echo "$key => ";
                
                if ($value instanceof stdClass)
                    echo "stdClass";
                else if (is_array($value))
                {
                    echo " =>";
                    foreach ( $value as $subkey => $subvalue )
                    {
                        echo "\n\t\t\t";
                        echo "$subkey => ";
                        echo "$subvalue";
                    }
                }
                else
                    echo "$value";
                
                echo "\n";
                if (isset($value))
                {
                    echo "</strong>";
                }
            }
        }
        echo "</pre>";
    }

    /**
     * Initialize the proposal
     *
     * @param $user Proposalgen_Model_User            
     * @param $dealerCompany Proposalgen_Model_DealerCompany            
     * @param $report Proposalgen_Model_Report            
     * @param $options array            
     */
    public function __construct (Proposalgen_Model_User $user, Proposalgen_Model_DealerCompany $dealerCompany, Proposalgen_Model_Report $report, array $options = null)
    {
        parent::__construct($options);
        $this->User = $user;
        $this->DealerCompany = $dealerCompany;
        
        if (isset(self::$Proposal))
        {
            self::$Proposal = $this;
        }
        $this->setReport($report);
        $this->setReportId($report->getReportId());
        
        // Initialize Settings
        

        // Set Page Coverage
        Proposalgen_Model_Toner::setESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE($this->getPageCoverageBlackAndWhite() / 100);
        Proposalgen_Model_Toner::setESTIMATED_PAGE_COVERAGE_COLOR($this->getPageCoverageColor() / 100);
        
        // Gross Margin Report Page Coverage
        Proposalgen_Model_Toner::setACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE($report->getReportActualPageCoverageMono() / 100);
        Proposalgen_Model_Toner::setACTUAL_PAGE_COVERAGE_COLOR($report->getReportActualPageCoverageColor() / 100);
        
        Proposalgen_Model_DeviceInstance::setKWH_Cost($report->ReportKilowattsPerHour);
        Proposalgen_Model_MasterDevice::setPricingConfig($report->getPricingConfig());
        Proposalgen_Model_MasterDevice::setGrossMarginPricingConfig($report->getGrossMarginPricingConfig());
        Proposalgen_Model_MasterDevice::setReportMargin(1 - ((((int)$report->getReportPricingMargin())) / 100));
    }

    /**
     *
     * @return the $Ranking
     */
    public function getRanking ()
    {
        if (! isset($this->Ranking))
        {
            $this->Ranking = new Proposalgen_Model_Proposal_Ranking($this);
        }
        return $this->Ranking;
    }

    /**
     *
     * @param $Ranking field_type            
     */
    public function setRanking ($Ranking)
    {
        $this->Ranking = $Ranking;
        return $this;
    }

    /**
     *
     * @return the $YearlyBlackAndWhitePercentage
     */
    public function getYearlyBlackAndWhitePercentage ()
    {
        if (! isset($this->YearlyBlackAndWhitePercentage))
        {
            $percentage = 0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
                $percentage = $this->getPageCounts()->Total->BlackAndWhite->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            $this->YearlyBlackAndWhitePercentage = $percentage;
        }
        return $this->YearlyBlackAndWhitePercentage;
    }

    /**
     *
     * @return the $YearlyColorPercentage
     */
    public function getYearlyColorPercentage ()
    {
        if (! isset($this->YearlyColorPercentage))
        {
            $percentage = 0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
                $percentage = $this->getPageCounts()->Total->Color->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            $this->YearlyColorPercentage = $percentage;
        }
        return $this->YearlyColorPercentage;
    }

    /**
     *
     * @return the $YearlyPurchasedBlackAndWhitePercentage
     */
    public function getYearlyPurchasedBlackAndWhitePercentage ()
    {
        if (! isset($this->YearlyPurchasedBlackAndWhitePercentage))
        {
            $percentage = 0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
                $percentage = $this->getPageCounts()->Purchased->BlackAndWhite->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            $this->YearlyPurchasedBlackAndWhitePercentage = $percentage;
        }
        return $this->YearlyPurchasedBlackAndWhitePercentage;
    }

    /**
     *
     * @return the $YearlyPurchasedColorPercentage
     */
    public function getYearlyPurchasedColorPercentage ()
    {
        if (! isset($this->YearlyPurchasedColorPercentage))
        {
            $percentage = 0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
                $percentage = $this->getPageCounts()->Purchased->Color->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            $this->YearlyPurchasedColorPercentage = $percentage;
        }
        return $this->YearlyPurchasedColorPercentage;
    }

    /**
     *
     * @return the $YearlyLeasedBlackAndWhitePercentage
     */
    public function getYearlyLeasedBlackAndWhitePercentage ()
    {
        if (! isset($this->YearlyLeasedBlackAndWhitePercentage))
        {
            $percentage = 0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
                $percentage = $this->getPageCounts()->Leased->BlackAndWhite->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            $this->YearlyLeasedBlackAndWhitePercentage = $percentage;
        }
        return $this->YearlyLeasedBlackAndWhitePercentage;
    }

    /**
     *
     * @return the $YearlyLeasedColorPercentage
     */
    public function getYearlyLeasedColorPercentage ()
    {
        if (! isset($this->YearlyLeasedColorPercentage))
        {
            $percentage = 0;
            if ($this->getPageCounts()->Total->Combined->Yearly > 0)
                $percentage = $this->getPageCounts()->Leased->Color->Yearly / $this->getPageCounts()->Total->Combined->Yearly;
            $this->YearlyLeasedColorPercentage = $percentage;
        }
        return $this->YearlyLeasedColorPercentage;
    }

    /**
     *
     * @return the Customer Address
     */
    public function getCustomerAddress ()
    {
        $reportQuestions = $this->getReportQuestions();
        return $reportQuestions [30]->getTextualAnswer();
    }

    /**
     *
     * @return Proposalgen_Model_Report $Report
     */
    public function getReport ()
    {
        if (! isset($this->Report))
        {
            throw new Exception("No Report Provided!");
        }
        return $this->Report;
    }

    /**
     *
     * @param $Report Proposalgen_Model_Report            
     */
    public function setReport ($Report)
    {
        $this->Report = $Report;
        return $this;
    }

    /**
     *
     * @return the $ReportId
     */
    public function getReportId ()
    {
        if (! isset($this->ReportId))
        {
            // Report Id MUST be set before anything tries to use it.
            throw new Exception("ReportId not set in Abstract");
        }
        return $this->ReportId;
    }

    /**
     *
     * @param $ReportId field_type            
     */
    public function setReportId ($ReportId)
    {
        $this->ReportId = $ReportId;
        return $this;
    }

    /**
     *
     * @return the $ReportQuestions
     */
    public function getReportQuestions ()
    {
        if (! isset($this->ReportQuestions))
        {
            $questionSetMapper = Proposalgen_Model_Mapper_QuestionSet::getInstance();
            $this->ReportQuestions = $questionSetMapper->getQuestionSetQuestions($this->getReport()->QuestionSetId, $this->ReportId);
        }
        return $this->ReportQuestions;
    }

    /**
     *
     * @param $ReportQuestions field_type            
     */
    public function setReportQuestions ($ReportQuestions)
    {
        $this->ReportQuestions = $ReportQuestions;
        return $this;
    }

    /**
     *
     * @return Proposalgen_Model_DeviceInstance[] $Devices
     */
    public function getDevices ()
    {
        if (! isset($this->Devices))
        {
            // Calculating margin:
            $report = $this->getReport();
            $reportMargin = $this->getReportMargin();
            $companyMargin = $this->getCompanyMargin();
            $deviceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
            
            // Known Devices
            $knownDevices = $deviceMapper->fetchAll(array (
                    "report_id = ?" => $this->ReportId, 
                    "is_excluded = ?" => 0 
            ));
            
            // Unknown Devices
            $unknowndeviceMapper = Proposalgen_Model_Mapper_UnknownDeviceInstance::getInstance();
            $unknownDevices = $unknowndeviceMapper->fetchAllUnknownDevicesAsKnownDevices($this->ReportId, array (
                    "report_id = ?" => $this->ReportId, 
                    "is_excluded = ?" => 0 
            ));
            
            // Merge the two
            $this->Devices = array_merge($knownDevices, $unknownDevices);
            
            if (count($this->Devices) <= 0)
            {
                throw new Exception("There were no devices associated with this report.");
            }
            
            // Get any overriden prices
            foreach ( $this->Devices as $device )
            {
                Proposalgen_Model_DeviceInstance::processOverrides($device, $report, $reportMargin, $companyMargin);
            } // endforeach
            

            /**
             * * Calculate IT CPP **
             */
            Proposalgen_Model_DeviceInstance::setITCPP(($this->getAnnualITCost() * 0.5 + $this->getAnnualCostOfOutSourcing()) / $this->getPageCounts()->Purchased->Combined->Yearly);
        } // endif
        return $this->Devices;
    }

    /**
     *
     * @return the $Devices
     */
    public function getExcludedDevices ()
    {
        if (! isset($this->ExcludedDevices))
        {
            // Calculating margin:
            $deviceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
            $knownDevices = $deviceMapper->fetchAll(array (
                    "report_id = ?" => $this->ReportId, 
                    "is_excluded = ?" => 1 
            ));
            $unknowndeviceMapper = Proposalgen_Model_Mapper_UnknownDeviceInstance::getInstance();
            $unknownDevices = $unknowndeviceMapper->fetchAllUnknownDevicesAsKnownDevices($this->ReportId, array (
                    "report_id = ?" => $this->ReportId, 
                    "is_excluded = 1" 
            ));
            
            $uploadDevices = Proposalgen_Model_Mapper_UploadDataCollector::getInstance()->getExcludedAsDeviceInstance($this->ReportId);
            $this->ExcludedDevices = array_merge($knownDevices, $unknownDevices, $uploadDevices);
        }
        return $this->ExcludedDevices;
    }

    /**
     *
     * @param $Devices field_type            
     */
    public function setDevices ($Devices)
    {
        $this->Devices = $Devices;
        return $this;
    }

    /**
     *
     * @return the $LeasedDevices
     */
    public function getLeasedDevices ()
    {
        if (! isset($this->LeasedDevices))
        {
            $this->LeasedDevices = array ();
            foreach ( $this->getDevices() as $device )
            {
                if ($device->MasterDevice->IsLeased)
                {
                    $this->LeasedDevices [] = $device;
                }
            }
        }
        return $this->LeasedDevices;
    }

    /**
     *
     * @param $LeasedDevices field_type            
     */
    public function setLeasedDevices ($LeasedDevices)
    {
        $this->LeasedDevices = $LeasedDevices;
        return $this;
    }

    /**
     *
     * @return Count of Devices
     */
    public function getDeviceCount ()
    {
        return count($this->getDevices());
    }

    /**
     *
     * @return Count of LeasedDevices
     */
    public function getLeasedDeviceCount ()
    {
        return count($this->getLeasedDevices());
    }

    /**
     *
     * @return the $PurchasedDevices
     */
    public function getPurchasedDevices ()
    {
        if (! isset($this->PurchasedDevices))
        {
            $this->PurchasedDevices = array ();
            foreach ( $this->getDevices() as $device )
            {
                if ($device->MasterDevice->IsLeased === 0)
                {
                    $this->PurchasedDevices [] = $device;
                }
            }
        }
        return $this->PurchasedDevices;
    }

    /**
     *
     * @param $PurchasedDevices field_type            
     */
    public function setPurchasedDevices ($PurchasedDevices)
    {
        $this->PurchasedDevices = $PurchasedDevices;
        return $this;
    }

    /**
     *
     * @return Count of PurchasedDevices
     */
    public function getPurchasedDeviceCount ()
    {
        return count($this->getPurchasedDevices());
    }

    /**
     *
     * @return the $LeasedEstimatedBlackAndWhiteCPP
     */
    public function getLeasedEstimatedBlackAndWhiteCPP ()
    {
        if (! isset($this->LeasedEstimatedBlackAndWhiteCPP))
        {
            $this->LeasedEstimatedBlackAndWhiteCPP = $this->getLeasedBlackAndWhiteCharge() + $this->getPerPageLeaseCost();
        }
        return $this->LeasedEstimatedBlackAndWhiteCPP;
    }

    /**
     *
     * @param $LeasedEstimatedBlackAndWhiteCPP field_type            
     */
    public function setLeasedEstimatedBlackAndWhiteCPP ($LeasedEstimatedBlackAndWhiteCPP)
    {
        $this->LeasedEstimatedBlackAndWhiteCPP = $LeasedEstimatedBlackAndWhiteCPP;
        return $this;
    }

    /**
     *
     * @return the $LeasedEstimatedColorCPP
     */
    public function getLeasedEstimatedColorCPP ()
    {
        if (! isset($this->LeasedEstimatedColorCPP))
        {
            $this->LeasedEstimatedColorCPP = $this->getLeasedColorCharge() + $this->getPerPageLeaseCost();
        }
        return $this->LeasedEstimatedColorCPP;
    }

    /**
     *
     * @param $LeasedEstimatedColorCPP field_type            
     */
    public function setLeasedEstimatedColorCPP ($LeasedEstimatedColorCPP)
    {
        $this->LeasedEstimatedColorCPP = $LeasedEstimatedColorCPP;
        return $this;
    }

    /**
     *
     * @return the $PurchasedEstimatedBlackAndWhiteCPP
     */
    public function getPurchasedEstimatedBlackAndWhiteCPP ()
    {
        if (! isset($this->PurchasedEstimatedBlackAndWhiteCPP))
        {
            // TODO: hardcoding for now
            $this->PurchasedEstimatedBlackAndWhiteCPP = 0.05;
        }
        return $this->PurchasedEstimatedBlackAndWhiteCPP;
    }

    /**
     *
     * @param $PurchasedEstimatedBlackAndWhiteCPP field_type            
     */
    public function setPurchasedEstimatedBlackAndWhiteCPP ($PurchasedEstimatedBlackAndWhiteCPP)
    {
        $this->PurchasedEstimatedBlackAndWhiteCPP = $PurchasedEstimatedBlackAndWhiteCPP;
        return $this;
    }

    /**
     *
     * @return the $PurchasedEstimatedColorCPP
     */
    public function getPurchasedEstimatedColorCPP ()
    {
        if (! isset($this->PurchasedEstimatedColorCPP))
        {
            // TODO: hardcoding for now
            $this->PurchasedEstimatedColorCPP = 0.08;
        }
        return $this->PurchasedEstimatedColorCPP;
    }

    /**
     *
     * @param $PurchasedEstimatedColorCPP field_type            
     */
    public function setPurchasedEstimatedColorCPP ($PurchasedEstimatedColorCPP)
    {
        $this->PurchasedEstimatedColorCPP = $PurchasedEstimatedColorCPP;
        return $this;
    }

    /**
     *
     * @return the $CombinedAnnualLeasePayments
     */
    public function getCombinedAnnualLeasePayments ()
    {
        if (! isset($this->CombinedAnnualLeasePayments))
        {
            $this->CombinedAnnualLeasePayments = $this->getReport()->ReportMonthlyLeasePayment * $this->getLeasedDeviceCount() * 12;
        }
        return $this->CombinedAnnualLeasePayments;
    }

    /**
     *
     * @param $CombinedAnnualLeasePayments field_type            
     */
    public function setCombinedAnnualLeasePayments ($CombinedAnnualLeasePayments)
    {
        $this->CombinedAnnualLeasePayments = $CombinedAnnualLeasePayments;
        return $this;
    }

    /**
     *
     * @return the $PerPageLeaseCost
     */
    public function getPerPageLeaseCost ()
    {
        if (! isset($this->PerPageLeaseCost))
        {
            if ($this->getPageCounts()->Leased->Combined->Yearly)
                $this->PerPageLeaseCost = $this->getCombinedAnnualLeasePayments() / $this->getPageCounts()->Leased->Combined->Yearly;
        }
        return $this->PerPageLeaseCost;
    }

    /**
     *
     * @param $PerPageLeaseCost field_type            
     */
    public function setPerPageLeaseCost ($PerPageLeaseCost)
    {
        $this->PerPageLeaseCost = $PerPageLeaseCost;
        return $this;
    }

    /**
     *
     * @return the $User
     */
    public function getUser ()
    {
        if (! isset($this->User))
        {
            
            $this->User = null;
        }
        return $this->User;
    }

    /**
     *
     * @param $User field_type            
     */
    public function setUser ($User)
    {
        $this->User = $User;
        return $this;
    }

    /**
     *
     * @return the $DealerCompany
     */
    public function getDealerCompany ()
    {
        if (! isset($this->DealerCompany))
        {
            
            $this->DealerCompany = null;
        }
        return $this->DealerCompany;
    }

    /**
     *
     * @param $DealerCompany field_type            
     */
    public function setDealerCompany ($DealerCompany)
    {
        $this->DealerCompany = $DealerCompany;
        return $this;
    }

    /**
     *
     * @return the $CompanyMargin
     */
    public function getCompanyMargin ()
    {
        if (! isset($this->CompanyMargin))
        {
            $this->CompanyMargin = 1 - (((int)$this->getDealerCompany()->getDcPricingMargin()) / 100);
        }
        return $this->CompanyMargin;
    }

    /**
     *
     * @param $CompanyMargin field_type            
     */
    public function setCompanyMargin ($CompanyMargin)
    {
        $this->CompanyMargin = $CompanyMargin;
        return $this;
    }

    /**
     *
     * @return the $ReportMargin
     */
    public function getReportMargin ()
    {
        if (! isset($this->ReportMargin))
        {
            $this->ReportMargin = 1 - ((((int)$this->getReport()->getReportPricingMargin())) / 100);
        }
        return $this->ReportMargin;
    }

    /**
     *
     * @param $ReportMargin field_type            
     */
    public function setReportMargin ($ReportMargin)
    {
        $this->ReportMargin = $ReportMargin;
        return $this;
    }

    /**
     *
     * @return the $LeasedBlackAndWhiteCharge
     */
    public function getLeasedBlackAndWhiteCharge ()
    {
        if (! isset($this->LeasedBlackAndWhiteCharge))
        {
            $this->LeasedBlackAndWhiteCharge = $this->getReport()->ReportLeasedBWPerPage;
        }
        return $this->LeasedBlackAndWhiteCharge;
    }

    /**
     *
     * @param $LeasedBlackAndWhiteCharge field_type            
     */
    public function setLeasedBlackAndWhiteCharge ($LeasedBlackAndWhiteCharge)
    {
        $this->LeasedBlackAndWhiteCharge = $LeasedBlackAndWhiteCharge;
        return $this;
    }

    /**
     *
     * @return the $LeasedColorCharge
     */
    public function getLeasedColorCharge ()
    {
        if (! isset($this->LeasedColorCharge))
        {
            $this->LeasedColorCharge = $this->getReport()->ReportLeasedColorPerPage;
        }
        return $this->LeasedColorCharge;
    }

    /**
     *
     * @param $LeasedColorCharge field_type            
     */
    public function setLeasedColorCharge ($LeasedColorCharge)
    {
        $this->LeasedColorCharge = $LeasedColorCharge;
        return $this;
    }

    /**
     *
     * @return the $EstimatedAnnualCostOfLeaseMachines
     */
    public function getEstimatedAnnualCostOfLeaseMachines ()
    {
        if (! isset($this->EstimatedAnnualCostOfLeaseMachines))
        {
            $this->EstimatedAnnualCostOfLeaseMachines = $this->getCombinedAnnualLeasePayments() + ($this->getLeasedBlackAndWhiteCharge() * $this->getPageCounts()->Leased->BlackAndWhite->Yearly) + ($this->getLeasedColorCharge() * $this->getLeasedColorCharge());
        }
        return $this->EstimatedAnnualCostOfLeaseMachines;
    }

    /**
     *
     * @param $EstimatedAnnualCostOfLeaseMachines field_type            
     */
    public function setEstimatedAnnualCostOfLeaseMachines ($EstimatedAnnualCostOfLeaseMachines)
    {
        $this->EstimatedAnnualCostOfLeaseMachines = $EstimatedAnnualCostOfLeaseMachines;
        return $this;
    }

    /**
     *
     * @return the $AnnualCostOfHardwarePurchases
     */
    public function getAnnualCostOfHardwarePurchases ()
    {
        if (! isset($this->AnnualCostOfHardwarePurchases))
        {
            $averageAge = 0;
            $totalAge = 0;
            foreach ( $this->getPurchasedDevices() as $device )
                $totalAge += $device->getAge();
            if ($this->getPurchasedDeviceCount())
            {
                $averageAge = $totalAge / $this->getPurchasedDeviceCount();
                $this->AnnualCostOfHardwarePurchases = ($this->getDeviceCount() / $averageAge) * $this->getReport()->ReportAverageNonLeasePrinterCost;
            }
            else
            {
                $this->AnnualCostOfHardwarePurchases = 0;
            }
        }
        return $this->AnnualCostOfHardwarePurchases;
    }

    /**
     *
     * @param $AnnualCostOfHardwarePurchases field_type            
     */
    public function setAnnualCostOfHardwarePurchases ($AnnualCostOfHardwarePurchases)
    {
        $this->AnnualCostOfHardwarePurchases = $AnnualCostOfHardwarePurchases;
        return $this;
    }

    /**
     *
     * @return the $CostOfInkAndTonerMonthly
     */
    public function getCostOfInkAndTonerMonthly ()
    {
        if (! isset($this->CostOfInkAndTonerMonthly))
        {
            // Calculate
            $totalCost = 0;
            foreach ( $this->getPurchasedDevices() as $device )
            {
                $totalCost += $device->getCostOfInkAndToner();
            }
            $this->CostOfInkAndTonerMonthly = $totalCost;
        }
        return $this->CostOfInkAndTonerMonthly;
    }

    /**
     *
     * @return the $CostOfInkAndToner
     */
    public function getCostOfInkAndToner ()
    {
        if (! isset($this->CostOfInkAndToner))
        {
            $this->CostOfInkAndToner = $this->getCostOfInkAndTonerMonthly() * 12;
        }
        return $this->CostOfInkAndToner;
    }

    /**
     *
     * @return the $NumberOfScanCapableDevices
     */
    public function getNumberOfScanCapableDevices ()
    {
        if (! isset($this->NumberOfScanCapableDevices))
        {
            $numberOfDevices = 0;
            foreach ( $this->getDevices() as $device )
                if ($device->getMasterDevice()->IsScanner)
                    $numberOfDevices ++;
            $this->NumberOfScanCapableDevices = $numberOfDevices;
        }
        return $this->NumberOfScanCapableDevices;
    }

    /**
     *
     * @param $NumberOfScanCapableDevices field_type            
     */
    public function setNumberOfScanCapableDevices ($NumberOfScanCapableDevices)
    {
        $this->NumberOfScanCapableDevices = $NumberOfScanCapableDevices;
        return $this;
    }

    /**
     *
     * @return the $NumberOfDuplexCapableDevices
     */
    public function getNumberOfDuplexCapableDevices ()
    {
        if (! isset($this->NumberOfDuplexCapableDevices))
        {
            $numberOfDevices = 0;
            foreach ( $this->getDevices() as $device )
                if ($device->getMasterDevice()->IsDuplex)
                    $numberOfDevices ++;
            $this->NumberOfDuplexCapableDevices = $numberOfDevices;
        }
        return $this->NumberOfDuplexCapableDevices;
    }

    /**
     *
     * @param $NumberOfDuplexCapableDevices field_type            
     */
    public function setNumberOfDuplexCapableDevices ($NumberOfDuplexCapableDevices)
    {
        $this->NumberOfDuplexCapableDevices = $NumberOfDuplexCapableDevices;
        return $this;
    }

    /**
     *
     * @return the $NumberOfFaxCapableDevices
     */
    public function getNumberOfFaxCapableDevices ()
    {
        if (! isset($this->NumberOfFaxCapableDevices))
        {
            $numberOfDevices = 0;
            foreach ( $this->getDevices() as $device )
                if ($device->getMasterDevice()->IsFax)
                    $numberOfDevices ++;
            $this->NumberOfFaxCapableDevices = $numberOfDevices;
        }
        return $this->NumberOfFaxCapableDevices;
    }

    /**
     *
     * @return the $NumberOfUniqueModels
     */
    public function getNumberOfUniqueModels ()
    {
        if (! isset($this->NumberOfUniqueModels))
        {
            $this->NumberOfUniqueModels = count($this->getUniqueDeviceList());
        }
        return $this->NumberOfUniqueModels;
    }

    /**
     *
     * @param $NumberOfUniqueModels field_type            
     */
    public function setNumberOfUniqueModels ($NumberOfUniqueModels)
    {
        $this->NumberOfUniqueModels = $NumberOfUniqueModels;
        return $this;
    }

    /**
     *
     * @param $NumberOfFaxCapableDevices field_type            
     */
    public function setNumberOfFaxCapableDevices ($NumberOfFaxCapableDevices)
    {
        $this->NumberOfFaxCapableDevices = $NumberOfFaxCapableDevices;
        return $this;
    }

    /**
     *
     * @return the $NumberOfUniqueToners
     */
    public function getNumberOfUniqueToners ()
    {
        if (! isset($this->NumberOfUniqueToners))
        {
            $this->NumberOfUniqueToners = count($this->getUniqueTonerList());
        }
        return $this->NumberOfUniqueToners;
    }

    /**
     *
     * @param $NumberOfUniqueToners field_type            
     */
    public function setNumberOfUniqueToners ($NumberOfUniqueToners)
    {
        $this->NumberOfUniqueToners = $NumberOfUniqueToners;
        return $this;
    }

    /**
     *
     * @return the $CashHeldInInventory
     */
    public function getCashHeldInInventory ()
    {
        if (! isset($this->CashHeldInInventory))
        {
            $inventoryCash = 0;
            foreach ( $this->getUniquePurchasedTonerList() as $toner )
            {
                $inventoryCash += $toner->TonerPrice;
            }
        }
        $this->CashHeldInInventory = $inventoryCash * 2;
        
        return $this->CashHeldInInventory;
    }

    /**
     *
     * @param $CashHeldInInventory field_type            
     */
    public function setCashHeldInInventory ($CashHeldInInventory)
    {
        $this->CashHeldInInventory = $CashHeldInInventory;
        return $this;
    }

    /**
     *
     * @return the $PageCoverageBlackAndWhite
     */
    public function getPageCoverageBlackAndWhite ()
    {
        if (! isset($this->PageCoverageBlackAndWhite))
        {
            $questions = $this->getReportQuestions();
            $this->PageCoverageBlackAndWhite = $questions [21]->NumericAnswer;
        }
        return $this->PageCoverageBlackAndWhite;
    }

    /**
     *
     * @param $PageCoverageBlackAndWhite field_type            
     */
    public function setPageCoverageBlackAndWhite ($PageCoverageBlackAndWhite)
    {
        $this->PageCoverageBlackAndWhite = $PageCoverageBlackAndWhite;
        return $this;
    }

    /**
     *
     * @return the $PageCoverageColor
     */
    public function getPageCoverageColor ()
    {
        if (! isset($this->PageCoverageColor))
        {
            $questions = $this->getReportQuestions();
            $this->PageCoverageColor = $questions [22]->NumericAnswer;
        }
        return $this->PageCoverageColor;
    }

    /**
     *
     * @param $PageCoverageColor field_type            
     */
    public function setPageCoverageColor ($PageCoverageColor)
    {
        $this->PageCoverageColor = $PageCoverageColor;
        return $this;
    }

    /**
     *
     * @return the $MaximumMonthlyPrintVolume
     */
    public function getMaximumMonthlyPrintVolume ()
    {
        if (! isset($this->MaximumMonthlyPrintVolume))
        {
            $maxVolume = 0;
            foreach ( $this->getDevices() as $device )
            {
                $maxVolume += $device->getMaximumMonthlyPageVolume();
            }
            $this->MaximumMonthlyPrintVolume = $maxVolume;
        }
        return $this->MaximumMonthlyPrintVolume;
    }

    /**
     *
     * @return the $NumberOfColorCapableDevices
     */
    public function getNumberOfColorCapableDevices ()
    {
        if (! isset($this->NumberOfColorCapableDevices))
        {
            $numberOfDevices = 0;
            foreach ( $this->getDevices() as $device )
            {
                if ($device->getMasterDevice()->getTonerConfigId() != Proposalgen_Model_TonerConfig::BLACK_ONLY)
                    $numberOfDevices ++;
            }
            $this->NumberOfColorCapableDevices = $numberOfDevices;
        }
        return $this->NumberOfColorCapableDevices;
    }

    /**
     *
     * @param $NumberOfColorCapableDevices field_type            
     */
    public function setNumberOfColorCapableDevices ($NumberOfColorCapableDevices)
    {
        $this->NumberOfColorCapableDevices = $NumberOfColorCapableDevices;
        return $this;
    }

    /**
     *
     * @return the $NumberOfBlackAndWhiteCapableDevices
     */
    public function getNumberOfBlackAndWhiteCapableDevices ()
    {
        if (! isset($this->NumberOfBlackAndWhiteCapableDevices))
        {
            $this->NumberOfBlackAndWhiteCapableDevices = $this->getDeviceCount() - $this->getNumberOfColorCapableDevices();
        }
        return $this->NumberOfBlackAndWhiteCapableDevices;
    }

    /**
     *
     * @param $NumberOfBlackAndWhiteCapableDevices field_type            
     */
    public function setNumberOfBlackAndWhiteCapableDevices ($NumberOfBlackAndWhiteCapableDevices)
    {
        $this->NumberOfBlackAndWhiteCapableDevices = $NumberOfBlackAndWhiteCapableDevices;
        return $this;
    }

    public function getPercentages ()
    {
        if (! isset($this->Percentages))
        {
            $Percentages = new stdClass();
            $Percentages->TotalColorPercentage = 0;
            $Percentages->PurchasedVsLeasedBlackAndWhite = new stdClass();
            $Percentages->PurchasedVsLeasedBlackAndWhite->Leased = 0;
            $Percentages->PurchasedVsLeasedBlackAndWhite->Purchased = 0;
            $Percentages->PurchasedVsLeasedColor = new stdClass();
            $Percentages->PurchasedVsLeasedColor->Leased = 0;
            $Percentages->PurchasedVsLeasedColor->Purchased = 0;
            if ($this->getPageCounts()->Total->Combined->Monthly)
                $Percentages->TotalColorPercentage = $this->getPageCounts()->Total->Color->Monthly / $this->getPageCounts()->Total->Combined->Monthly;
            if ($this->getPageCounts()->Total->BlackAndWhite->Yearly)
            {
                $Percentages->PurchasedVsLeasedBlackAndWhite->Leased = $this->getPageCounts()->Leased->BlackAndWhite->Yearly / $this->getPageCounts()->Total->BlackAndWhite->Yearly;
                $Percentages->PurchasedVsLeasedBlackAndWhite->Purchased = $this->getPageCounts()->Purchased->BlackAndWhite->Yearly / $this->getPageCounts()->Total->BlackAndWhite->Yearly;
            }
            if ($this->getPageCounts()->Total->Color->Yearly)
            {
                $Percentages->PurchasedVsLeasedColor->Leased = $this->getPageCounts()->Leased->Color->Yearly / $this->getPageCounts()->Total->Color->Yearly;
                $Percentages->PurchasedVsLeasedColor->Purchased = $this->getPageCounts()->Purchased->Color->Yearly / $this->getPageCounts()->Total->Color->Yearly;
            }
            $this->Percentages = $Percentages;
        }
        return $this->Percentages;
    }

    /**
     *
     * @return the $PageCounts
     */
    public function getPageCounts ()
    {
        if (! isset($this->PageCounts))
        {
            $pageCounts = new stdClass();
            // Purchased Pages
            $pageCounts->Purchased = new stdClass();
            $pageCounts->Purchased->BlackAndWhite = new stdClass();
            $pageCounts->Purchased->BlackAndWhite->Monthly = 0;
            $pageCounts->Purchased->Color = new stdClass();
            $pageCounts->Purchased->Color->Monthly = 0;
            $pageCounts->Purchased->Combined = new stdClass();
            foreach ( $this->getPurchasedDevices() as $device )
            {
                $pageCounts->Purchased->BlackAndWhite->Monthly += $device->getAverageMonthlyBlackAndWhitePageCount();
                $pageCounts->Purchased->Color->Monthly += $device->getAverageMonthlyColorPageCount();
            }
            $pageCounts->Purchased->BlackAndWhite->Yearly = $pageCounts->Purchased->BlackAndWhite->Monthly * 12;
            $pageCounts->Purchased->Color->Yearly = $pageCounts->Purchased->Color->Monthly * 12;
            $pageCounts->Purchased->Combined->Monthly = $pageCounts->Purchased->BlackAndWhite->Monthly + $pageCounts->Purchased->Color->Monthly;
            $pageCounts->Purchased->Combined->Yearly = $pageCounts->Purchased->BlackAndWhite->Yearly + $pageCounts->Purchased->Color->Yearly;
            // Leased Pages
            $pageCounts->Leased = new stdClass();
            $pageCounts->Leased->BlackAndWhite = new stdClass();
            $pageCounts->Leased->BlackAndWhite->Monthly = 0;
            $pageCounts->Leased->Color = new stdClass();
            $pageCounts->Leased->Color->Monthly = 0;
            $pageCounts->Leased->Combined = new stdClass();
            foreach ( $this->getLeasedDevices() as $device )
            {
                $pageCounts->Leased->BlackAndWhite->Monthly += $device->getAverageMonthlyBlackAndWhitePageCount();
                $pageCounts->Leased->Color->Monthly += $device->getAverageMonthlyColorPageCount();
            }
            $pageCounts->Leased->BlackAndWhite->Yearly = $pageCounts->Leased->BlackAndWhite->Monthly * 12;
            $pageCounts->Leased->Color->Yearly = $pageCounts->Leased->Color->Monthly * 12;
            $pageCounts->Leased->Combined->Monthly = $pageCounts->Leased->BlackAndWhite->Monthly + $pageCounts->Leased->Color->Monthly;
            $pageCounts->Leased->Combined->Yearly = $pageCounts->Leased->BlackAndWhite->Yearly + $pageCounts->Leased->Color->Yearly;
            // Total Pages
            $pageCounts->Total = new stdClass();
            $pageCounts->Total->BlackAndWhite = new stdClass();
            $pageCounts->Total->BlackAndWhite->Monthly = $pageCounts->Purchased->BlackAndWhite->Monthly + $pageCounts->Leased->BlackAndWhite->Monthly;
            $pageCounts->Total->BlackAndWhite->Yearly = $pageCounts->Purchased->BlackAndWhite->Yearly + $pageCounts->Leased->BlackAndWhite->Yearly;
            $pageCounts->Total->Color = new stdClass();
            $pageCounts->Total->Color->Monthly = $pageCounts->Purchased->Color->Monthly + $pageCounts->Leased->Color->Monthly;
            $pageCounts->Total->Color->Yearly = $pageCounts->Purchased->Color->Yearly + $pageCounts->Leased->Color->Yearly;
            $pageCounts->Total->Combined = new stdClass();
            $pageCounts->Total->Combined->Monthly = $pageCounts->Purchased->Combined->Monthly + $pageCounts->Leased->Combined->Monthly;
            $pageCounts->Total->Combined->Yearly = $pageCounts->Purchased->Combined->Yearly + $pageCounts->Leased->Combined->Yearly;
            $this->PageCounts = $pageCounts;
        }
        return $this->PageCounts;
    }

    /**
     *
     * @return the $AverageCostOfDevices
     */
    public function getAverageCostOfDevices ()
    {
        if (! isset($this->AverageCostOfDevices))
        {
            $this->AverageCostOfDevices = $this->getReport()->getReportAverageNonLeasePrinterCost();
        }
        return $this->AverageCostOfDevices;
    }

    /**
     *
     * @param $AverageCostOfDevices field_type            
     */
    public function setAverageCostOfDevices ($AverageCostOfDevices)
    {
        $this->AverageCostOfDevices = $AverageCostOfDevices;
        return $this;
    }

    /**
     *
     * @return the $PercentDevicesUnderused
     */
    public function getPercentDevicesUnderused ()
    {
        if (! isset($this->PercentDevicesUnderused))
        {
            $devicesUnderusedCount = 0;
            foreach ( $this->getDevices() as $device )
            {
                if ($device->getAverageMonthlyPageCount() < ($device->getMasterDevice()->getMaximumMonthlyPageVolume() * 0.25))
                    $devicesUnderusedCount ++;
            }
            $this->PercentDevicesUnderused = ($devicesUnderusedCount / count($this->getDevices())) * 100;
        }
        return $this->PercentDevicesUnderused;
    }

    /**
     *
     * @param $PercentDevicesUnderused field_type            
     */
    public function setPercentDevicesUnderused ($PercentDevicesUnderused)
    {
        $this->PercentDevicesUnderused = $PercentDevicesUnderused;
        return $this;
    }

    /**
     *
     * @return the $PercentDevicesOverused
     */
    public function getPercentDevicesOverused ()
    {
        if (! isset($this->PercentDevicesOverused))
        {
            $devicesOverusedCount = 0;
            foreach ( $this->getDevices() as $device )
            {
                if ($device->getAverageMonthlyPageCount() > $device->getMasterDevice()->getMaximumMonthlyPageVolume())
                    $devicesOverusedCount ++;
            }
            $this->PercentDevicesOverused = ($devicesOverusedCount / count($this->getDevices())) * 100;
        }
        return $this->PercentDevicesOverused;
    }

    /**
     *
     * @param $PercentDevicesOverused field_type            
     */
    public function setPercentDevicesOverused ($PercentDevicesOverused)
    {
        $this->PercentDevicesOverused = $PercentDevicesOverused;
        return $this;
    }

    /**
     *
     * @return the $LeastUsedDevices
     */
    public function getLeastUsedDevices ()
    {
        if (! isset($this->LeastUsedDevices))
        {
            $deviceArray = $this->getDevices();
            usort($deviceArray, array (
                    $this, 
                    "ascendingSortDevicesByUsage" 
            ));
            // returning only the first 2
            $deviceArray = array (
                    $deviceArray [0], 
                    $deviceArray [1] 
            );
            $this->LeastUsedDevices = $deviceArray;
        }
        return $this->LeastUsedDevices;
    }

    /**
     *
     * @param $LeastUsedDevices field_type            
     */
    public function setLeastUsedDevices ($LeastUsedDevices)
    {
        $this->LeastUsedDevices = $LeastUsedDevices;
        return $this;
    }

    /**
     * Callback function for usort when we want to sort a device based on usage
     *
     * @param $a Proposalgen_Model_DeviceInstance            
     * @param $b Proposalgen_Model_DeviceInstance            
     */
    public function ascendingSortDevicesByUsage ($deviceA, $deviceB)
    {
        if ($deviceA->getUsage() == $deviceB->getUsage())
        {
            return 0;
        }
        return ($deviceA->getUsage() < $deviceB->getUsage()) ? - 1 : 1;
    }

    /**
     * Callback function for usort when we want to sort a device based on usage
     *
     * @param $a Proposalgen_Model_DeviceInstance            
     * @param $b Proposalgen_Model_DeviceInstance            
     */
    public function descendingSortDevicesByUsage ($deviceA, $deviceB)
    {
        if ($deviceA->getUsage() == $deviceB->getUsage())
        {
            return 0;
        }
        return ($deviceA->getUsage() > $deviceB->getUsage()) ? - 1 : 1;
    }

    /**
     *
     * @return the $DefaultToners
     */
    public function getDefaultToners ()
    {
        if (! isset($this->DefaultToners))
        {
            $tonerOverrides ["BW"] = array (
                    "Cost" => 0, 
                    "Yield" => 0 
            );
            $tonerOverrides ["Color"] = array (
                    "Cost" => 0, 
                    "Yield" => 0 
            );
            $tonerOverrides ["ThreeColor"] = array (
                    "Cost" => 0, 
                    "Yield" => 0 
            );
            $tonerOverrides ["FourColor"] = array (
                    "Cost" => 0, 
                    "Yield" => 0 
            );
            
            $overrideLocation ["Report"] = $this->getReport();
            $overrideLocation ["User"] = $this->getUser();
            $overrideLocation ["Dc"] = $this->getDealerCompany();
            $overrideLocation ["Dc"] = Proposalgen_Model_DealerCompany::getMasterCompany();
            
            foreach ( $tonerOverrides as $type => $override )
            {
                // This is a fancy way of looping through the objects
                // dynamically
                // Written by Lee
                

                // For the cost
                foreach ( $overrideLocation as $FuncPrefix => $object )
                {
                    $functionCall = "get" . $FuncPrefix . "Default" . $type . "TonerCost";
                    if ($object->$functionCall())
                    {
                        $override ["Cost"] = $object->$functionCall();
                        break;
                    }
                }
                
                if ($override ["Cost"] <= 0)
                {
                    throw new Exception("Cost of " . $override ["Cost"] . " detected for " . $type . " toner. Cost must be greater than zero.");
                }
                
                // For the yield
                foreach ( $overrideLocation as $FuncPrefix => $object )
                {
                    $functionCall = "get" . $FuncPrefix . "Default" . $type . "TonerYield";
                    if ($object->$functionCall())
                    {
                        $override ["Yield"] = $object->$functionCall();
                        break;
                    }
                }
                
                if ($override ["Yield"] <= 0)
                {
                    throw new Exception("Yield of " . $override ["Yield"] . " detected for " . $type . " toner. Yield must be greater than zero.");
                }
            }
            
            $blackToner = new Proposalgen_Model_Toner();
            $blackToner->setTonerPrice($tonerOverrides ["BW"] ["Cost"]);
            $blackToner->setTonerYield($tonerOverrides ["BW"] ["Yield"]);
            $blackToner->setTonerColorId(Proposalgen_Model_TonerColor::BLACK);
            
            $cyanToner = new Proposalgen_Model_Toner();
            $cyanToner->setTonerPrice($tonerOverrides ["Color"] ["Cost"]);
            $cyanToner->setTonerYield($tonerOverrides ["Color"] ["Yield"]);
            $cyanToner->setTonerColorId(Proposalgen_Model_TonerColor::CYAN);
            
            $magentaToner = new Proposalgen_Model_Toner();
            $magentaToner->setTonerPrice($tonerOverrides ["Color"] ["Cost"]);
            $magentaToner->setTonerYield($tonerOverrides ["Color"] ["Yield"]);
            $magentaToner->setTonerColorId(Proposalgen_Model_TonerColor::MAGENTA);
            
            $yellowToner = new Proposalgen_Model_Toner();
            $yellowToner->setTonerPrice($tonerOverrides ["Color"] ["Cost"]);
            $yellowToner->setTonerYield($tonerOverrides ["Color"] ["Yield"]);
            $yellowToner->setTonerColorId(Proposalgen_Model_TonerColor::YELLOW);
            
            $threeColorToner = new Proposalgen_Model_Toner();
            $threeColorToner->setTonerPrice($tonerOverrides ["ThreeColor"] ["Cost"]);
            $threeColorToner->setTonerYield($tonerOverrides ["ThreeColor"] ["Yield"]);
            $threeColorToner->setTonerColorId(Proposalgen_Model_TonerColor::THREE_COLOR);
            
            $fourColorToner = new Proposalgen_Model_Toner();
            $fourColorToner->setTonerPrice($tonerOverrides ["FourColor"] ["Cost"]);
            $fourColorToner->setTonerYield($tonerOverrides ["FourColor"] ["Yield"]);
            $fourColorToner->setTonerColorId(Proposalgen_Model_TonerColor::FOUR_COLOR);
            
            $defaultToners = array ();
            $defaultToners [Proposalgen_Model_TonerConfig::BLACK_ONLY] = array (
                    $blackToner 
            );
            $defaultToners [Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED] = array (
                    $blackToner->getTonerColorId() => $blackToner, 
                    $cyanToner->getTonerColorId() => $cyanToner, 
                    $magentaToner->getTonerColorId() => $magentaToner, 
                    $yellowToner->getTonerColorId() => $yellowToner 
            );
            $defaultToners [Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED] = array (
                    $blackToner->getTonerColorId() => $blackToner, 
                    $threeColorToner->getTonerColorId() => $threeColorToner 
            );
            $defaultToners [Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED] = array (
                    $fourColorToner->getTonerColorId() => $fourColorToner 
            );
            
            $this->DefaultToners = $defaultToners;
        }
        return $this->DefaultToners;
    }

    /**
     *
     * @param $DefaultToners field_type            
     */
    public function setDefaultToners ($DefaultToners)
    {
        $this->DefaultToners = $DefaultToners;
        return $this;
    }

    /**
     *
     * @return the $MostUsedDevices
     */
    public function getMostUsedDevices ()
    {
        if (! isset($this->MostUsedDevices))
        {
            $deviceArray = $this->getDevices();
            usort($deviceArray, array (
                    $this, 
                    "descendingSortDevicesByUsage" 
            ));
            // returning only the first 2
            $deviceArray = array (
                    $deviceArray [0], 
                    $deviceArray [1] 
            );
            $this->MostUsedDevices = $deviceArray;
        }
        return $this->MostUsedDevices;
    }

    /**
     *
     * @param $MostUsedDevices field_type            
     */
    public function setMostUsedDevices ($MostUsedDevices)
    {
        $this->MostUsedDevices = $MostUsedDevices;
        return $this;
    }

    /**
     *
     * @return the $PercentColorDevices
     */
    public function getPercentColorDevices ()
    {
        if (! isset($this->PercentColorDevices))
        {
            $this->PercentColorDevices = $this->getNumberOfColorCapableDevices() / count($this->getDevices());
        }
        return $this->PercentColorDevices;
    }

    /**
     *
     * @param $PercentColorDevices field_type            
     */
    public function setPercentColorDevices ($PercentColorDevices)
    {
        $this->PercentColorDevices = $PercentColorDevices;
        return $this;
    }

    /**
     *
     * @return the $AverageAgeOfDevices
     */
    public function getAverageAgeOfDevices ()
    {
        if (! isset($this->AverageAgeOfDevices))
        {
            $totalAge = 0;
            foreach ( $this->getDevices() as $device )
                $totalAge += $device->getAge();
            $this->AverageAgeOfDevices = $totalAge / count($this->getDevices());
        }
        return $this->AverageAgeOfDevices;
    }

    /**
     *
     * @param $AverageAgeOfDevices field_type            
     */
    public function setAverageAgeOfDevices ($AverageAgeOfDevices)
    {
        $this->AverageAgeOfDevices = $AverageAgeOfDevices;
        return $this;
    }

    /**
     *
     * @return the $HighPowerConsumptionDevices
     */
    public function getHighPowerConsumptionDevices ()
    {
        if (! isset($this->HighPowerConsumptionDevices))
        {
            $deviceArray = $this->getDevices();
            usort($deviceArray, array (
                    $this, 
                    "ascendingSortDevicesByPowerConsumption" 
            ));
            $this->HighPowerConsumptionDevices = $deviceArray;
        }
        return $this->HighPowerConsumptionDevices;
    }

    /**
     *
     * @param $HighPowerConsumptionDevices field_type            
     */
    public function setHighPowerConsumptionDevices ($HighPowerConsumptionDevices)
    {
        $this->HighPowerConsumptionDevices = $HighPowerConsumptionDevices;
        return $this;
    }

    /**
     * Callback function for usort when we want to sort a device based on power
     * consumption
     *
     * @param $a Proposalgen_Model_DeviceInstance            
     * @param $b Proposalgen_Model_DeviceInstance            
     */
    public function ascendingSortDevicesByPowerConsumption ($deviceA, $deviceB)
    {
        if ($deviceA->getAverageDailyPowerConsumption() == $deviceB->getAverageDailyPowerConsumption())
        {
            return 0;
        }
        return ($deviceA->getAverageDailyPowerConsumption() > $deviceB->getAverageDailyPowerConsumption()) ? - 1 : 1;
    }

    /**
     *
     * @return the $MostExpensiveDevices
     */
    public function getMostExpensiveDevices ()
    {
        if (! isset($this->MostExpensiveDevices))
        {
            
            $deviceArray = $this->getPurchasedDevices();
            usort($deviceArray, array (
                    $this, 
                    "ascendingSortDevicesByMonthlyCost" 
            ));
            $this->MostExpensiveDevices = $deviceArray;
        }
        return $this->MostExpensiveDevices;
    }

    /**
     *
     * @param $MostExpensiveDevices field_type            
     */
    public function setMostExpensiveDevices ($MostExpensiveDevices)
    {
        $this->MostExpensiveDevices = $MostExpensiveDevices;
        return $this;
    }

    /**
     * Callback function for usort when we want to sort a device based on
     * monthly cost
     *
     * @param $a Proposalgen_Model_DeviceInstance            
     * @param $b Proposalgen_Model_DeviceInstance            
     */
    public function ascendingSortDevicesByMonthlyCost ($deviceA, $deviceB)
    {
        if ($deviceA->getMonthlyRate() == $deviceB->getMonthlyRate())
        {
            return 0;
        }
        return ($deviceA->getMonthlyRate() > $deviceB->getMonthlyRate()) ? - 1 : 1;
    }

    /**
     *
     * @return the $DateReportPrepared
     */
    public function getDateReportPrepared ()
    {
        if (! isset($this->DateReportPrepared))
        {
            // $today = date("F jS, Y");
            $report_date = new DateTime($this->Report->getReportDate());
            $this->DateReportPrepared = date_format($report_date, 'F jS, Y');
        }
        return $this->DateReportPrepared;
    }

    /**
     *
     * @param $DateReportPrepared field_type            
     */
    public function setDateReportPrepared ($DateReportPrepared)
    {
        $this->DateReportPrepared = $DateReportPrepared;
        return $this;
    }

    /**
     *
     * @return the $AveragePowerUsagePerMonth
     */
    public function getAveragePowerUsagePerMonth ()
    {
        if (! isset($this->AveragePowerUsagePerMonth))
        {
            $totalPowerUsage = 0;
            $devicesReportingPower = 0;
            foreach ( $this->getDevices() as $device )
            {
                if ($device->getMasterDevice()->WattsPowerNormal > 0)
                {
                    $totalPowerUsage += $device->getAverageMonthlyPowerConsumption();
                    $devicesReportingPower ++;
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
            $this->setNumberOfDevicesReportingPower($devicesReportingPower);
            $this->AveragePowerUsagePerMonth = $totalPowerUsage;
        }
        return $this->AveragePowerUsagePerMonth;
    }

    /**
     *
     * @param $AveragePowerUsagePerMonth field_type            
     */
    public function setAveragePowerUsagePerMonth ($AveragePowerUsagePerMonth)
    {
        $this->AveragePowerUsagePerMonth = $AveragePowerUsagePerMonth;
        return $this;
    }

    /**
     *
     * @return the $AveragePowerCostPerMonth
     */
    public function getAveragePowerCostPerMonth ()
    {
        if (! isset($this->AveragePowerCostPerMonth))
        {
            $this->AveragePowerCostPerMonth = $this->getAveragePowerUsagePerMonth() * Proposalgen_Model_DeviceInstance::getKWH_Cost();
            ;
        }
        return $this->AveragePowerCostPerMonth;
    }

    /**
     *
     * @param $AveragePowerCostPerMonth field_type            
     */
    public function setAveragePowerCostPerMonth ($AveragePowerCostPerMonth)
    {
        $this->AveragePowerCostPerMonth = $AveragePowerCostPerMonth;
        return $this;
    }

    /**
     *
     * @return the $LeastUsedDeviceCount
     */
    public function getLeastUsedDeviceCount ()
    {
        if (! isset($this->LeastUsedDeviceCount))
        {
            $this->LeastUsedDeviceCount = count($this->getLeastUsedDevices());
        }
        return $this->LeastUsedDeviceCount;
    }

    /**
     *
     * @param $LeastUsedDeviceCount field_type            
     */
    public function setLeastUsedDeviceCount ($LeastUsedDeviceCount)
    {
        $this->LeastUsedDeviceCount = $LeastUsedDeviceCount;
        return $this;
    }

    /**
     *
     * @return the $LeastUsedDevicePercentage
     */
    public function getLeastUsedDevicePercentage ()
    {
        if (! isset($this->LeastUsedDevicePercentage))
        {
            $this->LeastUsedDevicePercentage = $this->getLeastUsedDeviceCount() / $this->getDeviceCount() * 100;
        }
        return $this->LeastUsedDevicePercentage;
    }

    /**
     *
     * @param $LeastUsedDevicePercentage field_type            
     */
    public function setLeastUsedDevicePercentage ($LeastUsedDevicePercentage)
    {
        $this->LeastUsedDevicePercentage = $LeastUsedDevicePercentage;
        return $this;
    }

    /**
     *
     * @return the $MostUsedDeviceCount
     */
    public function getMostUsedDeviceCount ()
    {
        if (! isset($this->MostUsedDeviceCount))
        {
            $this->MostUsedDeviceCount = count($this->getMostUsedDevices());
        }
        return $this->MostUsedDeviceCount;
    }

    /**
     *
     * @param $MostUsedDeviceCount field_type            
     */
    public function setMostUsedDeviceCount ($MostUsedDeviceCount)
    {
        $this->MostUsedDeviceCount = $MostUsedDeviceCount;
        return $this;
    }

    /**
     *
     * @return the $MostUsedDevicePercentage
     */
    public function getMostUsedDevicePercentage ()
    {
        if (! isset($this->MostUsedDevicePercentage))
        {
            $this->MostUsedDevicePercentage = $this->getMostUsedDeviceCount() / $this->getDeviceCount() * 100;
        }
        return $this->MostUsedDevicePercentage;
    }

    /**
     *
     * @param $MostUsedDevicePercentage field_type            
     */
    public function setMostUsedDevicePercentage ($MostUsedDevicePercentage)
    {
        $this->MostUsedDevicePercentage = $MostUsedDevicePercentage;
        return $this;
    }

    /**
     *
     * @return the $AverageDeviceAge
     */
    public function getAverageDeviceAge ()
    {
        if (! isset($this->AverageDeviceAge))
        {
            $averageAge = 0;
            $cumulativeAge = 0;
            foreach ( $this->getDevices() as $device )
            {
                $cumulativeAge += $device->Age;
            }
            if ($cumulativeAge > 0)
                $averageAge = $cumulativeAge / $this->getDeviceCount();
            $this->AverageDeviceAge = $averageAge;
        }
        return $this->AverageDeviceAge;
    }

    /**
     *
     * @param $AverageDeviceAge field_type            
     */
    public function setAverageDeviceAge ($AverageDeviceAge)
    {
        $this->AverageDeviceAge = $AverageDeviceAge;
        return $this;
    }

    /**
     *
     * @return the $NumberOfUniquePurchasedModels
     */
    public function getNumberOfUniquePurchasedModels ()
    {
        if (! isset($this->NumberOfUniquePurchasedModels))
        {
            $numberOfModels = 0;
            $uniqueModelArray = array ();
            foreach ( $this->getPurchasedDevices() as $device )
            {
                if (! in_array($device->getMasterDevice()->PrinterModel, $uniqueModelArray))
                {
                    $numberOfModels ++;
                    $uniqueModelArray [] = $device->getMasterDevice()->PrinterModel;
                }
            }
            $this->NumberOfUniquePurchasedModels = $numberOfModels;
        }
        return $this->NumberOfUniquePurchasedModels;
    }

    /**
     *
     * @param $NumberOfUniquePurchasedModels field_type            
     */
    public function setNumberOfUniquePurchasedModels ($NumberOfUniquePurchasedModels)
    {
        $this->NumberOfUniquePurchasedModels = $NumberOfUniquePurchasedModels;
        return $this;
    }

    /**
     *
     * @return the $PercentageOfDevicesReportingPower
     */
    public function getPercentageOfDevicesReportingPower ()
    {
        if (! isset($this->PercentageOfDevicesReportingPower))
        {
            $this->PercentageOfDevicesReportingPower = $this->getNumberOfDevicesReportingPower() / $this->getDeviceCount();
        }
        return $this->PercentageOfDevicesReportingPower;
    }

    /**
     *
     * @param $PercentageOfDevicesReportingPower field_type            
     */
    public function setPercentageOfDevicesReportingPower ($PercentageOfDevicesReportingPower)
    {
        $this->PercentageOfDevicesReportingPower = $PercentageOfDevicesReportingPower;
        return $this;
    }

    /**
     *
     * @return the $NumberOfDevicesReportingPower
     */
    public function getNumberOfDevicesReportingPower ()
    {
        if (! isset($this->NumberOfDevicesReportingPower))
        {
            $this->getAveragePowerUsagePerMonth();
        }
        return $this->NumberOfDevicesReportingPower;
    }

    /**
     *
     * @param $NumberOfDevicesReportingPower field_type            
     */
    public function setNumberOfDevicesReportingPower ($NumberOfDevicesReportingPower)
    {
        $this->NumberOfDevicesReportingPower = $NumberOfDevicesReportingPower;
        return $this;
    }

    /**
     *
     * @return the $GrossMarginTotalMonthlyCost
     */
    public function getGrossMarginTotalMonthlyCost ()
    {
        if (! isset($this->GrossMarginTotalMonthlyCost))
        {
            $totalCost = new stdClass();
            $totalCost->BlackAndWhite = 0;
            $totalCost->Color = 0;
            $totalCost->Combined = 0;
            foreach ( $this->getPurchasedDevices() as $device )
            {
                $totalCost->BlackAndWhite += $device->GrossMarginMonthlyBlackAndWhiteCost;
                $totalCost->Color += $device->GrossMarginMonthlyColorCost;
            }
            $totalCost->Combined = $totalCost->BlackAndWhite + $totalCost->Color;
            $this->GrossMarginTotalMonthlyCost = $totalCost;
        }
        return $this->GrossMarginTotalMonthlyCost;
    }

    /**
     *
     * @param $GrossMarginTotalMonthlyCost field_type            
     */
    public function setGrossMarginTotalMonthlyCost ($GrossMarginTotalMonthlyCost)
    {
        $this->GrossMarginTotalMonthlyCost = $GrossMarginTotalMonthlyCost;
        return $this;
    }

    /**
     *
     * @return the $GrossMarginTotalMonthlyRevenue
     */
    public function getGrossMarginTotalMonthlyRevenue ()
    {
        if (! isset($this->GrossMarginTotalMonthlyRevenue))
        {
            $totalCost = new stdClass();
            $totalCost->BlackAndWhite = $this->getPageCounts()->Purchased->BlackAndWhite->Monthly * $this->getMPSBlackAndWhiteCPP();
            $totalCost->Color = $this->getPageCounts()->Purchased->Color->Monthly * $this->getMPSColorCPP();
            $totalCost->Combined = $totalCost->BlackAndWhite + $totalCost->Color;
            
            $this->GrossMarginTotalMonthlyRevenue = $totalCost;
        }
        return $this->GrossMarginTotalMonthlyRevenue;
    }

    /**
     *
     * @param $GrossMarginTotalMonthlyRevenue field_type            
     */
    public function setGrossMarginTotalMonthlyRevenue ($GrossMarginTotalMonthlyRevenue)
    {
        $this->GrossMarginTotalMonthlyRevenue = $GrossMarginTotalMonthlyRevenue;
        return $this;
    }

    /**
     *
     * @return the $NumberOfRepairs
     */
    public function getNumberOfRepairs ()
    {
        if (! isset($this->NumberOfRepairs))
        {
            $reportQuestions = $this->getReportQuestions();
            if (strcasecmp($reportQuestions [24]->getTextualAnswer(), "I know the exact amount") === 0)
            {
                $this->NumberOfRepairs = $reportQuestions [24]->NumericAnswer;
            }
            else
            {
                // Device count / 20 (which is the same as device count * .05)
                $this->NumberOfRepairs = $this->getDeviceCount() * 0.05;
            }
        }
        return $this->NumberOfRepairs;
    }

    /**
     *
     * @param $NumberOfRepairs field_type            
     */
    public function setNumberOfRepairs ($NumberOfRepairs)
    {
        $this->NumberOfRepairs = $NumberOfRepairs;
        return $this;
    }

    /**
     *
     * @return the $AverageTimeBetweenBreakdownAndFix
     */
    public function getAverageTimeBetweenBreakdownAndFix ()
    {
        if (! isset($this->AverageTimeBetweenBreakdownAndFix))
        {
            $reportQuestions = $this->getReportQuestions();
            $this->AverageTimeBetweenBreakdownAndFix = $reportQuestions [20]->getNumericAnswer();
        }
        return $this->AverageTimeBetweenBreakdownAndFix;
    }

    /**
     *
     * @param $AverageTimeBetweenBreakdownAndFix field_type            
     */
    public function setAverageTimeBetweenBreakdownAndFix ($AverageTimeBetweenBreakdownAndFix)
    {
        $this->AverageTimeBetweenBreakdownAndFix = $AverageTimeBetweenBreakdownAndFix;
        return $this;
    }

    /**
     *
     * @return the $AnnualDowntimeFromBreakdowns
     */
    public function getAnnualDowntimeFromBreakdowns ()
    {
        if (! isset($this->AnnualDowntimeFromBreakdowns))
        {
            // convert to hours (8hrs = 1day : 4hrs = 1/2day) breakdowns *
            // (repairtime * 8)
            $downtime = $this->getNumberOfRepairs() * ($this->getAverageTimeBetweenBreakdownAndFix() * 8) * 12;
            $this->AnnualDowntimeFromBreakdowns = $downtime;
        }
        return $this->AnnualDowntimeFromBreakdowns;
    }

    /**
     *
     * @param $AnnualDowntimeFromBreakdowns field_type            
     */
    public function setAnnualDowntimeFromBreakdowns ($AnnualDowntimeFromBreakdowns)
    {
        $this->AnnualDowntimeFromBreakdowns = $AnnualDowntimeFromBreakdowns;
        return $this;
    }

    /**
     *
     * @return the $NumberOfVendors
     */
    public function getNumberOfVendors ()
    {
        if (! isset($this->NumberOfVendors))
        {
            $reportQuestions = $this->getReportQuestions();
            $this->NumberOfVendors = $reportQuestions [16]->getNumericAnswer();
        }
        return $this->NumberOfVendors;
    }

    /**
     *
     * @param $NumberOfVendors field_type            
     */
    public function setNumberOfVendors ($NumberOfVendors)
    {
        $this->NumberOfVendors = $NumberOfVendors;
        return $this;
    }

    /**
     *
     * @return the $NumberOfAnnualInkTonerOrders
     */
    public function getNumberOfAnnualInkTonerOrders ()
    {
        if (! isset($this->NumberOfAnnualInkTonerOrders))
        {
            $this->NumberOfAnnualInkTonerOrders = $this->getNumberOfOrdersPerMonth() * 12;
        }
        return $this->NumberOfAnnualInkTonerOrders;
    }

    /**
     *
     * @param $NumberOfAnnualInkTonerOrders field_type            
     */
    public function setNumberOfAnnualInkTonerOrders ($NumberOfAnnualInkTonerOrders)
    {
        $this->NumberOfAnnualInkTonerOrders = $NumberOfAnnualInkTonerOrders;
        return $this;
    }

    /**
     *
     * @return the $NumberOfUniquePurchasedToners
     */
    public function getNumberOfUniquePurchasedToners ()
    {
        if (! isset($this->NumberOfUniquePurchasedToners))
        {
            $this->NumberOfUniquePurchasedToners = count($this->getUniquePurchasedTonerList());
        }
        return $this->NumberOfUniquePurchasedToners;
    }

    /**
     *
     * @param $NumberOfUniquePurchasedToners field_type            
     */
    public function setNumberOfUniquePurchasedToners ($NumberOfUniquePurchasedToners)
    {
        $this->NumberOfUniquePurchasedToners = $NumberOfUniquePurchasedToners;
        return $this;
    }

    /**
     *
     * @return the $PercentPrintingDoneOnInkjet
     */
    public function getPercentPrintingDoneOnInkjet ()
    {
        if (! isset($this->PercentPrintingDoneOnInkjet))
        {
            $reportQuestions = $this->getReportQuestions();
            $this->PercentPrintingDoneOnInkjet = $reportQuestions [23]->getNumericAnswer();
        }
        return $this->PercentPrintingDoneOnInkjet;
    }

    /**
     *
     * @param $PercentPrintingDoneOnInkjet field_type            
     */
    public function setPercentPrintingDoneOnInkjet ($PercentPrintingDoneOnInkjet)
    {
        $this->PercentPrintingDoneOnInkjet = $PercentPrintingDoneOnInkjet;
        return $this;
    }

    /**
     *
     * @return the $HighRiskDevices
     */
    public function getHighRiskDevices ()
    {
        if (! isset($this->HighRiskDevices))
        {
            $deviceArraySortedByUsage = $this->getDevices();
            $deviceArraySortedByAge = $this->getDevices();
            $deviceArraySortedByRiskRanking = $this->getDevices();
            usort($deviceArraySortedByUsage, array (
                    $this, 
                    "sortDevicesByLifeUsage" 
            ));
            usort($deviceArraySortedByAge, array (
                    $this, 
                    "sortDevicesByAge" 
            ));
            // setting the age rank for each device
            $ctr = 1;
            foreach ( $deviceArraySortedByAge as $device )
            {
                $device->setAgeRank($ctr);
                $ctr ++;
            }
            
            // setting the life usage rank for each device
            $ctr = 1;
            foreach ( $deviceArraySortedByAge as $device )
            {
                $device->setLifeUsageRank($ctr);
                $ctr ++;
            }
            // setting the risk ranking based on age and life usage rank
            foreach ( $this->getDevices() as $device )
                $device->setRiskRank($device->getLifeUsageRank() + $device->getAgeRank());
                
                // sorting devices based on risk ranking
            usort($deviceArraySortedByRiskRanking, array (
                    $this, 
                    "sortDevicesByRiskRanking" 
            ));
            $this->HighRiskDevices = $deviceArraySortedByRiskRanking;
        }
        return $this->HighRiskDevices;
    }

    /**
     *
     * @param $HighRiskDevices field_type            
     */
    public function setHighRiskDevices ($HighRiskDevices)
    {
        $this->HighRiskDevices = $HighRiskDevices;
        return $this;
    }

    /**
     * Callback function for usort when we want to sort devices based on life
     * usage
     *
     * @param $a Proposalgen_Model_DeviceInstance            
     * @param $b Proposalgen_Model_DeviceInstance            
     */
    public function sortDevicesByLifeUsage ($deviceA, $deviceB)
    {
        if ($deviceA->getLifeUsage() == $deviceB->getLifeUsage())
        {
            return 0;
        }
        return ($deviceA->getLifeUsage() < $deviceB->getLifeUsage()) ? - 1 : 1;
    }

    /**
     * Callback function for usort when we want to sort devices based on age
     *
     * @param $a Proposalgen_Model_DeviceInstance            
     * @param $b Proposalgen_Model_DeviceInstance            
     */
    public function sortDevicesByAge ($deviceA, $deviceB)
    {
        if ($deviceA->getAge() == $deviceB->getAge())
        {
            return 0;
        }
        return ($deviceA->getAge() < $deviceB->getAge()) ? - 1 : 1;
    }

    /**
     * Callback function for usort when we want to sort devices based their risk
     * ranking
     *
     * @param $a Proposalgen_Model_DeviceInstance            
     * @param $b Proposalgen_Model_DeviceInstance            
     */
    public function sortDevicesByRiskRanking ($deviceA, $deviceB)
    {
        if ($deviceA->RiskRank == $deviceB->RiskRank)
        {
            return 0;
        }
        return ($deviceA->RiskRank > $deviceB->RiskRank) ? - 1 : 1;
    }

    /**
     *
     * @return the $WeeklyITHours
     */
    public function getWeeklyITHours ()
    {
        if (! isset($this->WeeklyITHours))
        {
            $reportQuestions = $this->getReportQuestions();
            if (strcasecmp($reportQuestions [18]->getTextualAnswer(), "I know the exact amount") === 0)
            {
                $this->WeeklyITHours = $reportQuestions [18]->getNumericAnswer();
            }
            else
            {
                $this->WeeklyITHours = $this->getDeviceCount() * 0.25;
            }
        }
        return $this->WeeklyITHours;
    }

    /**
     *
     * @param $WeeklyITHours field_type            
     */
    public function setWeeklyITHours ($WeeklyITHours)
    {
        $this->WeeklyITHours = $WeeklyITHours;
        return $this;
    }

    /**
     *
     * @return the $AnnualITHours
     */
    public function getAnnualITHours ()
    {
        if (! isset($this->AnnualITHours))
        {
            $this->AnnualITHours = $this->getWeeklyITHours() * 52;
        }
        return $this->AnnualITHours;
    }

    /**
     *
     * @param $AnnualITHours field_type            
     */
    public function setAnnualITHours ($AnnualITHours)
    {
        $this->AnnualITHours = $AnnualITHours;
        return $this;
    }

    /**
     *
     * @return the $AverageITRate
     */
    public function getAverageITRate ()
    {
        if (! isset($this->AverageITRate))
        {
            $reportQuestions = $this->getReportQuestions();
            $this->AverageITRate = $reportQuestions [15]->getNumericAnswer();
        }
        return $this->AverageITRate;
    }

    /**
     *
     * @param $AverageITRate field_type            
     */
    public function setAverageITRate ($AverageITRate)
    {
        $this->AverageITRate = $AverageITRate;
        return $this;
    }

    /**
     *
     * @return the $AnnualITCost
     */
    public function getAnnualITCost ()
    {
        if (! isset($this->AnnualITCost))
        {
            $this->AnnualITCost = $this->getAverageITRate() * $this->getAnnualITHours();
        }
        return $this->AnnualITCost;
    }

    /**
     *
     * @param $AnnualITCost field_type            
     */
    public function setAnnualITCost ($AnnualITCost)
    {
        $this->AnnualITCost = $AnnualITCost;
        return $this;
    }

    /**
     *
     * @return the $Graphs
     */
    public function getGraphs ()
    {
        if (! isset($this->Graphs))
        {
            // Variables that could be settings
            $OD_AverageMonthlyPagesPerEmployee = 500;
            $OD_AverageMonthlyPages = 4200;
            $OD_AverageEmployeesPerDevice = 4.4;
            
            // Other variables used in several places
            $pageCounts = $this->getPageCounts();
            $reportQuestions = $this->getReportQuestions();
            $companyName = $this->getReport()->CustomerCompanyName;
            $employeeCount = $reportQuestions [5]->NumericAnswer;
            
            // Formatting variables
            $numberValueMarker = "N *sz0";
            $currencyValueMarker = "N $*sz2";
            $PrintIQSavingsBarGraph_currencyValueMarker = "N $*sz0";
            
            /**
             * -- PrintIQSavingsBarGraph
             */
            $highest = ($this->getPrintIQTotalCost() > $this->getTotalPurchasedAnnualCost()) ? $this->getPrintIQTotalCost() : $this->getTotalPurchasedAnnualCost();
            $barGraph = new gchart\gGroupedBarChart(600, 160);
            $barGraph->setHorizontal(true);
            $barGraph->setTitle("Annual Printing Costs for Purchased Hardware");
            $barGraph->setVisibleAxes(array (
                    'x' 
            ));
            $barGraph->addDataSet(array (
                    $this->getTotalPurchasedAnnualCost() 
            ));
            $barGraph->addColors(array (
                    "0194D2" 
            ));
            $barGraph->addDataSet(array (
                    $this->getPrintIQTotalCost() 
            ));
            $barGraph->setLegend(array (
                    "Current", 
                    "PrintIQ" 
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.3);
            $barGraph->setDataRange(0, $highest * 1.3);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("r");
            $barGraph->addColors(array (
                    "E21736" 
            ));
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($PrintIQSavingsBarGraph_currencyValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($PrintIQSavingsBarGraph_currencyValueMarker, "000000", "1", "-1", "11");
            
            $this->Graphs [] = $barGraph->getUrl();
            
            /**
             * -- LeasedVsPurchasedBarGraph
             */
            $highest = ($this->getLeasedDeviceCount() > $this->getPurchasedDeviceCount()) ? $this->getLeasedDeviceCount() : $this->getPurchasedDeviceCount();
            $barGraph = new gchart\gBarChart(280, 230);
            $barGraph->setVisibleAxes(array (
                    'y' 
            ));
            $barGraph->addDataSet(array (
                    $this->getLeasedDeviceCount() 
            ));
            $barGraph->addColors(array (
                    "E21736" 
            ));
            $barGraph->addDataSet(array (
                    $this->getPurchasedDeviceCount() 
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(70, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array (
                    "0194D2" 
            ));
            $barGraph->setLegend(array (
                    "Number of leased devices", 
                    "Number of purchased devices" 
            ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            
            $this->Graphs [] = $barGraph->getUrl();
            
            /**
             * -- LeasedVsPurchasedPageCountBarGraph
             */
            $highest = ($pageCounts->Leased->Combined->Monthly > $pageCounts->Purchased->Combined->Monthly) ? $pageCounts->Leased->Combined->Monthly : $pageCounts->Purchased->Combined->Monthly;
            $barGraph = new gchart\gBarChart(280, 230);
            
            $barGraph->setVisibleAxes(array (
                    'y' 
            ));
            $barGraph->addDataSet(array (
                    round($pageCounts->Leased->Combined->Monthly) 
            ));
            $barGraph->addColors(array (
                    "E21736" 
            ));
            $barGraph->addDataSet(array (
                    round($pageCounts->Purchased->Combined->Monthly) 
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.20);
            $barGraph->setDataRange(0, $highest * 1.20);
            $barGraph->setBarScale(70, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array (
                    "0194D2" 
            ));
            $barGraph->setLegend(array (
                    "Monthly pages on leased devices", 
                    "Monthly pages on purchased devices" 
            ));
            
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $this->Graphs [] = $barGraph->getUrl();
            
            /**
             * -- UniqueDevicesGraph
             */
            $legendItems = Array ();
            $numberOfModels = 0;
            $uniqueModelArray = array ();
            foreach ( $this->getPurchasedDevices() as $device )
            {
                if (array_key_exists($device->getMasterDevice()->PrinterModel, $uniqueModelArray))
                    $uniqueModelArray [$device->getMasterDevice()->PrinterModel] += 1;
                else
                {
                    // $legendItems[] =
                    // $device->getMasterDevice()->PrinterModel;
                    $uniqueModelArray [$device->getMasterDevice()->PrinterModel] = 1;
                }
            }
            $uniqueDevicesGraph = new gchart\gPie3DChart(350, 270);
            $uniqueDevicesGraph->addDataSet($uniqueModelArray);
            $uniqueDevicesGraph->addColors(array (
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
            $this->Graphs [] = $uniqueDevicesGraph->getUrl();
            
            /**
             * -- AverageMonthlyPagesBarGraph
             */
            $averagePageCount = round($pageCounts->Total->Combined->Monthly / $this->getDeviceCount(), 0);
            $highest = ($averagePageCount > $OD_AverageMonthlyPages) ? $averagePageCount : $OD_AverageMonthlyPages;
            $barGraph = new gchart\gBarChart(200, 300);
            $barGraph->setTitle("Average monthly pages|per networked printer");
            $barGraph->setVisibleAxes(array (
                    'y' 
            ));
            $barGraph->addDataSet(array (
                    $averagePageCount 
            ));
            $barGraph->addColors(array (
                    "E21736" 
            ));
            $barGraph->addDataSet(array (
                    $OD_AverageMonthlyPages 
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array (
                    "0194D2" 
            ));
            $barGraph->setLegend(array (
                    $companyName, 
                    "Average" 
            ));
            
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $this->Graphs [] = $barGraph->getUrl();
            
            /**
             * -- AverageMonthlyPagesPerEmployeeBarGraph
             */
            $pagesPerEmployee = round($pageCounts->Total->Combined->Monthly / $employeeCount);
            $highest = ($OD_AverageMonthlyPagesPerEmployee > $pagesPerEmployee) ? $OD_AverageMonthlyPagesPerEmployee : $pagesPerEmployee;
            $barGraph = new gchart\gBarChart(200, 300);
            $barGraph->setTitle("Average monthly pages|per employee");
            $barGraph->setVisibleAxes(array (
                    'y' 
            ));
            $barGraph->addDataSet(array (
                    $pagesPerEmployee 
            ));
            $barGraph->addColors(array (
                    "E21736" 
            ));
            $barGraph->addDataSet(array (
                    $OD_AverageMonthlyPagesPerEmployee 
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array (
                    "0194D2" 
            ));
            $barGraph->setLegend(array (
                    $companyName, 
                    "Average" 
            ));
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $this->Graphs [] = $barGraph->getUrl();
            
            /**
             * -- AverageMonthlyPagesPerEmployeeBarGraph
             */
            $devicesPerEmployee = round($employeeCount / $this->getDeviceCount(), 2);
            $highest = ($devicesPerEmployee > $OD_AverageEmployeesPerDevice) ? $devicesPerEmployee : $OD_AverageEmployeesPerDevice;
            $barGraph = new gchart\gBarChart(200, 300);
            $barGraph->setTitle("Employees per|printing device");
            $barGraph->setVisibleAxes(array (
                    'y' 
            ));
            $barGraph->addDataSet(array (
                    $devicesPerEmployee 
            ));
            $barGraph->addColors(array (
                    "E21736" 
            ));
            $barGraph->addDataSet(array (
                    $OD_AverageEmployeesPerDevice 
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array (
                    "0194D2" 
            ));
            $barGraph->setLegend(array (
                    $companyName, 
                    "Average" 
            ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $this->Graphs [] = $barGraph->getUrl();
            
            /**
             * -- Color Capable Devices Graph
             */
            $colorPercentage = 0;
            if ($this->getDeviceCount())
                $colorPercentage = round((($this->getNumberOfColorCapableDevices() / $this->getDeviceCount()) * 100), 2);
            
            $notcolorPercentage = 100 - $colorPercentage;
            $colorCapableGraph = new gchart\gPie3DChart(305, 210);
            $colorCapableGraph->setTitle("Color-Capable Printing Devices");
            $colorCapableGraph->addDataSet(array (
                    $colorPercentage, 
                    $notcolorPercentage 
            ));
            $colorCapableGraph->setLegend(array (
                    "Color-capable", 
                    "Black-and-white only" 
            ));
            $colorCapableGraph->setLabels(array (
                    "$colorPercentage%" 
            ));
            $colorCapableGraph->addColors(array (
                    "E21736", 
                    "0194D2" 
            ));
            $colorCapableGraph->setLegendPosition("bv");
            $this->Graphs [] = $colorCapableGraph->getUrl();
            
            /**
             * -- ColorVSBWPagesGraph
             */
            $colorPercentage = 0;
            if ($pageCounts->Total->Combined->Monthly > 0)
                $colorPercentage = round((($pageCounts->Total->Color->Monthly / $pageCounts->Total->Combined->Monthly) * 100), 2);
            
            $bwPercentage = 100 - $colorPercentage;
            $colorVSBWPagesGraph = new gchart\gPie3DChart(305, 210);
            $colorVSBWPagesGraph->setTitle("Color vs Black/White Pages");
            $colorVSBWPagesGraph->addDataSet(array (
                    $colorPercentage, 
                    $bwPercentage 
            ));
            $colorVSBWPagesGraph->setLegend(array (
                    "Color pages printed", 
                    "Black-and-white pages printed" 
            ));
            $colorVSBWPagesGraph->setLabels(array (
                    "$colorPercentage%", 
                    "$bwPercentage%" 
            ));
            $colorVSBWPagesGraph->addColors(array (
                    "E21736", 
                    "0194D2" 
            ));
            $colorVSBWPagesGraph->setLegendPosition("bv");
            $this->Graphs [] = $colorVSBWPagesGraph->getUrl();
            
            /**
             * -- Device Ages Graph
             */
            $deviceAges = array (
                    "Less than 5 years old" => 0, 
                    "5-6 years old" => 0, 
                    "7-8 years old" => 0, 
                    "More than 8 years old" => 0 
            );
            foreach ( $this->getPurchasedDevices() as $device )
            {
                if ($device->Age < 5)
                {
                    $deviceAges ["Less than 5 years old"] ++;
                }
                else if ($device->Age <= 6)
                {
                    $deviceAges ["5-6 years old"] ++;
                }
                else if ($device->Age <= 8)
                {
                    $deviceAges ["7-8 years old"] ++;
                }
                else
                {
                    $deviceAges ["More than 8 years old"] ++;
                }
            }
            $dataset = array ();
            $legendItems = array ();
            
            foreach ( $deviceAges as $legendItem => $count )
            {
                if ($count > 0)
                {
                    $dataset [] = $count;
                    $legendItems [] = $legendItem;
                    $percentage = round(($count / $this->getPurchasedDeviceCount()) * 100, 2);
                    $labels [] = "$percentage%";
                }
            }
            $deviceAgeGraph = new gchart\gPie3DChart(350, 200);
            $deviceAgeGraph->addDataSet($dataset);
            $deviceAgeGraph->setLegend($legendItems);
            $deviceAgeGraph->setLabels($labels);
            $deviceAgeGraph->addColors(array (
                    "E21736", 
                    "0094cf", 
                    "5c3f9b", 
                    "adba1d" 
            ));
            $deviceAgeGraph->setLegendPosition("bv");
            $this->Graphs [] = $deviceAgeGraph->getUrl();
            
            /**
             * -- ScanCapableDevicesGraph
             */
            if ($this->getDeviceCount())
                $scanPercentage = round((($this->getNumberOfScanCapableDevices() / $this->getDeviceCount()) * 100), 2);
            else
                $scanPercentage = 0;
            $notScanPercentage = 100 - $scanPercentage;
            $scanCapableGraph = new gchart\gPie3DChart(200, 160);
            $scanCapableGraph->setTitle("Scan-Capable Printing Devices");
            $scanCapableGraph->addDataSet(array (
                    $scanPercentage, 
                    $notScanPercentage 
            ));
            $scanCapableGraph->setLegend(array (
                    "Scan capable", 
                    "Not scan capable" 
            ));
            $scanCapableGraph->setLabels(array (
                    "$scanPercentage%" 
            ));
            $scanCapableGraph->addColors(array (
                    "E21736", 
                    "0194D2" 
            ));
            $scanCapableGraph->setLegendPosition("bv");
            $this->Graphs [] = $scanCapableGraph->getUrl();
            
            /**
             * -- FaxCapableDevicesGraph
             */
            $faxPercentage = 0;
            if ($this->getDeviceCount())
                $faxPercentage = round((($this->getNumberOfFaxCapableDevices() / $this->getDeviceCount()) * 100), 2);
            
            $notfaxPercentage = 100 - $faxPercentage;
            $faxCapable = new gchart\gPie3DChart(200, 160);
            $faxCapable->setTitle("Fax-Capable Printing Devices");
            $faxCapable->addDataSet(array (
                    $faxPercentage, 
                    $notfaxPercentage 
            ));
            $faxCapable->setLegend(array (
                    "Fax capable", 
                    "Not fax capable" 
            ));
            $faxCapable->setLabels(array (
                    "$faxPercentage%" 
            ));
            $faxCapable->addColors(array (
                    "E21736", 
                    "0194D2" 
            ));
            $faxCapable->setLegendPosition("bv");
            $this->Graphs [] = $faxCapable->getUrl();
            
            /**
             * -- SmallColorCapableDevicesGraph
             */
            $colorCapableGraph->setDimensions(200, 160);
            $this->Graphs [] = $colorCapableGraph->getUrl();
            
            /**
             * -- DuplexCapableDevicesGraph
             */
            $duplexPercentage = 0;
            if ($this->getDeviceCount())
                $duplexPercentage = round((($this->getNumberOfDuplexCapableDevices() / $this->getDeviceCount()) * 100), 2);
            
            $notduplexPercentage = 100 - $duplexPercentage;
            $duplexCapableGraph = new gchart\gPie3DChart(305, 210);
            $duplexCapableGraph->setTitle("Duplex-Capable Printing Devices");
            $duplexCapableGraph->addDataSet(array (
                    $duplexPercentage, 
                    $notduplexPercentage 
            ));
            $duplexCapableGraph->setLegend(array (
                    "Duplex capable", 
                    "Not duplex capable" 
            ));
            $duplexCapableGraph->setLabels(array (
                    "$duplexPercentage%" 
            ));
            $duplexCapableGraph->addColors(array (
                    "E21736", 
                    "0194D2" 
            ));
            $duplexCapableGraph->setLegendPosition("bv");
            $this->Graphs [] = $duplexCapableGraph->getUrl();
            
            /**
             * -- BigScanCapableDevicesGraph
             */
            $scanCapableGraph->setDimensions(305, 210);
            $this->Graphs [] = $scanCapableGraph->getUrl();
        }
        return $this->Graphs;
    }

    /**
     *
     * @param $Graphs field_type            
     */
    public function setGraphs ($Graphs)
    {
        $this->Graphs = $Graphs;
        return $this;
    }

    /**
     *
     * @return the $CostOfExecutingSuppliesOrders
     */
    public function getCostOfExecutingSuppliesOrders ()
    {
        if (! isset($this->CostOfExecutingSuppliesOrders))
        {
            $reportQuestions = $this->getReportQuestions();
            $this->CostOfExecutingSuppliesOrders = $reportQuestions [14]->getNumericAnswer() * $this->getNumberOfAnnualInkTonerOrders();
        }
        return $this->CostOfExecutingSuppliesOrders;
    }

    /**
     *
     * @param $CostOfExecutingSuppliesOrders field_type            
     */
    public function setCostOfExecutingSuppliesOrders ($CostOfExecutingSuppliesOrders)
    {
        $this->CostOfExecutingSuppliesOrders = $CostOfExecutingSuppliesOrders;
        return $this;
    }

    /**
     *
     * @return the $EstimatedAnnualSupplyRelatedExpense
     */
    public function getEstimatedAnnualSupplyRelatedExpense ()
    {
        if (! isset($this->EstimatedAnnualSupplyRelatedExpense))
        {
            $this->EstimatedAnnualSupplyRelatedExpense = $this->getCostOfInkAndToner() + $this->getCostOfExecutingSuppliesOrders();
        }
        return $this->EstimatedAnnualSupplyRelatedExpense;
    }

    /**
     *
     * @param $EstimatedAnnualSupplyRelatedExpense field_type            
     */
    public function setEstimatedAnnualSupplyRelatedExpense ($EstimatedAnnualSupplyRelatedExpense)
    {
        $this->EstimatedAnnualSupplyRelatedExpense = $EstimatedAnnualSupplyRelatedExpense;
        return $this;
    }

    /**
     *
     * @return the $AnnualCostOfOutSourcing
     */
    public function getAnnualCostOfOutSourcing ()
    {
        if (! isset($this->AnnualCostOfOutSourcing))
        {
            $reportQuestions = $this->getReportQuestions();
            if (strcasecmp($reportQuestions [12]->getTextualAnswer(), "I know the exact amount") === 0)
            {
                $this->AnnualCostOfOutSourcing = $reportQuestions [12]->getNumericAnswer();
            }
            else
            {
                $this->AnnualCostOfOutSourcing = $this->getPurchasedDeviceCount() * 200;
            }
        }
        return $this->AnnualCostOfOutSourcing;
    }

    /**
     *
     * @param $AnnualCostOfOutSourcing field_type            
     */
    public function setAnnualCostOfOutSourcing ($AnnualCostOfOutSourcing)
    {
        $this->AnnualCostOfOutSourcing = $AnnualCostOfOutSourcing;
        return $this;
    }

    /**
     *
     * @return the $EstimatedAnnualCostOfService
     */
    public function getEstimatedAnnualCostOfService ()
    {
        if (! isset($this->EstimatedAnnualCostOfService))
        {
            $this->EstimatedAnnualCostOfService = $this->getAnnualCostOfOutSourcing() + $this->getAnnualITCost();
        }
        return $this->EstimatedAnnualCostOfService;
    }

    /**
     *
     * @param $EstimatedAnnualCostOfService field_type            
     */
    public function setEstimatedAnnualCostOfService ($EstimatedAnnualCostOfService)
    {
        $this->EstimatedAnnualCostOfService = $EstimatedAnnualCostOfService;
        return $this;
    }

    /**
     *
     * @return the $TotalPurchasedAnnualCost
     */
    public function getTotalPurchasedAnnualCost ()
    {
        if (! isset($this->TotalPurchasedAnnualCost))
        {
            $this->TotalPurchasedAnnualCost = $this->getEstimatedAnnualCostOfService() + $this->getAnnualCostOfHardwarePurchases() + $this->getEstimatedAnnualSupplyRelatedExpense();
        }
        return $this->TotalPurchasedAnnualCost;
    }

    /**
     *
     * @param $TotalPurchasedAnnualCost field_type            
     */
    public function setTotalPurchasedAnnualCost ($TotalPurchasedAnnualCost)
    {
        $this->TotalPurchasedAnnualCost = $TotalPurchasedAnnualCost;
        return $this;
    }

    /**
     *
     * @return the $EstimatedAllInBlackAndWhiteCPP
     */
    public function getEstimatedAllInBlackAndWhiteCPP ()
    {
        if (! isset($this->EstimatedAllInBlackAndWhiteCPP))
        {
            $workingCPP = 0;
            $BWCPP = 0;
            $costOfBlackAndWhiteInkAndToner = 0;
            $costWithNoInkToner = $this->getTotalPurchasedAnnualCost() - $this->getCostOfInkAndToner();
            if ($this->getPageCounts()->Purchased->Combined->Yearly)
                $workingCPP = $costWithNoInkToner / $this->getPageCounts()->Purchased->Combined->Yearly;
            foreach ( $this->getPurchasedDevices() as $device )
                $costOfBlackAndWhiteInkAndToner += $device->getCostOfBlackAndWhiteInkAndToner();
            if ($this->getPageCounts()->Purchased->BlackAndWhite->Yearly)
                $BWCPP = $workingCPP + ($costOfBlackAndWhiteInkAndToner / ($this->getPageCounts()->Purchased->BlackAndWhite->Yearly / 12));
            $this->EstimatedAllInBlackAndWhiteCPP = $BWCPP;
        }
        return $this->EstimatedAllInBlackAndWhiteCPP;
    }

    /**
     *
     * @param $EstimatedAllInBlackAndWhiteCPP field_type            
     */
    public function setEstimatedAllInBlackAndWhiteCPP ($EstimatedAllInBlackAndWhiteCPP)
    {
        $this->EstimatedAllInBlackAndWhiteCPP = $EstimatedAllInBlackAndWhiteCPP;
        return $this;
    }

    /**
     *
     * @return the $EstimatedAllInColorCPP
     */
    public function getEstimatedAllInColorCPP ()
    {
        if (! isset($this->EstimatedAllInColorCPP))
        {
            $workingCPP = 0;
            $costOfColorInkAndToner = 0;
            $ColorCPP = 0;
            $costWithNoInkToner = $this->getTotalPurchasedAnnualCost() - $this->getCostOfInkAndToner();
            if ($this->getPageCounts()->Purchased->Combined->Yearly)
                $workingCPP = $costWithNoInkToner / $this->getPageCounts()->Purchased->Combined->Yearly;
            foreach ( $this->getPurchasedDevices() as $device )
                $costOfColorInkAndToner += $device->getCostOfColorInkAndToner();
            if ($this->getPageCounts()->Purchased->Color->Yearly)
                $ColorCPP = $workingCPP + ($costOfColorInkAndToner / ($this->getPageCounts()->Purchased->Color->Yearly / 12));
            $this->EstimatedAllInColorCPP = $ColorCPP;
        }
        return $this->EstimatedAllInColorCPP;
    }

    /**
     *
     * @param $EstimatedAllInColorCPP field_type            
     */
    public function setEstimatedAllInColorCPP ($EstimatedAllInColorCPP)
    {
        $this->EstimatedAllInColorCPP = $EstimatedAllInColorCPP;
        return $this;
    }

    /**
     *
     * @return the $MPSBlackAndWhiteCPP
     */
    public function getMPSBlackAndWhiteCPP ()
    {
        if (! isset($this->MPSBlackAndWhiteCPP))
        {
            $this->MPSBlackAndWhiteCPP = $this->getReport()->getReportMPSBWPerPage();
        }
        return $this->MPSBlackAndWhiteCPP;
    }

    /**
     *
     * @param $MPSBlackAndWhiteCPP field_type            
     */
    public function setMPSBlackAndWhiteCPP ($MPSBlackAndWhiteCPP)
    {
        $this->MPSBlackAndWhiteCPP = $MPSBlackAndWhiteCPP;
        return $this;
    }

    /**
     *
     * @return the $MPSColorCPP
     */
    public function getMPSColorCPP ()
    {
        if (! isset($this->MPSColorCPP))
        {
            $this->MPSColorCPP = $this->getReport()->getReportMPSColorPerPage();
        }
        return $this->MPSColorCPP;
    }

    /**
     *
     * @param $MPSColorCPP field_type            
     */
    public function setMPSColorCPP ($MPSColorCPP)
    {
        $this->MPSColorCPP = $MPSColorCPP;
        return $this;
    }

    /**
     *
     * @return the $InternalAdminCost
     */
    public function getInternalAdminCost ()
    {
        if (! isset($this->InternalAdminCost))
        {
            $reportQuestions = $this->getReportQuestions();
            $this->InternalAdminCost = $reportQuestions [14]->getNumericAnswer() * 12;
        }
        return $this->InternalAdminCost;
    }

    /**
     *
     * @param $InternalAdminCost field_type            
     */
    public function setInternalAdminCost ($InternalAdminCost)
    {
        $this->InternalAdminCost = $InternalAdminCost;
        return $this;
    }

    /**
     *
     * @return the $PrintIQTotalCost
     */
    public function getPrintIQTotalCost ()
    {
        if (! isset($this->PrintIQTotalCost))
        {
            $this->PrintIQTotalCost = $this->getInternalAdminCost() + ($this->getAnnualITCost() * 0.5) + ($this->getPageCounts()->Purchased->Color->Yearly * $this->getMPSColorCPP()) + ($this->getPageCounts()->Purchased->BlackAndWhite->Yearly * $this->getMPSBlackAndWhiteCPP()) + $this->getAnnualCostOfHardwarePurchases();
        }
        return $this->PrintIQTotalCost;
    }

    /**
     *
     * @param $PrintIQTotalCost field_type            
     */
    public function setPrintIQTotalCost ($PrintIQTotalCost)
    {
        $this->PrintIQTotalCost = $PrintIQTotalCost;
        return $this;
    }

    /**
     *
     * @return the $PrintIQSavings
     */
    public function getPrintIQSavings ()
    {
        if (! isset($this->PrintIQSavings))
        {
            $this->PrintIQSavings = $this->getTotalPurchasedAnnualCost() - $this->getPrintIQTotalCost();
        }
        return $this->PrintIQSavings;
    }

    /**
     *
     * @param $PrintIQSavings field_type            
     */
    public function setPrintIQSavings ($PrintIQSavings)
    {
        $this->PrintIQSavings = $PrintIQSavings;
        return $this;
    }

    /**
     *
     * @return the $UniqueVendorCount
     */
    public function getUniqueVendorCount ()
    {
        if (! isset($this->UniqueVendorCount))
        {
            $questions = $this->getReportQuestions();
            $this->UniqueVendorCount = $questions [16]->NumericAnswer;
        }
        return $this->UniqueVendorCount;
    }

    /**
     *
     * @param $UniqueVendorCount field_type            
     */
    public function setUniqueVendorCount ($UniqueVendorCount)
    {
        $this->UniqueVendorCount = $UniqueVendorCount;
        return $this;
    }

    /**
     *
     * @return the $NumberOfOrdersPerMonth
     */
    public function getNumberOfOrdersPerMonth ()
    {
        if (! isset($this->NumberOfOrdersPerMonth))
        {
            $reportQuestions = $this->getReportQuestions();
            $this->NumberOfOrdersPerMonth = $reportQuestions [17]->NumericAnswer;
        }
        return $this->NumberOfOrdersPerMonth;
    }

    /**
     *
     * @param $NumberOfOrdersPerMonth field_type            
     */
    public function setNumberOfOrdersPerMonth ($NumberOfOrdersPerMonth)
    {
        $this->NumberOfOrdersPerMonth = $NumberOfOrdersPerMonth;
        return $this;
    }

    /**
     *
     * @return the $EmployeeCount
     */
    public function getEmployeeCount ()
    {
        if (! isset($this->EmployeeCount))
        {
            $questions = $this->getReportQuestions();
            $this->EmployeeCount = $questions [5]->NumericAnswer;
        }
        return $this->EmployeeCount;
    }

    /**
     *
     * @param $EmployeeCount field_type            
     */
    public function setEmployeeCount ($EmployeeCount)
    {
        $this->EmployeeCount = $EmployeeCount;
        return $this;
    }

    /**
     *
     * @return the $AverageOperatingWatts
     */
    public function getAverageOperatingWatts ()
    {
        if (! isset($this->AverageOperatingWatts))
        {
            $totalWatts = 0;
            foreach ( $this->getDevices() as $device )
            {
                $totalWatts += $device->MasterDevice->WattsPowerNormal;
            }
            $this->AverageOperatingWatts = ($totalWatts > 0) ? $totalWatts / $this->getDeviceCount() : 0;
        }
        return $this->AverageOperatingWatts;
    }

    /**
     *
     * @param $AverageOperatingWatts field_type            
     */
    public function setAverageOperatingWatts ($AverageOperatingWatts)
    {
        $this->AverageOperatingWatts = $AverageOperatingWatts;
        return $this;
    }

    /**
     *
     * @return the $ReplacementDevices
     */
    public function getReplacementDevices ()
    {
        if (! isset($this->ReplacementDevices))
        {
            $this->ReplacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->fetchCheapestForEachCategory();
        }
        return $this->ReplacementDevices;
    }

    /**
     *
     * @param $ReplacementDevices field_type            
     */
    public function setReplacementDevices ($ReplacementDevices)
    {
        $this->ReplacementDevices = $ReplacementDevices;
        return $this;
    }

    /**
     *
     * @return the $ReplacementDeviceCount
     */
    public function getReplacementDeviceCount ()
    {
        if (! isset($this->ReplacementDeviceCount))
        {
            $this->ReplacementDeviceCount = count($this->getReplacementDevices());
        }
        return $this->ReplacementDeviceCount;
    }

    /**
     *
     * @param $ReplacementDeviceCount field_type            
     */
    public function setReplacementDeviceCount ($ReplacementDeviceCount)
    {
        $this->ReplacementDeviceCount = $ReplacementDeviceCount;
        return $this;
    }

    public function getReplacement ($type)
    {
        $replacementDevice = null;
        $monthlyRate = 0;
        foreach ( $this->getReplacementDevices() as $device )
        {
            if ($device->ReplacementCategory == $type && ($device->getMonthlyRate() < $monthlyRate || $monthlyRate == 0))
            {
                $replacementDevice = $device;
                $monthlyRate = $device->getMonthlyRate();
            }
        }
        return $replacementDevice;
    }

    /**
     *
     * @return the $DevicesToBeReplaced
     */
    public function getDevicesToBeReplaced ()
    {
        if (! isset($this->DevicesToBeReplaced))
        {
            $minimumSavings = 20;
            $ampvThreshhold = 7000;
            
            $replacedDevices = new stdClass();
            $replacedDevices->BlackAndWhite = array ();
            $replacedDevices->BlackAndWhiteMFP = array ();
            $replacedDevices->Color = array ();
            $replacedDevices->ColorMFP = array ();
            $replacedDevices->NoTonerLevels = array ();
            
            // It's over 9000! (Really we mean over 10,000 pages printed)
            $replacedDevices->OverMaxLeasedCapacity = array ();
            $replacedDevices->LeftOver = array ();
            
            $replacementDevices = $this->getReplacementDevices();
            foreach ( $this->getPurchasedDevices() as $device )
            {
                $deviceReplaced = false;
                if ($device->getAverageMonthlyPageCount() >= $ampvThreshhold)
                {
                    $replacedDevices->OverMaxLeasedCapacity [] = $device;
                }
                else
                {
                    // If we are here the device is with in the page count and supports JIT?
                    switch ($device->getMasterDevice()->TonerConfigId)
                    {
                        case Proposalgen_Model_TonerConfig::BLACK_ONLY :
                            if ($device->getMasterDevice()->IsFax || $device->getMasterDevice()->IsScanner || $device->getMasterDevice()->IsCopier)
                            {
                                $savings = $device->getMonthlyRate() - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_BWMFP]->getMonthlyRate();
                                // MFP
                                if ($savings >= $minimumSavings)
                                {
                                    $replacedDevices->BlackAndWhiteMFP [] = $device;
                                    $deviceReplaced = true;
                                }
                            }
                            else
                            {
                                $savings = $device->getMonthlyRate() - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_BW]->getMonthlyRate();
                                if ($savings >= $minimumSavings)
                                {
                                    $replacedDevices->BlackAndWhite [] = $device;
                                    $deviceReplaced = true;
                                }
                            }
                            break;
                        case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED :
                        case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED :
                        case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED :
                            if ($device->getMasterDevice()->IsFax || $device->getMasterDevice()->IsScanner || $device->getMasterDevice()->IsCopier)
                            {
                                // MFP
                                $savings = $device->getMonthlyRate() - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_COLORMFP]->getMonthlyRate();
                                if ($savings >= $minimumSavings)
                                {
                                    $replacedDevices->ColorMFP [] = $device;
                                    $deviceReplaced = true;
                                }
                            }
                            else
                            {
                                $savings = $device->getMonthlyRate() - $replacementDevices [Proposalgen_Model_ReplacementDevice::REPLACMENT_COLOR]->getMonthlyRate();
                                if ($savings >= $minimumSavings)
                                {
                                    $replacedDevices->Color [] = $device;
                                    $deviceReplaced = true;
                                }
                            }
                            break;
                    }
                }
                if (! $deviceReplaced)
                {
                    if (! $device->JITSuppliesSupported)
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
     *
     * @param $DevicesToBeReplaced field_type            
     */
    public function setDevicesToBeReplaced ($DevicesToBeReplaced)
    {
        $this->DevicesToBeReplaced = $DevicesToBeReplaced;
        return $this;
    }

    /**
     *
     * @return the $LeftOverBlackAndWhitePageCount
     */
    public function getLeftOverBlackAndWhitePageCount ()
    {
        if (! isset($this->LeftOverBlackAndWhitePageCount))
        {
            $pageCount = 0;
            foreach ( $this->getDevicesToBeReplaced()->LeftOver as $device )
                $pageCount += $device->getAverageMonthlyBlackAndWhitePageCount();
            $this->LeftOverBlackAndWhitePageCount = $pageCount * 12;
        }
        return $this->LeftOverBlackAndWhitePageCount;
    }

    /**
     *
     * @param $LeftOverBlackAndWhitePageCount field_type            
     */
    public function setLeftOverBlackAndWhitePageCount ($LeftOverBlackAndWhitePageCount)
    {
        $this->LeftOverBlackAndWhitePageCount = $LeftOverBlackAndWhitePageCount;
        return $this;
    }

    /**
     *
     * @return the $LeftOverColorPageCount
     */
    public function getLeftOverColorPageCount ()
    {
        if (! isset($this->LeftOverColorPageCount))
        {
            $pageCount = 0;
            foreach ( $this->getDevicesToBeReplaced()->LeftOver as $device )
                $pageCount += $device->getAverageMonthlyColorPageCount();
            $this->LeftOverColorPageCount = $pageCount * 12;
        }
        return $this->LeftOverColorPageCount;
    }

    /**
     *
     * @param $LeftOverColorPageCount field_type            
     */
    public function setLeftOverColorPageCount ($LeftOverColorPageCount)
    {
        $this->LeftOverColorPageCount = $LeftOverColorPageCount;
        return $this;
    }

    /**
     *
     * @return the $LeftOverPrintIQCost
     */
    public function getLeftOverPrintIQCost ()
    {
        if (! isset($this->LeftOverPrintIQCost))
        {
            $this->LeftOverPrintIQCost = ($this->getLeftOverColorPageCount() * $this->getMPSColorCPP()) + ($this->getLeftOverBlackAndWhitePageCount() * $this->getMPSBlackAndWhiteCPP()) + $this->getInternalAdminCost() + ($this->getAnnualITCost() * 0.5);
        }
        return $this->LeftOverPrintIQCost;
    }

    /**
     *
     * @param $LeftOverPrintIQCost field_type            
     */
    public function setLeftOverPrintIQCost ($LeftOverPrintIQCost)
    {
        $this->LeftOverPrintIQCost = $LeftOverPrintIQCost;
        return $this;
    }

    /**
     *
     * @return the $LeftOverCostOfColorDevices
     */
    public function getLeftOverCostOfColorDevices ()
    {
        if (! isset($this->LeftOverCostOfColorDevices))
        {
            $cost = 0;
            foreach ( $this->getDevicesToBeReplaced()->BlackAndWhite as $device )
                $cost += $device->getMonthlyCost();
            foreach ( $this->getDevicesToBeReplaced()->BlackAndWhiteMFP as $device )
                $cost += $device->getMonthlyCost();
            $this->LeftOverCostOfColorDevices = $cost * 12;
        }
        return $this->LeftOverCostOfColorDevices;
    }

    /**
     *
     * @param $LeftOverCostOfColorDevices field_type            
     */
    public function setLeftOverCostOfColorDevices ($LeftOverCostOfColorDevices)
    {
        $this->LeftOverCostOfColorDevices = $LeftOverCostOfColorDevices;
        return $this;
    }

    /**
     *
     * @return the $LeftOverCostOfBlackAndWhiteDevices
     */
    public function getLeftOverCostOfBlackAndWhiteDevices ()
    {
        if (! isset($this->LeftOverCostOfBlackAndWhiteDevices))
        {
            $cost = 0;
            foreach ( $this->getDevicesToBeReplaced()->Color as $device )
                $cost += $device->getMonthlyCost();
            foreach ( $this->getDevicesToBeReplaced()->ColorMFP as $device )
                $cost += $device->getMonthlyCost();
            $this->LeftOverCostOfBlackAndWhiteDevices = $cost * 12;
        }
        return $this->LeftOverCostOfBlackAndWhiteDevices;
    }

    /**
     *
     * @param $LeftOverCostOfBlackAndWhiteDevices field_type            
     */
    public function setLeftOverCostOfBlackAndWhiteDevices ($LeftOverCostOfBlackAndWhiteDevices)
    {
        $this->LeftOverCostOfBlackAndWhiteDevices = $LeftOverCostOfBlackAndWhiteDevices;
        return $this;
    }

    /**
     *
     * @return the $CostOfRemainingDevices
     */
    public function getCostOfRemainingDevices ()
    {
        if (! isset($this->CostOfRemainingDevices))
        {
            $this->CostOfRemainingDevices = $this->getTotalPurchasedAnnualCost() - $this->getCurrentCostOfReplacedBlackAndWhitePrinters() - $this->getCurrentCostOfReplacedBlackAndWhiteMFPPrinters() - $this->getCurrentCostOfReplacedColorMFPPrinters() - $this->getCurrentCostOfReplacedColorPrinters();
        }
        return $this->CostOfRemainingDevices;
    }

    /**
     *
     * @param $CostOfRemainingDevices field_type            
     */
    public function setCostOfRemainingDevices ($CostOfRemainingDevices)
    {
        $this->CostOfRemainingDevices = $CostOfRemainingDevices;
        return $this;
    }

    /**
     *
     * @return the $CurrentCostOfReplacedColorMFPPrinters
     */
    public function getCurrentCostOfReplacedColorMFPPrinters ()
    {
        if (! isset($this->CurrentCostOfReplacedColorMFPPrinters))
        {
            $cost = 0;
            foreach ( $this->getDevicesToBeReplaced()->ColorMFP as $device )
                $cost += $device->getMonthlyRate();
            $this->CurrentCostOfReplacedColorMFPPrinters = $cost * 12;
        }
        return $this->CurrentCostOfReplacedColorMFPPrinters;
    }

    /**
     *
     * @return the $CurrentCostOfReplacedBlackAndWhiteMFPPrinters
     */
    public function getCurrentCostOfReplacedBlackAndWhiteMFPPrinters ()
    {
        if (! isset($this->CurrentCostOfReplacedBlackAndWhiteMFPPrinters))
        {
            $cost = 0;
            foreach ( $this->getDevicesToBeReplaced()->BlackAndWhiteMFP as $device )
                $cost += $device->getMonthlyRate();
            $this->CurrentCostOfReplacedBlackAndWhiteMFPPrinters = $cost * 12;
        }
        return $this->CurrentCostOfReplacedBlackAndWhiteMFPPrinters;
    }

    /**
     *
     * @return the $ProposedCostOfReplacedBlackAndWhiteMFPPrinters
     */
    public function getProposedCostOfReplacedBlackAndWhiteMFPPrinters ()
    {
        if (! isset($this->ProposedCostOfReplacedBlackAndWhiteMFPPrinters))
        {
            $countOfReplacedDevices = count($this->getDevicesToBeReplaced()->BlackAndWhiteMFP);
            $cost = $countOfReplacedDevices * $this->getReplacement('BLACK & WHITE MFP')->getMonthlyRate();
            $this->ProposedCostOfReplacedBlackAndWhiteMFPPrinters = $cost * 12;
        }
        return $this->ProposedCostOfReplacedBlackAndWhiteMFPPrinters;
    }

    /**
     *
     * @return the $ProposedCostOfReplacedColorMFPPrinters
     */
    public function getProposedCostOfReplacedColorMFPPrinters ()
    {
        if (! isset($this->ProposedCostOfReplacedColorMFPPrinters))
        {
            $countOfReplacedDevices = count($this->getDevicesToBeReplaced()->ColorMFP);
            $cost = $countOfReplacedDevices * $this->getReplacement('COLOR MFP')->getMonthlyRate();
            $this->ProposedCostOfReplacedColorMFPPrinters = $cost * 12;
        }
        return $this->ProposedCostOfReplacedColorMFPPrinters;
    }

    /**
     *
     * @param $CurrentCostOfReplacedColorMFPPrinters field_type            
     */
    public function setCurrentCostOfReplacedColorMFPPrinters ($CurrentCostOfReplacedColorMFPPrinters)
    {
        $this->CurrentCostOfReplacedColorMFPPrinters = $CurrentCostOfReplacedColorMFPPrinters;
        return $this;
    }

    /**
     *
     * @param $CurrentCostOfReplacedBlackAndWhiteMFPPrinters field_type            
     */
    public function setCurrentCostOfReplacedBlackAndWhiteMFPPrinters ($CurrentCostOfReplacedBlackAndWhiteMFPPrinters)
    {
        $this->CurrentCostOfReplacedBlackAndWhiteMFPPrinters = $CurrentCostOfReplacedBlackAndWhiteMFPPrinters;
        return $this;
    }

    /**
     *
     * @param $ProposedCostOfReplacedBlackAndWhiteMFPPrinters field_type            
     */
    public function setProposedCostOfReplacedBlackAndWhiteMFPPrinters ($ProposedCostOfReplacedBlackAndWhiteMFPPrinters)
    {
        $this->ProposedCostOfReplacedBlackAndWhiteMFPPrinters = $ProposedCostOfReplacedBlackAndWhiteMFPPrinters;
        return $this;
    }

    /**
     *
     * @param $ProposedCostOfReplacedColorMFPPrinters field_type            
     */
    public function setProposedCostOfReplacedColorMFPPrinters ($ProposedCostOfReplacedColorMFPPrinters)
    {
        $this->ProposedCostOfReplacedColorMFPPrinters = $ProposedCostOfReplacedColorMFPPrinters;
        return $this;
    }

    /**
     *
     * @return the $CurrentCostOfReplacedColorPrinters
     */
    public function getCurrentCostOfReplacedColorPrinters ()
    {
        if (! isset($this->CurrentCostOfReplacedColorPrinters))
        {
            $cost = 0;
            foreach ( $this->getDevicesToBeReplaced()->Color as $device )
                $cost += $device->getMonthlyRate();
            $this->CurrentCostOfReplacedColorPrinters = $cost * 12;
        }
        return $this->CurrentCostOfReplacedColorPrinters;
    }

    /**
     *
     * @param $CurrentCostOfReplacedColorPrinters field_type            
     */
    public function setCurrentCostOfReplacedColorPrinters ($CurrentCostOfReplacedColorPrinters)
    {
        $this->CurrentCostOfReplacedColorPrinters = $CurrentCostOfReplacedColorPrinters;
        return $this;
    }

    /**
     *
     * @return the $CurrentCostOfReplacedBlackAndWhitePrinters
     */
    public function getCurrentCostOfReplacedBlackAndWhitePrinters ()
    {
        if (! isset($this->CurrentCostOfReplacedBlackAndWhitePrinters))
        {
            $cost = 0;
            foreach ( $this->getDevicesToBeReplaced()->BlackAndWhite as $device )
                $cost += $device->getMonthlyRate();
            $this->CurrentCostOfReplacedBlackAndWhitePrinters = $cost * 12;
        }
        return $this->CurrentCostOfReplacedBlackAndWhitePrinters;
    }

    /**
     *
     * @param $CurrentCostOfReplacedBlackAndWhitePrinters field_type            
     */
    public function setCurrentCostOfReplacedBlackAndWhitePrinters ($CurrentCostOfReplacedBlackAndWhitePrinters)
    {
        $this->CurrentCostOfReplacedBlackAndWhitePrinters = $CurrentCostOfReplacedBlackAndWhitePrinters;
        return $this;
    }

    /**
     *
     * @return the $ProposedCostOfReplacedBlackAndWhitePrinters
     */
    public function getProposedCostOfReplacedBlackAndWhitePrinters ()
    {
        if (! isset($this->ProposedCostOfReplacedBlackAndWhitePrinters))
        {
            $countOfReplacedDevices = count($this->getDevicesToBeReplaced()->BlackAndWhite);
            $cost = $countOfReplacedDevices * $this->getReplacement('BLACK & WHITE')->getMonthlyRate();
            $this->ProposedCostOfReplacedBlackAndWhitePrinters = $cost * 12;
        }
        return $this->ProposedCostOfReplacedBlackAndWhitePrinters;
    }

    /**
     *
     * @param $ProposedCostOfReplacedBlackAndWhitePrinters field_type            
     */
    public function setProposedCostOfReplacedBlackAndWhitePrinters ($ProposedCostOfReplacedBlackAndWhitePrinters)
    {
        $this->ProposedCostOfReplacedBlackAndWhitePrinters = $ProposedCostOfReplacedBlackAndWhitePrinters;
        return $this;
    }

    /**
     *
     * @return the $ProposedCostOfReplacedColorPrinters
     */
    public function getProposedCostOfReplacedColorPrinters ()
    {
        if (! isset($this->ProposedCostOfReplacedColorPrinters))
        {
            $countOfReplacedDevices = count($this->getDevicesToBeReplaced()->Color);
            $cost = $countOfReplacedDevices * $this->getReplacement('COLOR')->getMonthlyRate();
            $this->ProposedCostOfReplacedColorPrinters = $cost * 12;
        }
        return $this->ProposedCostOfReplacedColorPrinters;
    }

    /**
     *
     * @param $ProposedCostOfReplacedColorPrinters field_type            
     */
    public function setProposedCostOfReplacedColorPrinters ($ProposedCostOfReplacedColorPrinters)
    {
        $this->ProposedCostOfReplacedColorPrinters = $ProposedCostOfReplacedColorPrinters;
        return $this;
    }

    /**
     *
     * @return the $TotalProposedAnnualCost
     */
    public function getTotalProposedAnnualCost ()
    {
        if (! isset($this->TotalProposedAnnualCost))
        {
            $this->TotalProposedAnnualCost = $this->getProposedCostOfReplacedColorPrinters() + $this->getProposedCostOfReplacedColorMFPPrinters() + $this->getProposedCostOfReplacedBlackAndWhitePrinters() + $this->getProposedCostOfReplacedBlackAndWhiteMFPPrinters() + $this->getLeftOverPrintIQCost();
        }
        return $this->TotalProposedAnnualCost;
    }

    /**
     *
     * @param $TotalProposedAnnualCost field_type            
     */
    public function setTotalProposedAnnualCost ($TotalProposedAnnualCost)
    {
        $this->TotalProposedAnnualCost = $TotalProposedAnnualCost;
        return $this;
    }

    /**
     *
     * @return the $TotalAnnualSavings
     */
    public function getTotalAnnualSavings ()
    {
        if (! isset($this->TotalAnnualSavings))
        {
            $this->TotalAnnualSavings = ($this->getCurrentCostOfReplacedBlackAndWhitePrinters() - $this->getProposedCostOfReplacedBlackAndWhitePrinters()) + ($this->getCurrentCostOfReplacedBlackAndWhiteMFPPrinters() - $this->getProposedCostOfReplacedBlackAndWhiteMFPPrinters()) + ($this->getCurrentCostOfReplacedColorPrinters() - $this->getProposedCostOfReplacedColorPrinters()) + ($this->getCurrentCostOfReplacedColorMFPPrinters() - $this->getProposedCostOfReplacedColorMFPPrinters()) + ($this->getCostOfRemainingDevices() - $this->getLeftOverPrintIQCost());
        }
        return $this->TotalAnnualSavings;
    }

    /**
     *
     * @param $TotalAnnualSavings field_type            
     */
    public function setTotalAnnualSavings ($TotalAnnualSavings)
    {
        $this->TotalAnnualSavings = $TotalAnnualSavings;
        return $this;
    }

    /**
     *
     * @return the $CostOfExecutingSuppliesOrder
     */
    public function getCostOfExecutingSuppliesOrder ()
    {
        if (! isset($this->CostOfExecutingSuppliesOrder))
        {
            $reportQuestions = $this->getReportQuestions();
            $this->CostOfExecutingSuppliesOrder = $reportQuestions [14]->getNumericAnswer();
        }
        return $this->CostOfExecutingSuppliesOrder;
    }

    /**
     *
     * @param $CostOfExecutingSuppliesOrder field_type            
     */
    public function setCostOfExecutingSuppliesOrder ($CostOfExecutingSuppliesOrder)
    {
        $this->CostOfExecutingSuppliesOrder = $CostOfExecutingSuppliesOrder;
        return $this;
    }

    /**
     *
     * @return the $DevicesReportingPowerThreshold
     */
    public function getDevicesReportingPowerThreshold ()
    {
        if (! isset($this->DevicesReportingPowerThreshold))
        {
            $this->DevicesReportingPowerThreshold = 0.25;
        }
        return $this->DevicesReportingPowerThreshold;
    }

    /**
     *
     * @param $DevicesReportingPowerThreshold field_type            
     */
    public function setDevicesReportingPowerThreshold ($DevicesReportingPowerThreshold)
    {
        $this->DevicesReportingPowerThreshold = $DevicesReportingPowerThreshold;
        return $this;
    }

    /**
     *
     * @return the $GrossMarginMonthlyProfit
     */
    public function getGrossMarginMonthlyProfit ()
    {
        if (! isset($this->GrossMarginMonthlyProfit))
        {
            $this->GrossMarginMonthlyProfit = $this->getGrossMarginTotalMonthlyRevenue()->Combined - $this->getGrossMarginTotalMonthlyCost()->Combined;
        }
        return $this->GrossMarginMonthlyProfit;
    }

    /**
     *
     * @param $GrossMarginMonthlyProfit field_type            
     */
    public function setGrossMarginMonthlyProfit ($GrossMarginMonthlyProfit)
    {
        $this->GrossMarginMonthlyProfit = $GrossMarginMonthlyProfit;
        return $this;
    }

    /**
     *
     * @return the $GrossMarginOverallMargin
     */
    public function getGrossMarginOverallMargin ()
    {
        if (! isset($this->GrossMarginOverallMargin))
        {
            $this->GrossMarginOverallMargin = $this->getGrossMarginMonthlyProfit() / $this->getGrossMarginTotalMonthlyRevenue()->Combined * 100;
        }
        return $this->GrossMarginOverallMargin;
    }

    /**
     *
     * @param $GrossMarginOverallMargin field_type            
     */
    public function setGrossMarginOverallMargin ($GrossMarginOverallMargin)
    {
        $this->GrossMarginOverallMargin = $GrossMarginOverallMargin;
        return $this;
    }

    /**
     *
     * @return the $GrossMarginWeightedCPP
     */
    public function getGrossMarginWeightedCPP ()
    {
        if (! isset($this->GrossMarginWeightedCPP))
        {
            $this->GrossMarginWeightedCPP = new stdClass();
            $this->GrossMarginWeightedCPP->BlackAndWhite = 0;
            $this->GrossMarginWeightedCPP->Color = 0;
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
     *
     * @param $GrossMarginWeightedCPP field_type            
     */
    public function setGrossMarginWeightedCPP ($GrossMarginWeightedCPP)
    {
        $this->GrossMarginWeightedCPP = $GrossMarginWeightedCPP;
        return $this;
    }

    /**
     *
     * @return the $GrossMarginBlackAndWhiteMargin
     */
    public function getGrossMarginBlackAndWhiteMargin ()
    {
        if (! isset($this->GrossMarginBlackAndWhiteMargin))
        {
            $this->GrossMarginBlackAndWhiteMargin = ($this->getMPSBlackAndWhiteCPP() - $this->getGrossMarginWeightedCPP()->BlackAndWhite) / $this->getMPSBlackAndWhiteCPP() * 100;
        }
        return $this->GrossMarginBlackAndWhiteMargin;
    }

    /**
     *
     * @param $GrossMarginBlackAndWhiteMargin field_type            
     */
    public function setGrossMarginBlackAndWhiteMargin ($GrossMarginBlackAndWhiteMargin)
    {
        $this->GrossMarginBlackAndWhiteMargin = $GrossMarginBlackAndWhiteMargin;
        return $this;
    }

    /**
     *
     * @return the $GrossMarginColorMargin
     */
    public function getGrossMarginColorMargin ()
    {
        if (! isset($this->GrossMarginColorMargin))
        {
            $this->GrossMarginColorMargin = ($this->getMPSColorCPP() - $this->getGrossMarginWeightedCPP()->Color) / $this->getMPSColorCPP() * 100;
            ;
        }
        return $this->GrossMarginColorMargin;
    }

    /**
     *
     * @param $GrossMarginColorMargin field_type            
     */
    public function setGrossMarginColorMargin ($GrossMarginColorMargin)
    {
        $this->GrossMarginColorMargin = $GrossMarginColorMargin;
        return $this;
    }

    /**
     *
     * @return the $UniqueTonerList
     */
    public function getUniqueTonerList ()
    {
        if (! isset($this->UniqueTonerList))
        {
            $uniqueToners = array ();
            foreach ( $this->getUniqueDeviceList() as $masterDevice )
            {
                $deviceToners = $masterDevice->getTonersForAssessment();
                foreach ( $deviceToners as $toner )
                {
                    if (! in_array($toner, $uniqueToners))
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
     *
     * @param $UniqueTonerList field_type            
     */
    public function setUniqueTonerList ($UniqueTonerList)
    {
        $this->UniqueTonerList = $UniqueTonerList;
        return $this;
    }

    /**
     *
     * @return the $UniquePurchasedDeviceList
     */
    public function getUniquePurchasedDeviceList ()
    {
        if (! isset($this->UniquePurchasedDeviceList))
        {
            $masterDevices = array ();
            foreach ( $this->getPurchasedDevices() as $device )
            {
                if (! in_array($device->MasterDevice, $masterDevices))
                {
                    $masterDevices [] = $device->MasterDevice;
                }
            }
            $this->UniquePurchasedDeviceList = $masterDevices;
        }
        return $this->UniquePurchasedDeviceList;
    }

    /**
     *
     * @param $UniquePurchasedDeviceList field_type            
     */
    public function setUniquePurchasedDeviceList ($UniquePurchasedDeviceList)
    {
        $this->UniquePurchasedDeviceList = $UniquePurchasedDeviceList;
        return $this;
    }

    /**
     *
     * @return the $UniquePurchasedTonerList
     */
    public function getUniquePurchasedTonerList ()
    {
        if (! isset($this->UniquePurchasedTonerList))
        {
            $uniqueToners = array ();
            foreach ( $this->getUniquePurchasedDeviceList() as $masterDevice )
            {
                $deviceToners = $masterDevice->getTonersForAssessment();
                foreach ( $deviceToners as $toner )
                {
                    if (! in_array($toner, $uniqueToners))
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
     *
     * @param $UniquePurchasedTonerList field_type            
     */
    public function setUniquePurchasedTonerList ($UniquePurchasedTonerList)
    {
        $this->UniquePurchasedTonerList = $UniquePurchasedTonerList;
        return $this;
    }

    /**
     *
     * @return the $UniqueDeviceList
     */
    public function getUniqueDeviceList ()
    {
        if (! isset($this->UniqueDeviceList))
        {
            $masterDevices = array ();
            foreach ( $this->getDevices() as $device )
            {
                if (! in_array($device->MasterDevice, $masterDevices))
                {
                    $masterDevices [] = $device->MasterDevice;
                }
            }
            $this->UniqueDeviceList = $masterDevices;
        }
        return $this->UniqueDeviceList;
    }

    /**
     *
     * @param $UniqueDeviceList field_type            
     */
    public function setUniqueDeviceList ($UniqueDeviceList)
    {
        $this->UniqueDeviceList = $UniqueDeviceList;
        return $this;
    }
}
?>