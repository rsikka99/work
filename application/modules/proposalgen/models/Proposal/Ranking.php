<?php

/**
 * Proposalgen_Model_Proposal_Ranking
 * @author	Lee Robert
 * @version	v1.0
 */
class Proposalgen_Model_Proposal_Ranking extends Tangent_Model_Abstract
{
    protected $proposal;
    
    // Rankings
    protected $SuppliesAndServiceLogistics;
    protected $PrintingHardwareUsage;
    protected $TechnologyReliabilityAndUserProductivity;
    protected $EnvironmentalFriendliness;
    protected $Expense;
    protected $RankingCriteria;
    
    /**
     * The constructor for proposal rankings.
     * Requires a reference to a proposal object
     *
     * @param $proposal Proposalgen_Model_Proposal_Abstract
     */
    public function __construct (Proposalgen_Model_Proposal_OfficeDepot $proposal)
    {
        $this->proposal = $proposal;
    }

    /**
     *
     * @return the $SuppliesAndServiceLogistics
     */
    public function getSuppliesAndServiceLogistics ()
    {
        if (! isset($this->SuppliesAndServiceLogistics))
        {
            
            // Service and supplies logistics
            $criteria = $this->getRankingCriteria();
            $criteria = $criteria ["ServiceAndSuppliesLogistics"];
            $ranking ["inkjetRanking"] = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getPercentPrintingDoneOnInkjet(), $criteria ["InkJetPrinting"]);
            $ranking ["breakdownRanking"] = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getNumberOfRepairs() / $this->proposal->getDeviceCount() * 100, $criteria ["EfficiencyOfBreakFix"]);
            $ranking ["repairTimeRanking"] = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getAverageTimeBetweenBreakdownAndFix(), $criteria ["TimeToRepair"]);
            $ranking ["vendorCountRanking"] = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getUniqueVendorCount(), $criteria ["VendorCount"]);
            $ranking ["monthlyOrdersRanking"] = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getNumberOfOrdersPerMonth(), $criteria ["OrdersPerMonth"]);
            $ranking ["uniqueModelsRanking"] = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getNumberOfUniqueModels() / $this->proposal->getDeviceCount() * 100, $criteria ["ModelPercentage"]);
            
            $totalRanking = round(((($ranking ["inkjetRanking"]) + (($ranking ["breakdownRanking"] + $ranking ["repairTimeRanking"]) / 2) + (($ranking ["vendorCountRanking"] + $ranking ["monthlyOrdersRanking"] + $ranking ["uniqueModelsRanking"]) / 3)) / 3), 1);
            
            $rankingText = $this->getOverallRankingText($totalRanking, "supplies and service logistics");
            $areasToImprove = array ();
            if ($ranking ["inkjetRanking"] <= $totalRanking)
                $areasToImprove [] = "reduce the number of pages printed to desktop inkjet devices";
            if ($ranking ["breakdownRanking"] <= $totalRanking)
                $areasToImprove [] = "update  your printing devices to more reliable machines";
            if ($ranking ["repairTimeRanking"] <= $totalRanking)
                $areasToImprove [] = "use a managed print program to increase efficiency of service";
            if ($ranking ["vendorCountRanking"] <= $totalRanking)
                $areasToImprove [] = "consolidate service and supplies vendors for simpler, more efficient processes";
            if ($ranking ["uniqueModelsRanking"] <= $totalRanking)
                $areasToImprove [] = "streamline print devices to reduce the amount of different supplies and parts required";
            
            if (count($areasToImprove) > 0)
            {
                $rankingText .= " To improve your score in this area, " . $this->proposal->getReport()->getCustomerCompanyName() . " could ";
                foreach ( $areasToImprove as $improvementText )
                {
                    $rankingText .= $improvementText . ", ";
                }
                $rankingText = trim(trim($rankingText), ",") . ".";
            }
            
            $this->SuppliesAndServiceLogistics = (object)array (
                    "Rank" => $totalRanking, 
                    "RankingText" => $rankingText 
            );
        }
        return $this->SuppliesAndServiceLogistics;
    }

    /**
     *
     * @param
     *            SuppliesAndServiceLogistics field_type
     */
    public function setSuppliesAndServiceLogistics ($SuppliesAndServiceLogistics)
    {
        $this->SuppliesAndServiceLogistics = $SuppliesAndServiceLogistics;
        return $this;
    }

    /**
     *
     * @return the $PrintingHardwareUsage
     */
    public function getPrintingHardwareUsage ()
    {
        if (! isset($this->PrintingHardwareUsage))
        {
            $criteria = $this->getRankingCriteria();
            $criteria = $criteria ["PrintingHardwareUsage"];
            $AverageMonthlyPrintVolumePerPrinter = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getPageCounts()->Purchased->Combined->Monthly, $criteria ["AverageMonthlyPrintVolumePerPrinter"]);
            $AverageMonthlyPrintVolumePerEmployee = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getPageCounts()->Total->Combined->Monthly / $this->proposal->getEmployeeCount(), $criteria ["AverageMonthlyPrintVolumePerEmployee"]);
            $NumberOfEmployeesPerDevice = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getEmployeeCount() / $this->proposal->getDeviceCount(), $criteria ["NumberOfEmployeesPerDevice"]);
            $UnderusedDevices = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getPercentDevicesUnderused(), $criteria ["UnderusedDevices"]);
            $OverusedDevices = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getPercentDevicesOverused(), $criteria ["OverusedDevices"]);
            
            $totalRanking = round(((($AverageMonthlyPrintVolumePerPrinter + $AverageMonthlyPrintVolumePerEmployee + $NumberOfEmployeesPerDevice) / 3) + (($UnderusedDevices + $OverusedDevices) / 2)) / 2, 1);
            
            $rankingText = $this->getOverallRankingText($totalRanking, "printer hardware usage");
            $areasToImprove = array ();
            if ($AverageMonthlyPrintVolumePerPrinter <= $totalRanking)
                $areasToImprove [] = "reduce the number of printing devices in your office through consolidation or device retirement";
            if ($UnderusedDevices <= $totalRanking || $OverusedDevices <= $totalRanking)
                $areasToImprove [] = "reallocate printing assets to areas where the printing volumes match the capacities of the printers";
            
            if (count($areasToImprove) > 0)
            {
                $rankingText .= " To improve your score in this area, " . $this->proposal->getReport()->getCustomerCompanyName() . " could ";
                foreach ( $areasToImprove as $improvementText )
                {
                    $rankingText .= $improvementText . ", ";
                }
                $rankingText = trim(trim($rankingText), ",") . ".";
            }
            
            $this->PrintingHardwareUsage = (object)array (
                    "Rank" => $totalRanking, 
                    "RankingText" => $rankingText 
            );
        }
        return $this->PrintingHardwareUsage;
    }

    /**
     *
     * @param
     *            PrintingHardwareUsage field_type
     */
    public function setPrintingHardwareUsage ($PrintingHardwareUsage)
    {
        $this->PrintingHardwareUsage = $PrintingHardwareUsage;
        return $this;
    }

    /**
     *
     * @return the $TechnologyReliabilityAndUserProductivity
     */
    public function getTechnologyReliabilityAndUserProductivity ()
    {
        if (! isset($this->TechnologyReliabilityAndUserProductivity))
        {
            $criteria = $this->getRankingCriteria();
            $criteria = $criteria ["TechnologyReliabilityAndUserProductivity"];
            $Averageage = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getAverageDeviceAge(), $criteria ["Averageage"]);
            $PercentITTime = Tangent_Functions::getValueFromRangeStepTable(($this->proposal->getweeklyITHours() * 60) / $this->proposal->getDeviceCount(), $criteria ["PercentITTime"]);
            $ScanCapable = Tangent_Functions::getValueFromRangeStepTable(($this->proposal->getNumberOfScanCapableDevices() / $this->proposal->getDeviceCount()) * 100, $criteria ["ScanCapable"]);
            $FaxCapable = Tangent_Functions::getValueFromRangeStepTable(($this->proposal->getNumberOfFaxCapableDevices() / $this->proposal->getDeviceCount()) * 100, $criteria ["FaxCapable"]);
            $ColorCapable = Tangent_Functions::getValueFromRangeStepTable(($this->proposal->getNumberOfColorCapableDevices() / $this->proposal->getDeviceCount()) * 100, $criteria ["ColorCapable"]);
            $technologyFeatures = ($ScanCapable + $FaxCapable + $ColorCapable) / 3;
            $totalRanking = round(((($Averageage + $PercentITTime) / 2) + $technologyFeatures) / 2, 1);
            
            $rankingText = $this->getOverallRankingText($totalRanking, "technology reliability and user productivity");
            $areasToImprove = array ();
            if ($Averageage <= $totalRanking)
                $areasToImprove [] = "update your printing devices to newer, more reliable machines";
            if ($PercentITTime <= $totalRanking)
                $areasToImprove [] = "use a managed print program to help reduce the amount of time your IT staff spend on printing-related issues";
            if ($technologyFeatures <= $totalRanking)
                $areasToImprove [] = "consolidate single-function printers, standalone copiers, scanners and fax machines into fewer multifunction devices";
            
            if (count($areasToImprove) > 0)
            {
                $rankingText .= " To improve your score in this area, " . $this->proposal->getReport()->getCustomerCompanyName() . " could ";
                foreach ( $areasToImprove as $improvementText )
                {
                    $rankingText .= $improvementText . ", ";
                }
                $rankingText = trim(trim($rankingText), ",") . ".";
            }
            
            $this->TechnologyReliabilityAndUserProductivity = (object)array (
                    "Rank" => $totalRanking, 
                    "RankingText" => $rankingText 
            );
        }
        return $this->TechnologyReliabilityAndUserProductivity;
    }

    /**
     *
     * @param
     *            TechnologyReliabilityAndUserProductivity field_type
     */
    public function setTechnologyReliabilityAndUserProductivity ($TechnologyReliabilityAndUserProductivity)
    {
        $this->TechnologyReliabilityAndUserProductivity = $TechnologyReliabilityAndUserProductivity;
        return $this;
    }

    /**
     *
     * @return the $EnvironmentalFriendliness
     */
    public function getEnvironmentalFriendliness ()
    {
        if (! isset($this->EnvironmentalFriendliness))
        {
            $rankingsCalculated = 0;
            $criteria = $this->getRankingCriteria();
            $criteria = $criteria ["EnvironmentalFriendliness"];
            $AverageKWHPerDevicePerMonth = 0;
            if ($this->proposal->getPercentageOfDevicesReportingPower() > $this->proposal->getDevicesReportingPowerThreshold())
            {
                $AverageKWHPerDevicePerMonth = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getAveragePowerUsagePerMonth() / $this->proposal->getDeviceCount(), $criteria ["AverageKWHPerDevicePerMonth"]);
                $rankingsCalculated ++;
            }
            
            $AverageOperatingWatts = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getAverageOperatingWatts(), $criteria ["AverageOperatingWatts"]);
            $rankingsCalculated ++;
            
            $DuplexCapable = Tangent_Functions::getValueFromRangeStepTable(($this->proposal->getNumberOfDuplexCapableDevices() / $this->proposal->getDeviceCount()) * 100, $criteria ["DuplexCapable"]);
            $ScanCapable = Tangent_Functions::getValueFromRangeStepTable(($this->proposal->getNumberOfScanCapableDevices() / $this->proposal->getDeviceCount()) * 100, $criteria ["ScanCapable"]);
            $greenFeatures = ($DuplexCapable + $ScanCapable) / 2;
            $rankingsCalculated ++;
            
            $totalRanking = round((($AverageKWHPerDevicePerMonth + $AverageOperatingWatts + ($greenFeatures)) / $rankingsCalculated), 1);
            
            $rankingText = $this->getOverallRankingText($totalRanking, "environmental friendliness");
            $areasToImprove = array ();
            if ($AverageKWHPerDevicePerMonth <= $totalRanking || $AverageOperatingWatts <= $totalRanking)
                $areasToImprove [] = "retire equipment that consumes a lot of energy or move page volumes to more energy-efficient machines";
            if ($greenFeatures <= $totalRanking)
                $areasToImprove [] = "consider equipment that has duplex and scanning features when you require new equipment";
            
            if (count($areasToImprove) > 0)
            {
                $rankingText .= " To improve your score in this area, " . $this->proposal->getReport()->getCustomerCompanyName() . " could ";
                foreach ( $areasToImprove as $improvementText )
                {
                    $rankingText .= $improvementText . ", ";
                }
                $rankingText = trim(trim($rankingText), ",") . ".";
            }
            
            $this->EnvironmentalFriendliness = (object)array (
                    "Rank" => $totalRanking, 
                    "RankingText" => $rankingText 
            );
        }
        return $this->EnvironmentalFriendliness;
    }

    /**
     *
     * @param
     *            EnvironmentalFriendliness field_type
     */
    public function setEnvironmentalFriendliness ($EnvironmentalFriendliness)
    {
        $this->EnvironmentalFriendliness = $EnvironmentalFriendliness;
        return $this;
    }

    /**
     *
     * @return the $Expense
     */
    public function getExpense ()
    {
        if (! isset($this->Expense))
        {
            $criteria = $this->getRankingCriteria();
            $criteria = $criteria ["Expense"];
            $LeasedBWPerPage = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getLeasedEstimatedBlackAndWhiteCPP(), $criteria ["LeasedBWPerPage"]);
            $LeasedColorPerPage = $LeasedBWPerPage;
            if ($this->proposal->getLeasedEstimatedColorCPP() > 0)
            {
                $LeasedColorPerPage = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getLeasedEstimatedColorCPP(), $criteria ["LeasedColorPerPage"]);
            }
            $PurchasedBWPerPage = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getEstimatedAllInBlackAndWhiteCPP(), $criteria ["PurchasedBWPerPage"]);
            $PurchasedColorPerPage = Tangent_Functions::getValueFromRangeStepTable($this->proposal->getEstimatedAllInColorCPP(), $criteria ["PurchasedColorPerPage"]);
            
            $totalRanking = round((((($LeasedBWPerPage + $LeasedColorPerPage) / 2) + (($PurchasedBWPerPage + $PurchasedColorPerPage) / 2)) / 2), 1);
            
            $rankingText = $this->getOverallRankingText($totalRanking, "expense");
            $rankingText .= " Under the PrintIQ® program, we estimate that we can save " . $this->proposal->getReport()->getCustomerCompanyName() . " up to $" . number_format($this->proposal->getPrintIQSavings()) . " annually.";
            
            $this->Expense = (object)array (
                    "Rank" => $totalRanking, 
                    "RankingText" => $rankingText 
            );
        }
        return $this->Expense;
    }

    /**
     *
     * @param
     *            Expense field_type
     */
    public function setExpense ($Expense)
    {
        $this->Expense = $Expense;
        return $this;
    }

    /**
     * Gets the overal paragraph
     *
     * @param $rank float           
     * @param $sectionname string           
     * @return string Returns the overall paragraph
     */
    public function getOverallRankingText ($rank, $sectionname)
    {
        $text = "";
        $ratings = array (
                "poor" => 2.0, 
                "below average" => 4.0, 
                "average" => 6.0, 
                "above average" => 8.0, 
                "excellent" => 10.0 
        );
        foreach ( $ratings as $ratingtext => $rating )
        {
            if ($rank <= $rating)
            {
                break;
            }
        }
        $text = "Overall, your score in $sectionname is $ratingtext compared to other companies. ";
        return $text;
    }

    /**
     *
     * @return the $RankingCriteria
     */
    public function getRankingCriteria ()
    {
        if (! isset($this->RankingCriteria))
        {
            // The following array is in the following range step format
            // array($valueToCheck => $ranking)
            $this->RankingCriteria = array (
                    "ServiceAndSuppliesLogistics" => array (
                            "InkJetPrinting" => array (
                                    10 => 10, 
                                    7 => 25, 
                                    4 => 50, 
                                    1 => 100 
                            ), 
                            "EfficiencyOfBreakFix" => array (
                                    10 => 4.35, 
                                    9 => 5.12, 
                                    8 => 6.02, 
                                    7 => 7.08, 
                                    6 => 8.33, 
                                    5 => 9.58, 
                                    4 => 11.02, 
                                    3 => 12.67, 
                                    2 => 14.58, 
                                    1 => 16.76 
                            ), 
                            "TimeToRepair" => array (
                                    9 => 0.5, 
                                    7 => 1.5, 
                                    5 => 2.5, 
                                    3 => 5, 
                                    1 => 365 
                            ), 
                            "VendorCount" => array (
                                    10 => 1, 
                                    8 => 2, 
                                    6 => 3, 
                                    4 => 4, 
                                    2 => 5, 
                                    1 => 6 
                            ), 
                            "OrdersPerMonth" => array (
                                    10 => 1, 
                                    9 => 2, 
                                    8 => 3, 
                                    7 => 4, 
                                    6 => 5, 
                                    5 => 8, 
                                    4 => 11, 
                                    3 => 14, 
                                    2 => 17, 
                                    1 => 18 
                            ), 
                            "ModelPercentage" => array (
                                    10 => 10, 
                                    9 => 20, 
                                    8 => 30, 
                                    7 => 40, 
                                    6 => 50, 
                                    5 => 60, 
                                    4 => 70, 
                                    3 => 80, 
                                    2 => 90, 
                                    1 => 100 
                            ) 
                    ), 
                    "PrintingHardwareUsage" => array (
                            "AverageMonthlyPrintVolumePerPrinter" => array (
                                    1 => 2480, 
                                    2 => 2756, 
                                    3 => 3062, 
                                    4 => 3402, 
                                    5 => 3780, 
                                    6 => 4200, 
                                    7 => 4620, 
                                    8 => 5082, 
                                    9 => 5590, 
                                    10 => 6149 
                            ), 
                            "AverageMonthlyPrintVolumePerEmployee" => array (
                                    10 => 328, 
                                    9 => 365, 
                                    8 => 405, 
                                    7 => 450, 
                                    6 => 500, 
                                    5 => 550, 
                                    4 => 605, 
                                    3 => 666, 
                                    2 => 732, 
                                    1 => 805 
                            ), 
                            "NumberOfEmployeesPerDevice" => array (
                                    1 => 1.95, 
                                    2 => 2.30, 
                                    3 => 2.70, 
                                    4 => 3.18, 
                                    5 => 3.74, 
                                    6 => 4.40, 
                                    7 => 5.06, 
                                    8 => 5.82, 
                                    9 => 6.69, 
                                    10 => 7.70 
                            ), 
                            "UnderusedDevices" => array (
                                    10 => 10, 
                                    9 => 20, 
                                    8 => 30, 
                                    7 => 40, 
                                    6 => 50, 
                                    5 => 60, 
                                    4 => 70, 
                                    3 => 80, 
                                    2 => 90, 
                                    1 => 100 
                            ), 
                            "OverusedDevices" => array (
                                    10 => 10, 
                                    9 => 20, 
                                    8 => 30, 
                                    7 => 40, 
                                    6 => 50, 
                                    5 => 60, 
                                    4 => 70, 
                                    3 => 80, 
                                    2 => 90, 
                                    1 => 100 
                            ) 
                    ), 
                    "TechnologyReliabilityAndUserProductivity" => array (
                            "Averageage" => array (
                                    10 => 3.28, 
                                    9 => 3.65, 
                                    8 => 4.05, 
                                    7 => 4.50, 
                                    6 => 5.00, 
                                    5 => 5.50, 
                                    4 => 6.05, 
                                    3 => 6.66, 
                                    2 => 7.32, 
                                    1 => 8.05 
                            ), 
                            "PercentITTime" => array (
                                    10 => 10, 
                                    9 => 11, 
                                    8 => 12, 
                                    7 => 14, 
                                    6 => 15, 
                                    5 => 17, 
                                    4 => 18, 
                                    3 => 20, 
                                    2 => 22, 
                                    1 => 24 
                            ), 
                            "ScanCapable" => array (
                                    1 => 0, 
                                    2 => 10, 
                                    3 => 20, 
                                    4 => 30, 
                                    5 => 40, 
                                    6 => 50, 
                                    7 => 60, 
                                    8 => 70, 
                                    9 => 80, 
                                    10 => 90 
                            ), 
                            "FaxCapable" => array (
                                    1 => 0, 
                                    2 => 10, 
                                    3 => 20, 
                                    4 => 30, 
                                    5 => 40, 
                                    6 => 50, 
                                    7 => 60, 
                                    8 => 70, 
                                    9 => 80, 
                                    10 => 90 
                            ), 
                            "ColorCapable" => array (
                                    1 => 0, 
                                    2 => 10, 
                                    3 => 20, 
                                    4 => 30, 
                                    5 => 40, 
                                    6 => 50, 
                                    7 => 60, 
                                    8 => 70, 
                                    9 => 80, 
                                    10 => 90 
                            ) 
                    ), 
                    "EnvironmentalFriendliness" => array (
                            "AverageKWHPerDevicePerMonth" => array (
                                    10 => 26.24, 
                                    9 => 29.16, 
                                    8 => 32.40, 
                                    7 => 36.00, 
                                    6 => 40.00, 
                                    5 => 44.00, 
                                    4 => 48.40, 
                                    3 => 53.24, 
                                    2 => 58.56, 
                                    1 => 64.42 
                            ), 
                            "AverageOperatingWatts" => array (
                                    10 => 100, 
                                    9 => 200, 
                                    8 => 300, 
                                    7 => 400, 
                                    6 => 500, 
                                    5 => 600, 
                                    4 => 700, 
                                    3 => 800, 
                                    2 => 900, 
                                    1 => 1000 
                            ), 
                            "DuplexCapable" => array (
                                    1 => 0, 
                                    2 => 10, 
                                    3 => 20, 
                                    4 => 30, 
                                    5 => 40, 
                                    6 => 50, 
                                    7 => 60, 
                                    8 => 70, 
                                    9 => 80, 
                                    10 => 90 
                            ), 
                            "ScanCapable" => array (
                                    1 => 0, 
                                    2 => 10, 
                                    3 => 20, 
                                    4 => 30, 
                                    5 => 40, 
                                    6 => 50, 
                                    7 => 60, 
                                    8 => 70, 
                                    9 => 80, 
                                    10 => 90 
                            ) 
                    ), 
                    "Expense" => array (
                            "LeasedBWPerPage" => array (
                                    10 => 0.0200, 
                                    9 => 0.0220, 
                                    8 => 0.0242, 
                                    7 => 0.0266, 
                                    6 => 0.0293, 
                                    5 => 0.0322, 
                                    4 => 0.0354, 
                                    3 => 0.0390, 
                                    2 => 0.0429, 
                                    1 => 0.0472 
                            ), 
                            "LeasedColorPerPage" => array (
                                    10 => 0.0900, 
                                    9 => 0.0990, 
                                    8 => 0.1089, 
                                    7 => 0.1198, 
                                    6 => 0.1318, 
                                    5 => 0.1449, 
                                    4 => 0.1594, 
                                    3 => 0.1754, 
                                    2 => 0.1929, 
                                    1 => 0.2122 
                            ), 
                            "PurchasedBWPerPage" => array (
                                    10 => 0.0200, 
                                    9 => 0.0220, 
                                    8 => 0.0242, 
                                    7 => 0.0266, 
                                    6 => 0.0293, 
                                    5 => 0.0322, 
                                    4 => 0.0354, 
                                    3 => 0.0390, 
                                    2 => 0.0429, 
                                    1 => 0.0472 
                            ), 
                            "PurchasedColorPerPage" => array (
                                    10 => 0.0900, 
                                    9 => 0.0990, 
                                    8 => 0.1089, 
                                    7 => 0.1198, 
                                    6 => 0.1318, 
                                    5 => 0.1449, 
                                    4 => 0.1594, 
                                    3 => 0.1754, 
                                    2 => 0.1929, 
                                    1 => 0.2122 
                            ) 
                    ) 
            );
        }
        return $this->RankingCriteria;
    }

    /**
     *
     * @param $RankingCriteria field_type           
     */
    public function setRankingCriteria ($RankingCriteria)
    {
        $this->RankingCriteria = $RankingCriteria;
        return $this;
    }

}
