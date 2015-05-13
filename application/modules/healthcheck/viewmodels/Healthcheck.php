<?php

use CpChart\Services\pChartFactory;
use MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ClientTonerOrderMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ClientTonerOrderModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\CostPerPageSettingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\PageCountsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel;

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
    const PAGES_PER_TREE         = 7800; //Number of pages divided by this, gives amount of trees

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

    protected $pImageGraphs = [];

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
    protected $graphs;
    // Device Replacement
    protected     $DevicesToBeReplaced;
    protected     $UniqueVendorCount;
    protected     $NumberOfOrdersPerMonth;
    protected     $EmployeeCount;
    protected     $ReplacementDevices;
    protected     $ReplacementDeviceCount;
    protected     $LeftOverBlackAndWhitePageCount;
    protected     $LeftOverColorPageCount;
    protected     $LeftOverPrintIQCost;
    protected     $LeftOverCostOfColorDevices;
    protected     $LeftOverCostOfBlackAndWhiteDevices;
    protected     $CostOfRemainingDevices;
    protected     $CurrentCostOfReplacedColorPrinters;
    protected     $CurrentCostOfReplacedBlackAndWhitePrinters;
    protected     $ProposedCostOfReplacedBlackAndWhitePrinters;
    protected     $ProposedCostOfReplacedColorPrinters;
    protected     $CurrentCostOfReplacedColorMFPPrinters;
    protected     $CurrentCostOfReplacedBlackAndWhiteMFPPrinters;
    protected     $ProposedCostOfReplacedBlackAndWhiteMFPPrinters;
    protected     $ProposedCostOfReplacedColorMFPPrinters;
    protected     $TotalProposedAnnualCost;
    protected     $TotalAnnualSavings;
    protected     $GrossMarginMonthlyProfit;
    protected     $GrossMarginOverallMargin;
    protected     $GrossMarginWeightedCPP;
    protected     $GrossMarginBlackAndWhiteMargin;
    protected     $GrossMarginColorMargin;
    protected     $UniqueTonerList;
    protected     $UniquePurchasedTonerList;
    protected     $UniqueDeviceList;
    protected     $UniquePurchasedDeviceList;
    protected     $_averageCompatibleOnlyCostPerPage;
    protected     $_averageOemOnlyCostPerPage;
    protected     $_maximumMonthlyPurchasedPrintVolume;
    protected     $_purchasedTotalMonthlyCost;
    protected     $_purchasedColorMonthlyCost;
    protected     $_purchasedMonochromeMonthlyCost;
    protected     $_optimizedDevices;
    protected     $_includedDevicesSortedAscendingByAge;
    protected     $_includedDevicesSortedDescendingByAge;
    protected     $_uniqueDeviceCountArray;
    protected     $_deviceVendorCount;
    protected     $_underutilizedA3Devices;
    protected     $_pageCounts;
    public        $highCostPurchasedDevices;
    public static $COLOR_ARRAY = [
        "FF7E00", "7CB9E8", "C9FFE5", "B284BE", "5D8AA8", "00308F", "72A0C1", "AF002A", "E32636", "C46210",
        "EFDECD", "A8BB19", "F19CBB", "3B7A57", "FFBF00", "FF9900", "FF033E", "9966CC", "A4C639", "9FA91F",
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
    ];

    /**
     * @var ClientTonerOrderModel[]
     */
    protected $_clientTonerOrders;

    /**
     * @param HealthCheckModel $report
     */
    public function __construct (HealthCheckModel $report)
    {
        parent::__construct($report);
        $this->DealerCompany = My_Brand::getDealerBranding()->shortDealerName;

        if (isset(self::$Proposal))
        {
            self::$Proposal = $this;
        }

        // Get the report settings
        DeviceInstanceModel::$KWH_Cost = $this->healthcheck->getClient()->getClientSettings()->genericSettings->defaultEnergyCost;

        if ($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly() > 0)
        {
            DeviceInstanceModel::$ITCostPerPage = (($this->getAnnualITCost() * 0.5 + $this->getAnnualCostOfOutSourcing()) / $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getYearly());
        }
        else
        {
            DeviceInstanceModel::$ITCostPerPage = 0.0;
        }
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
            $this->healthcheckMargin = $this->healthcheck->getClient()->getClientSettings()->genericSettings->tonerPricingMargin;
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
            $this->CombinedAnnualLeasePayments =
                $this->healthcheck->getClient()->getClientSettings()->genericSettings->defaultMonthlyLeasePayment *
                $this->getDevices()->leasedDeviceInstances->getCount() * 12;
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
            $this->LeasedBlackAndWhiteCharge = $this->healthcheck->getClient()->getClientSettings()->genericSettings->leasedMonochromeCostPerPage;
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
            $this->LeasedColorCharge = $this->healthcheck->getClient()->getClientSettings()->genericSettings->leasedColorCostPerPage;
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
                $this->AnnualCostOfHardwarePurchases = ($this->getDevices()->allIncludedDeviceInstances->getCount() / $averageAge) * $this->healthcheck->getClient()->getClientSettings()->genericSettings->defaultPrinterCost;
            }
            else
            {
                $this->AnnualCostOfHardwarePurchases = 0;
            }
        }

        return $this->AnnualCostOfHardwarePurchases;
    }


    /**
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
                $maxVolume += $deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume;
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
                if ($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() < ($deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE))
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
                if ($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly() > $deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume)
                {
                    $devicesOverusedCount++;
                }
            }
            $this->PercentDevicesOverused = ($devicesOverusedCount / count($this->getDevices()->allIncludedDeviceInstances->getDeviceInstances())) * 100;
        }

        return $this->PercentDevicesOverused;
    }

    /**
     * @return DeviceInstanceModel[]
     */
    public function getUnderutilizedA3Devices ()
    {
        if (!isset($this->_underutilizedA3Devices))
        {
            $devicesArray = [];
            foreach ($this->getDevices()->a3DeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                if ($deviceInstance->getPageCounts()->getPrintA3CombinedPageCount()->getMonthly() < ($deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume * self::UNDERUTILIZED_THRESHOLD_PERCENTAGE))
                {
                    $devicesArray[] = $deviceInstance;
                }
            }

            usort($devicesArray, [
                $this,
                "ascendingSortDevicesByA3Volume"
            ]);
            $this->_underutilizedA3Devices = $devicesArray;
        }

        return $this->_underutilizedA3Devices;
    }

    /**
     * Callback function for uSort when we want to sort a device based on its a3 page volume
     *
     * @param $deviceA \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel
     * @param $deviceB \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel
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
     * @return DeviceInstanceModel[]
     */
    public function getOverutilizedDevices ()
    {
        if (!isset($this->_overutilizedDevices))
        {
            $devicesArray = [];
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
            // returning only the first 2
            $deviceArray            = [
                $deviceArray [0],
                $deviceArray [1]
            ];
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
        if ($deviceA->getMonthlyRate($this->getCostPerPageSettingForCustomer()) == $deviceB->getMonthlyRate($this->getCostPerPageSettingForCustomer()))
        {
            return 0;
        }

        return ($deviceA->getMonthlyRate($this->getCostPerPageSettingForCustomer()) > $deviceB->getMonthlyRate($this->getCostPerPageSettingForCustomer())) ? -1 : 1;
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
            // returning only the first 2
            $deviceArray           = [
                $deviceArray [0],
                $deviceArray [1]
            ];
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
     * @return CostPerPageModel
     */
    public function calculateAverageOemOnlyCostPerPage ()
    {
        if (!isset($this->_averageOemOnlyCostPerPage))
        {
            $costPerPageSetting                         = clone $this->getCostPerPageSettingForCustomer();
            $oemRankSet                                 = new TonerVendorRankingSetModel();
            $costPerPageSetting->monochromeTonerRankSet = $oemRankSet;
            $costPerPageSetting->colorTonerRankSet      = $oemRankSet;

            $costPerPage                        = new CostPerPageModel();
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

            $costPerPage->monochromeCostPerPage = \Tangent\Accounting::applyMargin(
                $costPerPage->monochromeCostPerPage,
                $this->healthcheck->getClient()->getClientSettings()->genericSettings->tonerPricingMargin
            );

            $costPerPage->colorCostPerPage = \Tangent\Accounting::applyMargin(
                $costPerPage->colorCostPerPage,
                $this->healthcheck->getClient()->getClientSettings()->genericSettings->tonerPricingMargin
            );

            $this->_averageOemOnlyCostPerPage = $costPerPage;
        }

        return $this->_averageOemOnlyCostPerPage;
    }

    /**
     * Calculates the average cost per page for only toners that are Comp.
     *
     * @return CostPerPageModel
     */
    public function calculateAverageCompatibleOnlyCostPerPage ()
    {
        if (!isset($this->_averageCompatibleOnlyCostPerPage))
        {
            $costPerPageSetting   = clone $this->getCostPerPageSettingForCustomer();
            $costPerPage          = new CostPerPageModel();
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

            $costPerPage->monochromeCostPerPage = \Tangent\Accounting::applyMargin(
                $costPerPage->monochromeCostPerPage,
                $this->healthcheck->getClient()->getClientSettings()->genericSettings->tonerPricingMargin
            );

            $costPerPage->colorCostPerPage = \Tangent\Accounting::applyMargin(
                $costPerPage->colorCostPerPage,
                $this->healthcheck->getClient()->getClientSettings()->genericSettings->tonerPricingMargin
            );

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
            $this->AveragePowerCostPerMonth = $this->getAveragePowerUsagePerMonth() * DeviceInstanceModel::getKWH_Cost();

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
     * @return DeviceInstanceModel[]
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
     * @return float
     */
    public function getWeeklyITHours ()
    {
        if (!isset($this->WeeklyITHours))
        {
            $this->WeeklyITHours = $this->healthcheck->getClient()->getSurvey()->hoursSpentOnIt;
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
            $this->AverageITRate = $this->healthcheck->getClient()->getSurvey()->averageItHourlyRate;
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
     * @return float
     */
    public function getCostOfExecutingSuppliesOrders ()
    {
        if (!isset($this->CostOfExecutingSuppliesOrders))
        {
            $this->CostOfExecutingSuppliesOrders = $this->healthcheck->getClient()->getSurvey()->costToExecuteSuppliesOrder * $this->healthcheck->getClient()->getSurvey()->numberOfSupplyOrdersPerMonth * 12;
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
            $this->AnnualCostOfOutSourcing = $this->healthcheck->getClient()->getSurvey()->costOfLabor;
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

    public function png($name) {
        $myPicture = $this->getChart($name);
        if ($myPicture) {
            header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
            header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
            header( 'Cache-Control: no-store, no-cache, must-revalidate' );
            header( 'Cache-Control: post-check=0, pre-check=0', false );
            header( 'Pragma: no-cache' );
            $myPicture->autoOutput();
        }
    }

    public function getCharts()
    {
        $result=array();
        foreach (array(
                     'AverageMonthlyPagesBarGraph',
                     'AverageMonthlyPagesPerEmployeeBarGraph',
                     'EmployeesPerDeviceBarGraph',
                     'ColorCapablePrintingDevices',
                     'ColorVSBWPagesGraph',
                     'ColorCapablePieChart',
                     'DuplexCapableDevicesGraph',
                     'CopyCapableDevicesGraph',
                     'DuplexCapableDevicesGraphBig',
                     'ScanCapableDevicesGraphBig',
                     'AgePieGraph',
                     'UniqueDevicesGraph',
                     'UnmanagedVsManagedDevices',
                     'PagesWithOrWithoutJIT',
                     'PercentPerDeviceBrand',
                     'ManagedVsNotJitVsJitDevices',
                     'PagesPrintedManagedVsJitVsCompVsLeased',
                     'HardwareUtilizationCapacityBar',
                     'HardwareUtilizationCapacityPercent',
                 ) as $name) {
            $result[$name] = $this->getChart($name);
        }
        return $result;
    }

    public function getChart($name) {
        $dealerBranding = My_Brand::getDealerBranding();
        // Other variables used in several places
        $companyName = mb_strimwidth($this->healthcheck->getClient()->companyName, 0, 23, "...");
        $employeeCount = $this->healthcheck->getClient()->employeeCount;

        $hexToRGBConverter = new \Tangent\Format\HexToRGB();
        $factory = new pChartFactory();

        // Chart Styles
        $pieChartStyles = ["SecondPass" => true, "ValuePosition" => PIE_VALUE_INSIDE, "WriteValues" => PIE_VALUE_PERCENTAGE, "SliceHeight" => 10];
        $pieLegendStyles = ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "BoxSize" => 10];

        $myPicture = null;
        if (file_exists($file = dirname(__FILE__).'/charts/'.$name.'.php')) require $file;
        return $myPicture;
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
            $uniqueModelArray     = [];
            $removeArray          = [];
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
            $deviceVendorArray = [];
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
     * @return int|ClientTonerOrderModel[]
     */
    public function getClientTonerOrders ()
    {
        if (!isset($this->_clientTonerOrders))
        {
            $this->_clientTonerOrders = ClientTonerOrderMapper::getInstance()->fetchAllForClient($this->healthcheck->clientId, $this->healthcheck->dealerId);
            usort($this->_clientTonerOrders, [
                $this,
                "descendingSortClientTonerOrdersByNetSavings"
            ]);
        }

        return $this->_clientTonerOrders;
    }

    /**
     * Callback function for uSort when we want to sort Client Toner Orders by their net savings
     *
     * @param $clientTonerA \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ClientTonerOrderModel
     * @param $clientTonerB \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ClientTonerOrderModel
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
                if ($clientTonerOrder->getToner() instanceof TonerModel)
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
                if ($clientTonerOrder->getReplacementToner() instanceof TonerModel)
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
        return ($this->calculateCurrentTonerOrderCost() > 0 && $this->calculateOptimizedTonerOrderSavings() > 0) ? $this->calculateOptimizedTonerOrderSavings() / $this->calculateCurrentTonerOrderCost() * 100 : 0;
    }

    /**
     * Gets the list of fax/scan capable devices sorted by scan volume
     *
     * @return DeviceInstanceModel[]
     */
    public function getFaxAndScanTableDevices ()
    {
        $devices = [];

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

        usort($devices, [
            $this,
            "sortDescendingFaxAndScanTableData"
        ]);

        return $devices;
    }

    /**
     * Callback function for uSort when we want to sort a device based on its scan and fax page volume
     *
     * @param $deviceA \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel
     * @param $deviceB \MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel
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
