<?php

/**
 * Class Healthcheck_ViewModel_Healthcheck
 */
class Healthcheck_ViewModel_Healthcheck extends Healthcheck_ViewModel_Abstract
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
    const OLD_DEVICE_THRESHOLD   = 10;
    const GALLONS_WATER_PER_PAGE = 2.6; // Number of pages * this gives amount of gallons
    const PAGES_PER_TREE = 7800; //Number of pages divided by this, gives amount of trees
    public static $Proposal;

    // New Separated Proposal
    protected $Ranking;
    protected $ReportId;
    protected $DefaultToners;
    protected $User;
    protected $DealerCompany;
    protected $CompanyMargin;
    protected $healthcheckMargin;
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
    protected $NumberOfUniqueModels;
    protected $NumberOfUniquePurchasedModels;
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
    protected $_maximumMonthlyPurchasedPrintVolume;
    protected $_purchasedTotalMonthlyCost;
    protected $_purchasedColorMonthlyCost;
    protected $_purchasedMonochromeMonthlyCost;
    protected $_optimizedDevices;
    protected $_includedDevicesSortedAscendingByAge;
    protected $_includedDevicesSortedDescendingByAge;
    protected $_uniqueDeviceCountArray;
    protected $_deviceVendorCount;
    protected $_underutilizedA3Devices;
    protected $_pageCounts;
    public $highCostPurchasedDevices;
    public static $COLOR_ARRAY = array(
        "A8BB19", "7CB9E8", "C9FFE5", "B284BE", "5D8AA8", "00308F", "72A0C1", "AF002A", "E32636", "C46210",
        "EFDECD", "E52B50", "F19CBB", "3B7A57", "FFBF00", "FF7E00", "FF033E", "9966CC", "A4C639", "9FA91F",
        "CD9575", "665D1E", "915C83", "841B2D", "FAEBD7", "008000", "8DB600", "FBCEB1", "00FFFF", "E4D00A",
        "7FFFD4", "4B5320", "3B444B", "8F9779", "E9D66B", "B2BEB5", "87A96B", "FF9966", "A52A2A", "FDEE00",
        "6E7F80", "568203", "007FFF", "89CFF0", "A1CAF1", "F4C2C2", "FF91AF", "21ABCD", "E34234", "D2691E",
        "FAE7B5", "FFE135", "E0218A", "7C0A02", "848482", "98777B", "BCD4E6", "F4BBFF", "F5F5DC", "2E5894",
        "9C2542", "FFE4C4", "3D2B1F", "967117", "CAE00D", "648C11", "FE6F5E", "BF4F51", "000000", "3D0C02",
        "253529", "3B3C36", "FFEBCD", "A57164", "318CE7", "ACE5EE", "FAF0BE", "0000FF", "1F75FE", "0093AF",
        "0087BD", "333399", "0247FE", "A2A2D0", "6699CC", "0D98BA", "126180", "8A2BE2", "5072A7", "4F86F7",
        "1C1CF0", "DE5D83", "79443B", "0095B6", "E3DAC9", "CC0000", "006A4E", "873260", "0070FF", "B5A642",
        "CB4154", "1DACD6", "66FF00", "BF94E4", "D891EF", "C32148", "1974D2", "FF007F", "08E8DE", "D19FE8",
        "F4BBFF", "FF55A3", "FB607F", "004225", "CD7F32", "737000", "964B00", "A52A2A", "6B4423", "1B4D3E",
        "FFC1CC", "F0DC82", "7BB661", "480607", "800020", "DEB887", "CC5500", "E97451", "8A3324", "98817B",
        "BD33A4", "702963", "536872", "5F9EA0", "91A3B0", "006B3C", "ED872D", "E30022", "FFF600", "A67B5B",
        "4B3621", "1E4D2B", "A3C1AD", "C19A6B", "EFBBCC", "78866B", "FFEF00", "FF0800", "E4717A", "00BFFF",
        "592720", "C41E3A", "00CC99", "960018", "D70040", "EB4C42", "FF0038", "FFA6C9", "B31B1B", "99BADD",
        "ED9121", "00563F", "062A78", "703642", "C95A49", "92A1CF", "ACE1AF", "007BA7", "2F847C", "B2FFFF",
        "4997D0", "DE3163", "EC3B83", "007BA7", "2A52BE", "6D9BC3", "007AA5", "E03C31", "A0785A", "F7E7CE",
        "36454F", "232B2B", "E68FAC", "DFFF00", "7FFF00", "DE3163", "FFB7C5", "954535", "DE6FA1", "A8516E",
        "AA381E", "856088", "7B3F00", "D2691E", "FFA700",
    );

    /**
     * @var Proposalgen_Model_Client_Toner_Order[]
     */
    protected $_clientTonerOrders;

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

        Proposalgen_Model_DeviceInstance::$KWH_Cost = $healthcheckSettings->kilowattsPerHour;

        if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly() > 0)
        {
            Proposalgen_Model_DeviceInstance::$ITCostPerPage = (($this->getAnnualITCost() * 0.5 + $this->getAnnualCostOfOutSourcing()) / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly());
        }
        else
        {
            Proposalgen_Model_DeviceInstance::$ITCostPerPage = 0.0;
        }
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
     * @return float
     */
    public function getHealthcheckMargin ()
    {
        if (!isset($this->healthcheckMargin))
        {
            $this->healthcheckMargin = $this->healthcheck->getHealthcheckSettings()->healthcheckMargin;
        }

        return $this->healthcheckMargin;
    }

    /**
     * @return float
     */
    public function getCombinedAnnualLeasePayments ()
    {
        if (!isset($this->CombinedAnnualLeasePayments))
        {

            $this->CombinedAnnualLeasePayments = $this->healthcheck->getHealthcheckSettings()->monthlyLeasePayment * $this->getDevices()->leasedDeviceInstances->getCount() * 12;
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
                $this->AnnualCostOfHardwarePurchases = ($this->getDevices()->allIncludedDeviceInstances->getCount() / $averageAge) * $this->healthcheck->getHealthcheckSettings()->defaultPrinterCost;
            }
            else
            {
                $this->AnnualCostOfHardwarePurchases = 0;
            }
        }

        return $this->AnnualCostOfHardwarePurchases;
    }


    /**
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
                $totalCost += $device->getCostOfInkAndToner($costPerPageSetting);
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $maxVolume += $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer());
            }
            $this->MaximumMonthlyPrintVolume = $maxVolume;
        }

        return $this->MaximumMonthlyPrintVolume;
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
                if ($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()) * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE))
                {
                    $devicesUnderusedCount++;
                }
            }
            $this->PercentDevicesUnderused = ($devicesUnderusedCount / count($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances())) * 100;
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
                if ($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() > $deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()))
                {
                    $devicesOverusedCount++;
                }
            }
            $this->PercentDevicesOverused = ($devicesOverusedCount / count($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances())) * 100;
        }

        return $this->PercentDevicesOverused;
    }

    /**
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getUnderutilizedA3Devices ()
    {
        if (!isset($this->_underutilizedA3Devices))
        {
            $devicesArray = array();
            foreach ($this->getDevices()->a3DeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                if ($deviceInstance->getPageCounts()->getPrintA3CombinedPageCount()->getMonthly() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()) * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE))
                {
                    $devicesArray[] = $deviceInstance;
                }
            }

            usort($devicesArray, array(
                $this,
                "ascendingSortDevicesByA3Volume"
            ));
            $this->_underutilizedA3Devices = $devicesArray;
        }

        return $this->_underutilizedA3Devices;
    }

    /**
     * Callback function for uSort when we want to sort a device based on its a3 page volume
     *
     * @param $deviceA \Proposalgen_Model_DeviceInstance
     * @param $deviceB \Proposalgen_Model_DeviceInstance
     *
     * @return int
     */
    public function ascendingSortDevicesByA3Volume ($deviceA, $deviceB)
    {
        if ($deviceA->getPageCounts()->getPrintA3CombinedPageCount()->getMonthly() == $deviceB->getPageCounts()->getPrintA3CombinedPageCount()->getMonthly())
        {
            return 0;
        }

        return ($deviceA->getPageCounts()->getPrintA3CombinedPageCount()->getMonthly() < $deviceB->getPageCounts()->getPrintA3CombinedPageCount()->getMonthly()) ? -1 : 1;
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
                if ($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()) * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE))
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                if ($deviceInstance->getUsage($this->getCostPerPageSettingForCustomer()) > 1)
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
        if ($deviceA->getMonthlyRate($this->getCostPerPageSettingForCustomer()) == $deviceB->getMonthlyRate($this->getCostPerPageSettingForCustomer()))
        {
            return 0;
        }

        return ($deviceA->getMonthlyRate($this->getCostPerPageSettingForCustomer()) > $deviceB->getMonthlyRate($this->getCostPerPageSettingForCustomer())) ? -1 : 1;
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
                if (($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() < ($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($this->getCostPerPageSettingForCustomer()) * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE)) == false)
                {
                    //Check to see if it is not overUtilized
                    if ($deviceInstance->getUsage($this->getCostPerPageSettingForCustomer()) < 1)
                    {

                        //Check to see if it is under the age requirements
                        if ($deviceInstance->getAge() < self::OLD_DEVICE_THRESHOLD)
                        {
                            //Check to see if it is reporting toner levels
                            if ($deviceInstance->reportsTonerLevels || $deviceInstance->isLeased)
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
    public function getAverageAgeOfDevices ()
    {
        if (!isset($this->AverageAgeOfDevices))
        {
            $totalAge = 0;
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $totalAge += $deviceInstance->getAge();
            }
            $this->AverageAgeOfDevices = $totalAge / count($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances());
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
                    $costArray[] = array($key, $deviceInstance->getPageCounts()->getColorPageCount()->getMonthly() * $deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->colorCostPerPage);
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
     * Calculates the average cost per page for only toners that are OEM.
     *
     * @return Proposalgen_Model_CostPerPage
     */
    public function calculateAverageOemOnlyCostPerPage ()
    {
        if (!isset($this->_averageOemOnlyCostPerPage))
        {
            $costPerPageSetting                         = clone $this->getCostPerPageSettingForCustomer();
            $oemRankSet                                 = new Proposalgen_Model_Toner_Vendor_Ranking_Set();
            $costPerPageSetting->monochromeTonerRankSet = $oemRankSet;
            $costPerPageSetting->colorTonerRankSet      = $oemRankSet;

            $costPerPage                        = new Proposalgen_Model_CostPerPage();
            $costPerPage->monochromeCostPerPage = 0;
            $costPerPage->colorCostPerPage      = 0;
            $numberOfColorDevices               = 0;
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $costPerPage->add($deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostOfInkAndTonerPerPage());
                if ($deviceInstance->getMasterDevice()->isColor())
                {
                    $numberOfColorDevices++;
                }
            }
            $numberOfDevices = count($this->getDevices()->purchasedDeviceInstances->getDeviceInstances());
            if ($numberOfDevices > 0)
            {
                $costPerPage->monochromeCostPerPage = $costPerPage->monochromeCostPerPage / $numberOfDevices;
                if ($numberOfColorDevices > 0)
                {
                    $costPerPage->colorCostPerPage = $costPerPage->colorCostPerPage / $numberOfColorDevices;
                }
            }
            $costPerPage->monochromeCostPerPage = Tangent_Accounting::applyMargin($costPerPage->monochromeCostPerPage, $this->healthcheck->getHealthcheckSettings()->healthcheckMargin);
            $costPerPage->colorCostPerPage      = Tangent_Accounting::applyMargin($costPerPage->colorCostPerPage, $this->healthcheck->getHealthcheckSettings()->healthcheckMargin);
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
            $costPerPageSetting   = clone $this->getCostPerPageSettingForCustomer();
            $costPerPage          = new Proposalgen_Model_CostPerPage();
            $numberOfColorDevices = 0;
            foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $costPerPage->add($deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostOfInkAndTonerPerPage());
                if ($deviceInstance->getMasterDevice()->isColor())
                {
                    $numberOfColorDevices++;
                }
            }
            $numberOfDevices = count($this->getDevices()->purchasedDeviceInstances->getDeviceInstances());
            if ($numberOfDevices > 0)
            {
                $costPerPage->monochromeCostPerPage = $costPerPage->monochromeCostPerPage / $numberOfDevices;
                if ($numberOfColorDevices > 0)
                {
                    $costPerPage->colorCostPerPage = $costPerPage->colorCostPerPage / $numberOfDevices;
                }
            }
            $costPerPage->monochromeCostPerPage      = Tangent_Accounting::applyMargin($costPerPage->monochromeCostPerPage, $this->healthcheck->getHealthcheckSettings()->healthcheckMargin);
            $costPerPage->colorCostPerPage           = Tangent_Accounting::applyMargin($costPerPage->colorCostPerPage, $this->healthcheck->getHealthcheckSettings()->healthcheckMargin);
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
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getDevicesNotReportingTonerLevels ()
    {
        $devicesNotReportingTonerLevels = array();
        foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
        {
            if ($device->reportsTonerLevels == false)
            {
                $devicesNotReportingTonerLevels[] = $device;
            }
        }

        return $devicesNotReportingTonerLevels;
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
     * @return float
     */
    public function getWeeklyITHours ()
    {
        if (!isset($this->WeeklyITHours))
        {
            $this->WeeklyITHours = $this->healthcheck->getHealthcheckSettings()->hoursSpentOnIt;
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
            // Other variables used in several places
            $companyName   = $this->healthcheck->getClient()->companyName;
            $employeeCount = $this->healthcheck->getClient()->employeeCount;

            // Formatting variables
            $numberValueMarker = "N *sz0";
            //Graphs[2]
            $this->Graphs [] = "FILLER";

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
            $this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            /**
             * -- LeasedVsPurchasedPageCountBarGraph
             */
            $highest  = ($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() > $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) ? $this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() : $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            $barGraph = new gchart\gBarChart(225, 265);

            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(
                round($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())
            ));
            $barGraph->addColors(array(
                "E21736"
            ));
            $barGraph->addDataSet(array(
                round($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())
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
            $averagePageCount = round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getCount(), 0);
            $highest          = ($averagePageCount > Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_DEVICE) ? $averagePageCount : Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_DEVICE;
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
                Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_DEVICE
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
            $pagesPerEmployee = round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $employeeCount);
            $highest          = (Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE > $pagesPerEmployee) ? Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE : $pagesPerEmployee;
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
                Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE
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
            $highest            = ($devicesPerEmployee > Assessment_ViewModel_Assessment::AVERAGE_EMPLOYEES_PER_DEVICE) ? $devicesPerEmployee : Assessment_ViewModel_Assessment::AVERAGE_EMPLOYEES_PER_DEVICE;
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
                Assessment_ViewModel_Assessment::AVERAGE_EMPLOYEES_PER_DEVICE
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
            if ($this->getDevices()->allIncludedDeviceInstances->getCount())
            {
                $duplexPercentage = round((($this->getDevices()->duplexCapableDeviceInstances->getCount() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
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
            if ($this->getDevices()->allIncludedDeviceInstances->getCount())
            {
                $scanPercentage = round((($this->getDevices()->scanCapableDeviceInstances->getCount() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
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
                number_format($this->getDevices()->scanCapableDeviceInstances->getCount()),
                number_format($this->getDevices()->allIncludedDeviceInstances->getCount() - $this->getDevices()->scanCapableDeviceInstances->getCount())
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
     * @return $this
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
     * Calculates half the difference between OEM Total Cost Annually And Compatible
     *
     * @return float
     */
    public function calculateHalfDifferenceBetweenOemTotalCostAnnuallyAndCompAnnually ()
    {
        return $this->calculateDifferenceBetweenOemTotalCostAnnuallyAndCompAnnually() / 2;
    }

    /**
     * Calculates the difference between OEM Total Cost Annually And Compatible
     *
     * @return float
     */
    public function calculateDifferenceBetweenOemTotalCostAnnuallyAndCompAnnually ()
    {
        return $this->calculateEstimatedOemTonerCostAnnually() - $this->calculateEstimatedCompTonerCostAnnually();
    }

    /**
     * Calculates the difference between Oem Total Cost Monthly And Compatible Monthly
     *
     * @return float
     */
    public function calculateDifferenceBetweenOemTotalCostMonthlyAndCompMonthly ()
    {
        return $this->calculateDifferenceBetweenOemTotalCostAnnuallyAndCompAnnually() / 12;
    }

    /**
     * @return float
     */
    public function calculateEstimatedCompTonerCostAnnually ()
    {
        return ($this->calculateAverageCompatibleOnlyCostPerPage()->colorCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() + ($this->calculateAverageCompatibleOnlyCostPerPage()->monochromeCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly())) * 12;
    }

    /**
     * Calculates The Estimated OEM Toner Cost Annually.
     *
     * @return float
     */
    public function calculateEstimatedOemTonerCostAnnually ()
    {
        return ($this->calculateAverageOemOnlyCostPerPage()->colorCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() + ($this->calculateAverageOemOnlyCostPerPage()->monochromeCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly())) * 12;
    }

    /**
     * Calculates the percentage difference between Oem Total Cost Annually And Compatible
     *
     * @return float
     */
    public function calculateDifferencePercentageBetweenOemTotalCostAnnuallyAndCompAnnually ()
    {
        return 1 - ($this->calculateEstimatedCompTonerCostAnnually() / $this->calculateEstimatedOemTonerCostAnnually());
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
            $percentage = $this->getDevices()->reportingTonerLevelsDeviceInstances->getCount() / $totalDeviceCount * 100;
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
     * Calculates the average pages per device monthly
     *
     * @return float
     */
    public function calculateAveragePagesPerDeviceMonthly ()
    {
        return $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getCount();
    }

    /**
     * Calculates the percent of total volume of purchased devices that are color
     *
     * @return float
     */
    public function calculatePercentOfTotalVolumeColorMonthly ()
    {
        return ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) * 100;
    }

    /**
     * Calculates the total Average Cost For OEM Monochrome Printers Monthly
     *
     * @return float
     */
    public function calculateAverageTotalCostOemMonochromeMonthly ()
    {
        return $this->calculateAverageOemOnlyCostPerPage()->monochromeCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly();
    }

    /**
     * Calculates the total Average Cost For Compatible Monochrome Printers Monthly
     *
     * @return float
     */
    public function calculateAverageTotalCostCompatibleMonochromeMonthly ()
    {
        return $this->calculateAverageCompatibleOnlyCostPerPage()->monochromeCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly();
    }

    /**
     * Calculates the total Average Cost For Compatible Color Printers Monthly
     *
     * @return float
     */
    public function calculateAverageTotalCostOemColorMonthly ()
    {
        return $this->calculateAverageOemOnlyCostPerPage()->colorCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly();
    }

    /**
     * Calculates the total Average Cost For OEM Color Printers Monthly
     *
     * @return float
     */
    public function calculateAverageTotalCostCompatibleColorMonthly ()
    {
        return $this->calculateAverageCompatibleOnlyCostPerPage()->colorCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly();
    }

    /**
     * Gets an array of graphs
     *
     * @return array
     */
    public function getGraphs ()
    {

        if ($this->Graphs == null)
        {
            // Fetch the old graphs
            $this->_getOldGraphs();

            $healthcheckGraphs       = array();
            $numberValueMarker       = "N *sz0";
            $companyName             = $this->healthcheck->getClient()->companyName;
            $employeeCount           = $this->healthcheck->getClient()->employeeCount;
            $numberOfIncludedDevices = $this->getDevices()->allIncludedDeviceInstances->getCount();

            /**
             * -- PagesPrintedReportingTonerLevelsPieGraph
             */
            $deviceAges = array(
                "Pages Printed on Devices Reporting Toner Levels"     => 0,
                "Pages Printed on Devices Not Reporting Toner Levels" => 0
            );

            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                if ($device->reportsTonerLevels)
                {
                    $deviceAges ["Pages Printed on Devices Reporting Toner Levels"] += $device->getPageCounts()->getCombinedPageCount()->getMonthly();
                }
                else
                {
                    $deviceAges ["Pages Printed on Devices Not Reporting Toner Levels"] += $device->getPageCounts()->getCombinedPageCount()->getMonthly();
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
                    $percentage     = round(($count / $this->getPageCounts()->getCombinedPageCount()->getMonthly()) * 100, 2);
                    $labels []      = "$percentage%";
                }
            }
            $deviceAgeGraph = new gchart\gPie3DChart(350, 200);
            $deviceAgeGraph->addDataSet($dataSet);
            $deviceAgeGraph->setLegend($legendItems);
            $deviceAgeGraph->setLabels($labels);
            $deviceAgeGraph->addColors(array(
                "E21736",
                "0094cf"
            ));
            $deviceAgeGraph->setLegendPosition("bv");
            $deviceAgeGraph->setTitle("Pages printed on Devices Reporting Toner Levels");

            // PagesPrintedReportingTonerLevelsPieGraph
            $healthcheckGraphs['PagesPrintedReportingTonerLevelsPieGraph'] = $deviceAgeGraph->getUrl();

            /**
             * -- PagesPrintedJITPieGraph
             */
            $devicePages = array(
                "Pages Printed on " . My_Brand::$jit . " compatible devices"     => 0,
                "Pages Printed on non-" . My_Brand::$jit . " compatible devices" => 0
            );

            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                if ($device->compatibleWithJitProgram)
                {
                    $devicePages ["Pages Printed on " . My_Brand::$jit . " compatible devices"] += $device->getPageCounts()->getCombinedPageCount()->getMonthly();
                }
                else
                {
                    $devicePages ["Pages Printed on non-" . My_Brand::$jit . " compatible devices"] += $device->getPageCounts()->getCombinedPageCount()->getMonthly();
                }
            }
            $dataSet     = array();
            $legendItems = array();
            $labels      = array();

            foreach ($devicePages as $legendItem => $count)
            {
                if ($count > 0)
                {
                    $dataSet []     = $count;
                    $legendItems [] = $legendItem;
                    $percentage     = round(($count / $this->getPageCounts()->getCombinedPageCount()->getMonthly()) * 100, 2);
                    $labels []      = "$percentage%";
                }
            }
            $deviceAgeGraph = new gchart\gPie3DChart(350, 200);
            $deviceAgeGraph->addDataSet($dataSet);
            $deviceAgeGraph->setLegend($legendItems);
            $deviceAgeGraph->setLabels($labels);
            $deviceAgeGraph->addColors(array(
                "E21736",
                "0094cf"
            ));
            $deviceAgeGraph->setLegendPosition("bv");
            $deviceAgeGraph->setTitle("Pages printed on " . My_Brand::$jit);

            // PagesPrintedJITPieGraph
            $healthcheckGraphs['PagesPrintedJITPieGraph'] = $deviceAgeGraph->getUrl();

            /**
             * -- HardwareUtilizationCapacityBar
             */
            $highest  = ($this->getMaximumMonthlyPrintVolume() > $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) ? $this->getMaximumMonthlyPrintVolume() : $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            $barGraph = new gchart\gGroupedBarChart(600, 160);
            $barGraph->setHorizontal(true);
            $barGraph->setVisibleAxes(array(
                'x'
            ));
            $barGraph->addDataSet(array(
                $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()
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
            $highest  = ($this->getDevices()->allIncludedDeviceInstances->getCount() - $this->getDevices()->colorCapableDeviceInstances->getCount() > $this->getDevices()->colorCapableDeviceInstances->getCount()) ? ($this->getDevices()->allIncludedDeviceInstances->getCount() - $this->getDevices()->colorCapableDeviceInstances->getCount()) : $this->getDevices()->colorCapableDeviceInstances->getCount();
            $barGraph = new gchart\gBarChart(280, 210);
            $barGraph->setTitle("Color-Capable|Printing Devices");
            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(
                $this->getDevices()->colorCapableDeviceInstances->getCount()
            ));
            $barGraph->addColors(array(
                "E21736"
            ));
            $barGraph->addDataSet(array(
                $this->getDevices()->allIncludedDeviceInstances->getCount() - $this->getDevices()->colorCapableDeviceInstances->getCount()
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
            $blackAndWhitePageCount = $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() - $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly();

            $highest  = ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() > $blackAndWhitePageCount) ? $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() : $blackAndWhitePageCount;
            $barGraph = new gchart\gBarChart(280, 210);
            $barGraph->setTitle("Color vs|Black/White Pages");
            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(
                $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly()
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
            $colorCapableDeviceCount    = $this->getDevices()->colorCapableDeviceInstances->getCount();
            $colorNonCapableDeviceCount = $numberOfIncludedDevices - $colorCapableDeviceCount;
            $colorCapableGraph          = new gchart\gPie3DChart(210, 150);
            $colorCapableGraph->setTitle("Color-Capable Printing Devices");
            $colorCapableGraph->addDataSet(array(
                $colorCapableDeviceCount,
                $colorNonCapableDeviceCount
            ));
            $colorCapableGraph->setLegend(array(
                "Color capable",
                "Black and white only"
            ));
            $colorCapableGraph->setLabels(array(
                $colorCapableDeviceCount,
                $colorNonCapableDeviceCount
            ));
            $colorCapableGraph->addColors(array(
                "E21736",
                "0194D2"
            ));
            $colorCapableGraph->setLegendPosition("bv");
            // colorCapablePieChart
            $healthcheckGraphs['colorCapablePieChart'] = $colorCapableGraph->getUrl();


            /**
             * -- a3CapablePieChart
             */
            $a3CapableDeviceCount    = number_format(($this->getDevices()->a3DeviceInstances->getCount() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100, 0);
            $a3NonCapableDeviceCount = 100 - $a3CapableDeviceCount;
            $a3CapableGraph          = new gchart\gPie3DChart(290, 210);
            $a3CapableGraph->setTitle("Percent of A3-Capable devices");
            $a3CapableGraph->addDataSet(array(
                $a3CapableDeviceCount,
                $a3NonCapableDeviceCount
            ));
            $a3CapableGraph->setLegend(array(
                "A3 Capable",
                "Not A3 Capable"
            ));
            $a3CapableGraph->setLabels(array(
                $a3CapableDeviceCount . "%",
                $a3NonCapableDeviceCount . "%"
            ));
            $a3CapableGraph->addColors(array(
                "E21736",
                "0194D2"
            ));
            $a3CapableGraph->setLegendPosition("bv");
            // a3CapablePieChart
            $healthcheckGraphs['a3CapablePieChart'] = $a3CapableGraph->getUrl();

            /**
             * -- a3PagePercent
             */
            $a3PageCountPercent      = number_format(($this->getDevices()->a3DeviceInstances->getPageCounts()->getPrintA3CombinedPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) * 100, 0);
            $a3NonA3PageCountPercent = 100 - $a3PageCountPercent;
            $a3CapableGraph          = new gchart\gPie3DChart(290, 210);
            $a3CapableGraph->setTitle("Percent of A3 Pages");
            $a3CapableGraph->addDataSet(array(
                $a3PageCountPercent,
                $a3NonA3PageCountPercent
            ));
            $a3CapableGraph->setLegend(array(
                "A3 Pages",
                "Other Pages"
            ));
            $a3CapableGraph->setLabels(array(
                $a3PageCountPercent . "%",
                $a3NonA3PageCountPercent . "%"
            ));
            $a3CapableGraph->addColors(array(
                "E21736",
                "0194D2"
            ));
            $a3CapableGraph->setLegendPosition("bv");
            // a3PagePercent
            $healthcheckGraphs['a3PagePercent'] = $a3CapableGraph->getUrl();

            /**
             * -- reportingTonerLevelsBarGraph
             */
            $numberOfDevicesReportingTonerLevels = $this->getDevices()->reportingTonerLevelsDeviceInstances->getCount();
            $numberOfIncompatibleDevices         = $this->getDevices()->allIncludedDeviceInstances->getCount() - $numberOfDevicesReportingTonerLevels;
            $highest                             = ($numberOfDevicesReportingTonerLevels > $numberOfIncompatibleDevices ? $numberOfDevicesReportingTonerLevels : ($numberOfIncompatibleDevices));
            $barGraph                            = new gchart\gBarChart(220, 220);
            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(
                $numberOfDevicesReportingTonerLevels
            ));
            $barGraph->addColors(array(
                "0194D2"
            ));
            $barGraph->addDataSet(array(
                ($numberOfIncompatibleDevices)
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(70, 10);
            $barGraph->setLegendPosition("b");
            $barGraph->addColors(array(
                "E21736"
            ));
            $barGraph->setLegend(array(
                "Printers Reporting Toner Levels",
                "Printers Not Reporting Toner Levels"
            ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->setTitle($this->healthcheck->getClient()->companyName);

            // reportingTonerLevelsBarGraph
            $healthcheckGraphs['reportingTonerLevelsBarGraph'] = $barGraph->getUrl();

            /**
             * -- CompatibleJITBarGraph
             */
            $numberOfDevicesReportingTonerLevels = $this->getDevices()->compatibleDeviceInstances->getCount();
            $numberOfIncompatibleDevices         = $this->getDevices()->allIncludedDeviceInstances->getCount() - $numberOfDevicesReportingTonerLevels;
            $highest                             = ($numberOfDevicesReportingTonerLevels > $numberOfIncompatibleDevices ? $numberOfDevicesReportingTonerLevels : ($numberOfIncompatibleDevices));
            $barGraph                            = new gchart\gBarChart(220, 220);
            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(
                $numberOfDevicesReportingTonerLevels
            ));
            $barGraph->addColors(array(
                "0194D2"
            ));
            $barGraph->addDataSet(array(
                ($numberOfIncompatibleDevices)
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(70, 10);
            $barGraph->setLegendPosition("b");
            $barGraph->addColors(array(
                "E21736"
            ));
            $barGraph->setLegend(array(
                "Printers Compatible with " . My_Brand::$jit,
                "Printers Not compatible with " . My_Brand::$jit
            ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->setTitle($this->healthcheck->getClient()->companyName);

            // CompatibleJITBarGraph
            $healthcheckGraphs['CompatibleJITBarGraph'] = $barGraph->getUrl();

            $oemCost  = ($this->calculateAverageOemOnlyCostPerPage()->colorCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() + ($this->calculateAverageOemOnlyCostPerPage()->monochromeCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly())) * 12;
            $compCost = ($this->calculateAverageCompatibleOnlyCostPerPage()->colorCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() + ($this->calculateAverageCompatibleOnlyCostPerPage()->monochromeCostPerPage * $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getBlackPageCount()->getMonthly())) * 12;
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
            $percentage = ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->getMaximumMonthlyPrintVolume());
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
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
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
             * -- AgePieGraph
             */
            $deviceAges = array(
                "Less than 3 years old" => 0,
                "3-5 years old"         => 0,
                "6-8 years old"         => 0,
                "More than 8 years old" => 0
            );
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
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

            $AgeOfPrintingPieChart = new gchart\gPie3DChart(400, 300);
            $AgeOfPrintingPieChart->addDataSet($deviceAges);
            $AgeOfPrintingPieChart->addColors(array(
                "0094cf",
                "E21736",
                "adba1d",
                "5c3f9b",
            ));
            $AgeOfPrintingPieChart->setLegendPosition("bv");
            $AgeOfPrintingPieChart->setTitle("Age of device");
            $AgeOfPrintingPieChart->setLabels(array(
                number_format((($deviceAges ["Less than 3 years old"] / $numberOfIncludedDevices) * 100), 0) . "%",
                number_format((($deviceAges ["3-5 years old"] / $numberOfIncludedDevices) * 100), 0) . "%",
                number_format((($deviceAges ["6-8 years old"] / $numberOfIncludedDevices) * 100), 0) . "%",
                number_format((($deviceAges ["More than 8 years old"] / $numberOfIncludedDevices) * 100), 0) . "%",
            ));
            $AgeOfPrintingPieChart->setLegend(array(
                "Less than 3 years old",
                "3-5 years old",
                "6-8 years old",
                "More than 8 years old"
            ));
            // AgePieGraph
            $healthcheckGraphs['AgePieGraph'] = $AgeOfPrintingPieChart->getUrl();

            /**
             * -- PrintIQAgePieGraph
             */
            $deviceAges = array(
                "Less than 3 years old" => 0,
                "3-5 years old"         => 0,
                "6-8 years old"         => 0,
                "More than 8 years old" => 0
            );
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
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

            $PrintIQAgeOfPrintingPieChart = new gchart\gPie3DChart(250, 220);
            $PrintIQAgeOfPrintingPieChart->addDataSet($deviceAges);
            $PrintIQAgeOfPrintingPieChart->addColors(array(
                "008000",
                "54CC64",
                "FFD000",
                "ff0000",
            ));
            $PrintIQAgeOfPrintingPieChart->setLegendPosition("bv");
            $PrintIQAgeOfPrintingPieChart->setTitle("Age of device");
            $PrintIQAgeOfPrintingPieChart->setLabels(array(
                number_format((($deviceAges ["Less than 3 years old"] / $numberOfIncludedDevices) * 100), 0) . "%",
                number_format((($deviceAges ["3-5 years old"] / $numberOfIncludedDevices) * 100), 0) . "%",
                number_format((($deviceAges ["6-8 years old"] / $numberOfIncludedDevices) * 100), 0) . "%",
                number_format((($deviceAges ["More than 8 years old"] / $numberOfIncludedDevices) * 100), 0) . "%",
            ));
            $PrintIQAgeOfPrintingPieChart->setLegend(array(
                "Less than 3 years old",
                "3-5 years old",
                "6-8 years old",
                "More than 8 years old"
            ));
            // PrintIQAgePieGraph
            $healthcheckGraphs['PrintIQAgePieGraph'] = $PrintIQAgeOfPrintingPieChart->getUrl();

            /**
             * -- PrintIQPagesPrintedByAgeBarGraph
             */
            $deviceAges = array(
                "Less than 3 years old" => 0,
                "3-5 years old"         => 0,
                "6-8 years old"         => 0,
                "More than 8 years old" => 0
            );
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                if ($device->getAge() < 3)
                {
                    $deviceAges ["Less than 3 years old"] += $device->getPageCounts()->getCombinedPageCount()->getMonthly();
                }
                else if ($device->getAge() <= 5)
                {
                    $deviceAges ["3-5 years old"] += $device->getPageCounts()->getCombinedPageCount()->getMonthly();
                }
                else if ($device->getAge() <= 8)
                {
                    $deviceAges ["6-8 years old"] += $device->getPageCounts()->getCombinedPageCount()->getMonthly();
                }
                else
                {
                    $deviceAges ["More than 8 years old"] += $device->getPageCounts()->getCombinedPageCount()->getMonthly();
                }
            }

            $PrintIQPagesPrintedByAgeBarGraph = new gchart\gBarChart(250, 250);
            $PrintIQPagesPrintedByAgeBarGraph->setVisibleAxes(array(
                'y'
            ));
            $PrintIQPagesPrintedByAgeBarGraph->addDataSet(array(
                $deviceAges ["Less than 3 years old"]
            ));
            $PrintIQPagesPrintedByAgeBarGraph->addColors(array(
                "008000"
            ));
            $PrintIQPagesPrintedByAgeBarGraph->addDataSet(array(
                $deviceAges ["3-5 years old"]
            ));
            $PrintIQPagesPrintedByAgeBarGraph->addColors(array(
                "54CC64"
            ));
            $PrintIQPagesPrintedByAgeBarGraph->addDataSet(array(
                $deviceAges ["6-8 years old"]
            ));
            $PrintIQPagesPrintedByAgeBarGraph->addColors(array(
                "FFD000"
            ));
            $PrintIQPagesPrintedByAgeBarGraph->addDataSet(array(
                $deviceAges ["More than 8 years old"]
            ));
            $PrintIQPagesPrintedByAgeBarGraph->addColors(array(
                "ff0000"
            ));

            $highest = max($deviceAges["Less than 3 years old"], $deviceAges["3-5 years old"], $deviceAges["6-8 years old"], $deviceAges["More than 8 years old"]);
            $PrintIQPagesPrintedByAgeBarGraph->addAxisRange(0, 0, $highest * 1.1);
            $PrintIQPagesPrintedByAgeBarGraph->setDataRange(0, $highest * 1.1);
            $PrintIQPagesPrintedByAgeBarGraph->setBarScale(45, 5);
            $PrintIQPagesPrintedByAgeBarGraph->setLegendPosition("bv");
            $PrintIQPagesPrintedByAgeBarGraph->setTitle("Pages Printed By Age of Device");
            $PrintIQPagesPrintedByAgeBarGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $PrintIQPagesPrintedByAgeBarGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $PrintIQPagesPrintedByAgeBarGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $PrintIQPagesPrintedByAgeBarGraph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            $PrintIQPagesPrintedByAgeBarGraph->setLegend(array(
                "Less than 3 years old",
                "3-5 years old",
                "6-8 years old",
                "More than 8 years old"
            ));
            // PrintIQPagesPrintedByAgeBarGraph
            $healthcheckGraphs['PrintIQPagesPrintedByAgeBarGraph'] = $PrintIQPagesPrintedByAgeBarGraph->getUrl();

            /**
             * -- DuplexCapableDevicesGraph
             */
            $duplexCapableDeviceCount    = $this->getDevices()->duplexCapableDeviceInstances->getCount();
            $duplexNonCapableDeviceCount = $numberOfIncludedDevices - $duplexCapableDeviceCount;
            $duplexCapableGraph          = new gchart\gPie3DChart(210, 150);
            $duplexCapableGraph->setTitle("Duplex-Capable Printing Devices");
            $duplexCapableGraph->addDataSet(array(
                $duplexCapableDeviceCount,
                $duplexNonCapableDeviceCount
            ));
            $duplexCapableGraph->setLegend(array(
                "Duplex capable",
                "Not duplex capable"
            ));
            $duplexCapableGraph->setLabels(array(
                $duplexCapableDeviceCount,
                $duplexNonCapableDeviceCount
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
            $averagePageCount = round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getCount(), 0);
            $highest          = ($averagePageCount > Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_DEVICE) ? $averagePageCount : Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_DEVICE;
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
                Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_DEVICE
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
            $pagesPerEmployee = round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $employeeCount);
            $highest          = (Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE > $pagesPerEmployee) ? Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE : $pagesPerEmployee;
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
                Assessment_ViewModel_Assessment::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE
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
            $devicesPerEmployee = round($employeeCount / $this->getDevices()->allIncludedDeviceInstances->getCount(), 2);
            $highest            = ($devicesPerEmployee > Assessment_ViewModel_Assessment::AVERAGE_EMPLOYEES_PER_DEVICE) ? $devicesPerEmployee : Assessment_ViewModel_Assessment::AVERAGE_EMPLOYEES_PER_DEVICE;
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
                Assessment_ViewModel_Assessment::AVERAGE_EMPLOYEES_PER_DEVICE
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
            $copyCapableDeviceCount    = $this->getDevices()->copyCapableDeviceInstances->getCount();
            $copyNonCapableDeviceCount = $numberOfIncludedDevices - $copyCapableDeviceCount;
            $copyCapableGraph          = new gchart\gPie3DChart(210, 150);
            $copyCapableGraph->setTitle("Copy-Capable Printing Devices");
            $copyCapableGraph->addDataSet(array(
                $copyCapableDeviceCount,
                $copyNonCapableDeviceCount
            ));
            $copyCapableGraph->setLegend(array(
                "Copy capable",
                "Not copy capable"
            ));
            $copyCapableGraph->setLabels(array(
                $copyCapableDeviceCount,
                $copyNonCapableDeviceCount
            ));
            $copyCapableGraph->addColors(array(
                "E21736",
                "0194D2"
            ));
            $copyCapableGraph->setLegendPosition("bv");
            // Graphs CopyCapableDevicesGraph
            $healthcheckGraphs['CopyCapableDevicesGraph'] = $copyCapableGraph->getUrl();

            /**
             * -- ManagedVsNotJitVsJitDevices
             */
            $managedByThirdPartyDeviceCount = $this->getDevices()->managedByThirdPartyDeviceInstances->getCount();
            $isManagedDeviceCount           = $this->getDevices()->isManagedDeviceInstances->getCount();
            $notCompatibleDeviceCount       = $this->getDevices()->notCompatibleDeviceInstances->getCount();
            $jitCompatibleDeviceCount       = $this->getDevices()->compatibleDeviceInstances->getCount();

            $highest  = max($isManagedDeviceCount, $notCompatibleDeviceCount, $jitCompatibleDeviceCount, $managedByThirdPartyDeviceCount);
            $barGraph = new gchart\gBarChart(280, 230);
            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(
                $isManagedDeviceCount
            ));
            $barGraph->addColors(array(
                "0194D2"
            ));

            $barGraph->addDataSet(array(
                $notCompatibleDeviceCount
            ));
            $barGraph->addColors(array(
                "E21736"
            ));
            $barGraph->addDataSet(array(
                $jitCompatibleDeviceCount
            ));

            $barGraph->addColors(array(
                "FFD000"
            ));

            $barGraph->addDataSet(array(
                $managedByThirdPartyDeviceCount
            ));

            $barGraph->addColors(array(
                "ababab"
            ));
            $barGraph->setBarScale(50, 10);
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);

            $barGraph->setLegendPosition("b|l");

            $barGraph->setLegend(array(
                "Managed/On " . My_Brand::$jit,
                "Not " . My_Brand::$jit . " Compatible",
                My_Brand::$jit . " Compatible",
                "Future Review",
            ));

            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            $barGraph->setTitle("Total printers on network");
            // Graphs[ManagedVsNotJitVsJitDevices]
            $healthcheckGraphs['ManagedVsNotJitVsJitDevices'] = $barGraph->getUrl();

            /**
             * -- UnmanagedVsManagedDevices
             */
            $insufficientData               = $this->getDevices()->allDevicesWithShortMonitorInterval->getCount() + $this->getDevices()->unmappedDeviceInstances->getCount() + $this->getDevices()->excludedDeviceInstances->getCount();
            $managedByThirdPartyDeviceCount = $this->getDevices()->managedByThirdPartyDeviceInstances->getCount();
            $isManagedDeviceCount           = $this->getDevices()->isManagedDeviceInstances->getCount();
            $unmanagedDeviceCount           = $this->getDevices()->unmanagedDeviceInstances->getCount();


            $highest  = max($isManagedDeviceCount, $unmanagedDeviceCount, $insufficientData, $managedByThirdPartyDeviceCount);
            $barGraph = new gchart\gBarChart(280, 230);
            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(
                $isManagedDeviceCount
            ));
            $barGraph->addColors(array(
                "0194D2"
            ));

            $barGraph->addDataSet(array(
                $unmanagedDeviceCount
            ));
            $barGraph->addColors(array(
                "E21736"
            ));
            $barGraph->addDataSet(array(
                $managedByThirdPartyDeviceCount
            ));

            $barGraph->addColors(array(
                "ababab"
            ));

            if ($insufficientData > 0)
            {
                $barGraph->addDataSet(array(
                    $insufficientData
                ));

                $barGraph->addColors(array(
                    "666666"
                ));
                $barGraph->setBarScale(50, 10);
            }
            else
            {
                $barGraph->setBarScale(50, 10);
            }


            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);

            $barGraph->setLegendPosition("b|l");

            $barGraph->setLegend(array(
                "Managed/On " . My_Brand::$jit,
                "Manageable",
                "Future Review",
                "Insufficient Data",
            ));

            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "4", "-1", "11");
            $barGraph->setTitle("Total printers on network");
            // Graphs[UnmanagedVsManagedDevices]
            $healthcheckGraphs['UnmanagedVsManagedDevices'] = $barGraph->getUrl();

            /**
             * -- PagesWithOrWithoutJIT
             */
            $barGraph = new gchart\gBarChart(280, 230);

            $pagesPrintedOnManaged           = "Managed/On " . My_Brand::$jit;
            $pagesPrintedOnUnmanaged         = "Manageable";
            $pagesPrintedManagedByThirdParty = "Future Review";

            $pagesPrinted                                   = array(
                $pagesPrintedOnManaged           => 0,
                $pagesPrintedOnUnmanaged         => 0,
                $pagesPrintedManagedByThirdParty => 0,
            );
            $pagesPrinted[$pagesPrintedOnManaged]           = $this->getDevices()->isManagedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            $pagesPrinted[$pagesPrintedOnUnmanaged]         = $this->getDevices()->unmanagedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            $pagesPrinted[$pagesPrintedManagedByThirdParty] = $this->getDevices()->managedByThirdPartyDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();

            $highest = max($pagesPrinted[$pagesPrintedOnManaged], $pagesPrinted [$pagesPrintedOnUnmanaged], $pagesPrinted [$pagesPrintedManagedByThirdParty]);
            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(
                round($pagesPrinted[$pagesPrintedOnManaged])
            ));
            $barGraph->addColors(array(
                "0194D2"
            ));
            $barGraph->addDataSet(array(
                round($pagesPrinted[$pagesPrintedOnUnmanaged])
            ));

            $barGraph->addColors(array(
                "E21736"
            ));

            $barGraph->addDataSet(array(
                round($pagesPrinted[$pagesPrintedManagedByThirdParty])
            ));

            $barGraph->addColors(array(
                "ababab"
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.20);
            $barGraph->setDataRange(0, $highest * 1.20);
            $barGraph->setBarScale(50, 5);
            $barGraph->setLegendPosition("b|l");

            $barGraph->setLegend(array(
                $pagesPrintedOnManaged,
                $pagesPrintedOnUnmanaged,
                $pagesPrintedManagedByThirdParty,
            ));
            $barGraph->setTitle("Total pages printed");
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            // Graphs[PagesWithOrWithoutJIT]
            $healthcheckGraphs['PagesWithOrWithoutJIT'] = $barGraph->getUrl();

            /**
             * -- PagesPrintedManagedVsJitVsCompVsLeased
             */
            $barGraph = new gchart\gBarChart(280, 230);

            $pagesPrintedOnManaged           = "Managed/On " . My_Brand::$jit;
            $pagesPrintedOnNotCompatible     = "Not " . My_Brand::$jit . " Compatible";
            $pagesPrintedOnCompatible        = My_Brand::$jit . " Compatible";
            $pagesPrintedManagedByThirdParty = "Future Review";

            $pagesPrinted = array(
                $pagesPrintedOnManaged           => 0,
                $pagesPrintedOnNotCompatible     => 0,
                $pagesPrintedOnCompatible        => 0,
                $pagesPrintedManagedByThirdParty => 0,
            );

            $pagesPrinted[$pagesPrintedOnManaged]           = $this->getDevices()->isManagedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            $pagesPrinted[$pagesPrintedOnNotCompatible]     = $this->getDevices()->notCompatibleDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            $pagesPrinted[$pagesPrintedOnCompatible]        = $this->getDevices()->compatibleDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            $pagesPrinted[$pagesPrintedManagedByThirdParty] = $this->getDevices()->managedByThirdPartyDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();

            $highest = max($pagesPrinted[$pagesPrintedOnManaged], $pagesPrinted [$pagesPrintedOnNotCompatible], $pagesPrinted[$pagesPrintedOnCompatible], $pagesPrinted [$pagesPrintedManagedByThirdParty]);
            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(
                round($pagesPrinted[$pagesPrintedOnManaged])
            ));
            $barGraph->addColors(array(
                "0194D2"
            ));
            $barGraph->addDataSet(array(
                round($pagesPrinted[$pagesPrintedOnNotCompatible])
            ));

            $barGraph->addColors(array(
                "E21736"
            ));
            $barGraph->addDataSet(array(
                round($pagesPrinted[$pagesPrintedOnCompatible])
            ));

            $barGraph->addColors(array(
                "FFD000"
            ));

            $barGraph->addDataSet(array(
                round($pagesPrinted[$pagesPrintedManagedByThirdParty])
            ));

            $barGraph->addColors(array(
                "ababab"
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.20);
            $barGraph->setDataRange(0, $highest * 1.20);
            $barGraph->setBarScale(50, 5);
            $barGraph->setLegendPosition("b|l");

            $barGraph->setLegend(array(
                $pagesPrintedOnManaged,
                $pagesPrintedOnNotCompatible,
                $pagesPrintedOnCompatible,
                $pagesPrintedManagedByThirdParty,
            ));
            $barGraph->setTitle("Total pages printed");
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            // Graphs[PagesPrintedManagedVsJitVsCompVsLeased]
            $healthcheckGraphs['PagesPrintedManagedVsJitVsCompVsLeased'] = $barGraph->getUrl();

            /**
             * -- UniqueDevicesGraph
             */
            $uniqueModelArray = $this->getUniqueDeviceCountArray();

            $uniqueDevicesGraph = new gchart\gPie3DChart(400, 250);
            $uniqueDevicesGraph->addDataSet($uniqueModelArray);
            $uniqueDevicesGraph->addColors(array_slice(self::$COLOR_ARRAY, 0, count($uniqueModelArray)));
            $uniqueDevicesGraph->setTitle("Percent per device on your network|Total Devices - " . $numberOfIncludedDevices);
            // Graphs[UniqueDevicesGraph]
            $healthcheckGraphs['UniqueDevicesGraph'] = $uniqueDevicesGraph->getUrl();

            /**
             * PercentPerDeviceBrand
             */
            $smallerSubsetOfColors = array_slice(self::$COLOR_ARRAY, 0, 30);
            $percentPerDeviceBrand = new gchart\gPie3DChart(400, 250);
            $percentPerDeviceBrand->setTitle("Percent per device brand on your network|Total Devices - " . $numberOfIncludedDevices);
            $deviceVendorCount = $this->getDeviceVendorCount();
            $percentPerDeviceBrand->addDataSet($deviceVendorCount);

            $percentageArray = $deviceVendorCount;
            foreach ($percentageArray as $key => $value)
            {
                $percentageArray[$key] = $key . " (" . number_format((($value / $numberOfIncludedDevices) * 100), 1) . "%)";
            }

            $percentPerDeviceBrand->setLegend(array_values($percentageArray));


            $percentPerDeviceBrand->addColors($smallerSubsetOfColors);
            $percentPerDeviceBrand->setLegendPosition("b");

            // PercentPerDeviceBrand
            $healthcheckGraphs['PercentPerDeviceBrand'] = $percentPerDeviceBrand->getUrl();

            /**
             * -- faxCapableBar
             */
            $faxPercentage = 0;
            if ($numberOfIncludedDevices)
            {
                $faxPercentage = round((($this->getDevices()->faxCapableDeviceInstances->getCount() / $numberOfIncludedDevices) * 100), 2);
            }

            $notFaxPercentage = 100 - $faxPercentage;
            $faxCapable       = new gchart\gPie3DChart(305, 210);
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
                number_format($this->getDevices()->faxCapableDeviceInstances->getCount()),
                number_format($numberOfIncludedDevices - $this->getDevices()->faxCapableDeviceInstances->getCount())
            ));
            $faxCapable->addColors(array(
                "E21736",
                "0194D2"
            ));
            $faxCapable->setLegendPosition("bv");
            // Graphs[faxCapableBar]
            $healthcheckGraphs['faxCapableBar'] = $faxCapable->getUrl();

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
        return $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly() / self::PAGES_PER_TREE;
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
        return $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly() * self::GALLONS_WATER_PER_PAGE;
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

    /**
     * Gets all the unique device counts in an array sorted by descending order
     *
     * @return array
     */
    public function getUniqueDeviceCountArray ()
    {
        if (!isset($this->_uniqueDeviceCountArray))
        {
            $uniqueModelArray     = array();
            $removeArray          = array();
            $allIncluded          = $this->getDevices()->allIncludedDeviceInstances->getDeviceInstances();
            $numberOfOtherDevices = 0;

            // Get all the devices counted in an array with the key as the modelName and the value as the number of devices
            foreach ($allIncluded as $device)
            {
                if (array_key_exists($device->getMasterDevice()->getManufacturer()->fullname . " " . $device->getMasterDevice()->modelName, $uniqueModelArray))
                {
                    $uniqueModelArray [$device->getMasterDevice()->getManufacturer()->fullname . " " . $device->getMasterDevice()->modelName] += 1;
                }
                else
                {
                    $uniqueModelArray [$device->getMasterDevice()->getManufacturer()->fullname . " " . $device->getMasterDevice()->modelName] = 1;
                }
            }

            // Sort it so the lowest numbers are at the front of the array
            natsort($uniqueModelArray);

            // Go through the array counting devices as long as we are below 10% of the total devices
            foreach ($uniqueModelArray as $modelName => $deviceCount)
            {
                // This device will not be shown
                if (($deviceCount / count($allIncluded) <= .007))
                {
                    $numberOfOtherDevices += $deviceCount;
                    array_push($removeArray, $modelName);
                }
                else
                {
                    break;
                }
            }

            // Remove all the devices that are in the others section, unless we only have one
            foreach ($removeArray as $keyToRemove)
            {
                if (count($removeArray) == 1)
                {
                    break;
                }

                unset($uniqueModelArray[$keyToRemove]);
            }

            // Sort it naturally, and then reverse it because natural sorts do not have a reverse
            natsort($uniqueModelArray);
            $uniqueModelArray = array_reverse($uniqueModelArray, true);

            // If we only have one don't use the others section, show them all
            if (count($removeArray) > 1)
            {
                $uniqueModelArray['Others'] = $numberOfOtherDevices;
            }

            $this->_uniqueDeviceCountArray = $uniqueModelArray;
        }

        return $this->_uniqueDeviceCountArray;
    }

    /**
     * Gets all the unique device counts in an array
     */
    public function getDeviceVendorCount ()
    {
        if (!isset($this->_deviceVendorCount))
        {
            $deviceVendorArray = array();
            foreach ($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                if (array_key_exists($device->getMasterDevice()->getManufacturer()->fullname, $deviceVendorArray))
                {
                    $deviceVendorArray [$device->getMasterDevice()->getManufacturer()->fullname] += 1;
                }
                else
                {
                    $deviceVendorArray [$device->getMasterDevice()->getManufacturer()->fullname] = 1;
                }
            }

            // Sort it naturally, and then reverse it because natural sorts do not have a reverse
            natsort($deviceVendorArray);
            $deviceVendorArray = array_reverse($deviceVendorArray, true);

            $this->_deviceVendorCount = $deviceVendorArray;
        }

        return $this->_deviceVendorCount;
    }

    /**
     * A3 Print volume as a percentage of the total print volume
     *
     * @return float
     */
    public function calculatePercentageA3Pages ()
    {
        return $this->getDevices()->a3DeviceInstances->getPageCounts()->getPrintA3CombinedPageCount()->getMonthly() / $this->getPageCounts()->getCombinedPageCount()->getMonthly();
    }

    /**
     * @return int|Proposalgen_Model_Client_Toner_Order[]
     */
    public function getClientTonerOrders ()
    {
        if (!isset($this->_clientTonerOrders))
        {
            $this->_clientTonerOrders = Proposalgen_Model_Mapper_Client_Toner_Order::getInstance()->fetchAllForClient($this->healthcheck->clientId, $this->healthcheck->dealerId);
            usort($this->_clientTonerOrders, array(
                $this,
                "descendingSortClientTonerOrdersByNetSavings"
            ));
        }

        return $this->_clientTonerOrders;
    }

    /**
     * Callback function for uSort when we want to sort Client Toner Orders by their net savings
     *
     * @param $clientTonerA \Proposalgen_Model_Client_Toner_Order
     * @param $clientTonerB \Proposalgen_Model_Client_Toner_Order
     *
     * @return int
     */
    public function descendingSortClientTonerOrdersByNetSavings ($clientTonerA, $clientTonerB)
    {
        $netSavingsA = $clientTonerA->getReplacementTonerSavings($this->getHealthcheckMargin());
        $netSavingsB = $clientTonerB->getReplacementTonerSavings($this->getHealthcheckMargin());

        if ($netSavingsA == $netSavingsB)
        {
            return 0;
        }

        return ($netSavingsA > $netSavingsB) ? -1 : 1;
    }

    /**
     * @return float
     */
    public function getOptimizedClientTonerOrderSavings ()
    {
        $totalSavings = 0.0;
        foreach ($this->getClientTonerOrders() as $clientTonerOrder)
        {
            if ($clientTonerOrder->getReplacementToner())
            {
                $totalSavings += ($clientTonerOrder->cost - $clientTonerOrder->getReplacementTonerCost($this->getHealthcheckMargin())) * $clientTonerOrder->quantity;
            }
        }

        return $totalSavings;
    }

    /**
     * @return float
     */
    public function getOptimizedClientTonerOrderCost ()
    {
        $totalCost = 0.0;
        foreach ($this->getClientTonerOrders() as $clientTonerOrder)
        {
            $totalCost += $clientTonerOrder->cost - $clientTonerOrder->quantity;
        }

        return $totalCost;
    }

    protected $_currentTonerOrderCost;
    protected $_currentTonerOrderSavings;
    protected $_optimizedTonerOrderSavings;

    /**
     * Gets the total cost of the current toner order
     *
     * @return float
     */
    public function calculateCurrentTonerOrderCost ()
    {
        if (!isset($this->_currentTonerOrderCost))
        {
            $this->_currentTonerOrderCost = 0.0;
            foreach ($this->getClientTonerOrders() as $clientTonerOrder)
            {
                $this->_currentTonerOrderCost += $clientTonerOrder->cost * $clientTonerOrder->quantity;
            }
        }

        return $this->_currentTonerOrderCost;
    }


    /**
     * Gets the difference between the current toner order and OEM supplies. This will be 0 if they are not compatible toners.
     *
     * @return float
     */
    public function calculateCurrentTonerOrderSavings ()
    {
        if (!isset($this->_currentTonerOrderSavings))
        {
            $totalOemCost = 0.0;
            foreach ($this->getClientTonerOrders() as $clientTonerOrder)
            {
                if ($clientTonerOrder->getToner() instanceof Proposalgen_Model_Toner)
                {
                    if ($clientTonerOrder->getToner()->isCompatible())
                    {
                        $toners = $clientTonerOrder->getToner()->getOemToners($this->healthcheck->clientId, $this->healthcheck->dealerId);
                        if (count($toners) > 0)
                        {
                            $toner = $toners[0];
                            $totalOemCost += $toner->cost - $clientTonerOrder->quantity;
                        }
                        else
                        {
                            $totalOemCost += $clientTonerOrder->cost * $clientTonerOrder->quantity;
                        }
                    }
                    else
                    {
                        $totalOemCost += $clientTonerOrder->cost * $clientTonerOrder->quantity;
                    }

                }
            }

            $this->_currentTonerOrderSavings = $totalOemCost - $this->calculateCurrentTonerOrderCost();
        }

        return $this->_currentTonerOrderSavings;
    }

    /**
     * Calculates the current savings percentage
     *
     * @return float
     */
    public function calculateCurrentTonerOrderSavingPercentage ()
    {
        return $this->calculateCurrentTonerOrderSavings() / $this->calculateCurrentTonerOrderCost() * 100;
    }

    /**
     * Gets the difference between the current toner order and OEM supplies. This will be 0 if they are not compatible toners.
     *
     * @return float
     */
    public function calculateOptimizedTonerOrderSavings ()
    {
        if (!isset($this->_optimizedTonerOrderSavings))
        {
            $totalOptimizedCost = 0.0;
            foreach ($this->getClientTonerOrders() as $clientTonerOrder)
            {
                if ($clientTonerOrder->getReplacementToner() instanceof Proposalgen_Model_Toner)
                {
                    $totalOptimizedCost += $clientTonerOrder->getReplacementTonerCost($this->getHealthcheckMargin()) * $clientTonerOrder->quantity;
                }
                else
                {
                    $totalOptimizedCost += $clientTonerOrder->cost * $clientTonerOrder->quantity;
                }
            }

            $this->_optimizedTonerOrderSavings = $this->calculateCurrentTonerOrderCost() - $totalOptimizedCost;
        }

        return $this->_optimizedTonerOrderSavings;
    }

    /**
     * Calculates the current savings percentage
     *
     * @return float
     */
    public function calculateOptimizedTonerOrderSavingPercentage ()
    {
        return $this->calculateOptimizedTonerOrderSavings() / $this->calculateCurrentTonerOrderCost() * 100;
    }

    /**
     * Gets the list of fax/scan capable devices sorted by scan volume
     *
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function getFaxAndScanTableDevices ()
    {
        $devices = array();

        /**
         * We only want
         */
        foreach ($this->getDevices()->faxAndScanDeviceInstances->getDeviceInstances() as $deviceInstance)
        {
            if ($deviceInstance->getPageCounts()->getScanPageCount()->getMonthly() > 0 || $deviceInstance->getPageCounts()->getFaxPageCount()->getMonthly() > 0)
            {
                $devices[] = $deviceInstance;
            }
        }

        usort($devices, array(
            $this,
            "sortDescendingFaxAndScanTableData"
        ));

        return $devices;
    }

    /**
     * Callback function for uSort when we want to sort a device based on its scan and fax page volume
     *
     * @param $deviceA \Proposalgen_Model_DeviceInstance
     * @param $deviceB \Proposalgen_Model_DeviceInstance
     *
     * @return int
     */
    public function sortDescendingFaxAndScanTableData ($deviceA, $deviceB)
    {
        if ($deviceA->getPageCounts()->getScanPageCount()->getMonthly() == $deviceB->getPageCounts()->getScanPageCount()->getMonthly())
        {
            if ($deviceA->getPageCounts()->getFaxPageCount()->getMonthly() == $deviceB->getPageCounts()->getFaxPageCount()->getMonthly())
            {
                return 0;
            }

            return ($deviceA->getPageCounts()->getFaxPageCount()->getMonthly() > $deviceB->getPageCounts()->getFaxPageCount()->getMonthly()) ? -1 : 1;
        }

        return ($deviceA->getPageCounts()->getScanPageCount()->getMonthly() > $deviceB->getPageCounts()->getScanPageCount()->getMonthly()) ? -1 : 1;
    }
}
