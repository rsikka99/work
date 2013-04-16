<?php
class Proposalgen_Model_Healthcheck_Healthcheck
{
    /**@var Proposalgen_Model_Proposal_OfficeDepot */
    public $proposal;
    protected $_graphs = array();
    const GALLONS_WATER_PER_PAGE = 0.121675; // Number of pages * this gives amount of gallons
    const TREE_PER_PAGE = 7800; //Number of pages divided by this, gives amount of trees
    public $reportSettings;

    /**
     * @param Proposalgen_Model_Proposal_OfficeDepot $Proposal
     */
    public function __construct (Proposalgen_Model_Proposal_OfficeDepot $Proposal)
    {
        $this->proposal = $Proposal;
        // Get the report settings
        $this->reportSettings = $this->proposal->report->getReportSettings();
    }

    public function getGraphs ()
    {

        if ($this->_graphs == null)
        {
            $this->_graphs                     = $this->proposal->getGraphs();
            $healthgraphs                      = array();
            $numberValueMarker                 = "N *sz0";
            $currencyValueMarker               = "N $*sz0";
            $pageCounts                        = $this->proposal->getPageCounts();
            $companyName                       = $this->proposal->report->getClient()->companyName;
            $OD_AverageMonthlyPagesPerEmployee = 200;
            $OD_AverageMonthlyPages            = 4200;
            $OD_AverageEmployeesPerDevice      = 4.4;
            $employeeCount                     = $this->proposal->report->getClient()->employeeCount;
            /**
             * -- PagesPrinterATRPieGraph
             */
            $deviceAges = array(
                "Pages Printed on JIT devices"     => 0,
                "Pages Printed on non-JIT devices" => 0
            );
            foreach ($this->proposal->getPurchasedDevices() as $device)
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
                    $percentage     = round(($count / $this->proposal->getPageCounts()->Purchased->Combined->Monthly) * 100, 2);
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
            $healthgraphs['PagesPrinterATRPieGraph'] = $deviceAgeGraph->getUrl();

            /**
             * -- HardwareUtilizationCapacityBar
             */
            $highest  = ($this->proposal->getMaximumMonthlyPrintVolume() > $pageCounts->Total->Combined->Monthly) ? $this->proposal->getMaximumMonthlyPrintVolume() : $pageCounts->Total->Combined->Monthly;
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
                                       $this->proposal->getMaximumMonthlyPrintVolume()
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
            $healthgraphs['HardwareUtilizationCapacityBar'] = $barGraph->getUrl();


            /**
             * -- ColorCapablePrintingDevices
             */
            $highest  = ($this->proposal->getPurchasedDeviceCount() - $this->proposal->getNumberOfColorCapablePurchasedDevices() > $this->proposal->getNumberOfColorCapablePurchasedDevices()) ? ($this->proposal->getPurchasedDeviceCount() - $this->proposal->getNumberOfColorCapablePurchasedDevices()) : $this->proposal->getNumberOfColorCapablePurchasedDevices();
            $barGraph = new gchart\gBarChart(280, 210);
            $barGraph->setTitle("Color-Capable Printing Devices");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $this->proposal->getNumberOfColorCapablePurchasedDevices()
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $this->proposal->getPurchasedDeviceCount() - $this->proposal->getNumberOfColorCapablePurchasedDevices()
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
            $healthgraphs['ColorCapablePrintingDevices'] = $barGraph->getUrl();


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
            $healthgraphs['ColorVSBWPagesGraph'] = $barGraph->getUrl();

            /**
             * -- colorCapablePieChart
             */
            $colorPercentage = 0;
            if ($this->proposal->getDeviceCount())
            {
                $colorPercentage = round((($this->proposal->getNumberOfColorCapableDevices() / $this->proposal->getDeviceCount()) * 100), 2);
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
            $healthgraphs['colorCapablePieChart'] = $colorCapableGraph->getUrl();

            /**
             * -- CompatibleATRBarGraph
             */
            $highest  = ($this->proposal->getNumberOfDevicesReportingTonerLevels() > $this->proposal->getNumberOfDevicesNotReportingTonerLevels() ? $this->proposal->getNumberOfDevicesReportingTonerLevels() : ($this->proposal->getNumberOfDevicesNotReportingTonerLevels()));
            $barGraph = new gchart\gBarChart(220, 220);
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $this->proposal->getNumberOfDevicesReportingTonerLevels()
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       ($this->proposal->getNumberOfDevicesNotReportingTonerLevels())
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
            $healthgraphs['CompatibleATRBarGraph'] = $barGraph->getUrl();

            $oemCost  = ($this->proposal->calculateAverageOemOnlyCostPerPage()->colorCostPerPage * $this->proposal->getPageCounts()->Purchased->Color->Monthly + ($this->proposal->calculateAverageOemOnlyCostPerPage()->monochromeCostPerPage * $this->proposal->getPageCounts()->Purchased->BlackAndWhite->Monthly)) * 12;
            $compCost = ($this->proposal->calculateAverageCompatibleOnlyCostPerPage()->colorCostPerPage * $this->proposal->getPageCounts()->Purchased->Color->Monthly + ($this->proposal->calculateAverageCompatibleOnlyCostPerPage()->monochromeCostPerPage * $this->proposal->getPageCounts()->Purchased->BlackAndWhite->Monthly)) * 12;
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
            $healthgraphs['DifferenceBarGraph'] = $barGraph->getUrl();

            /**
             * -- HardwareUtilizationCapacityPercent
             */
            $percentage = ($pageCounts->Total->Combined->Monthly / $this->proposal->getMaximumMonthlyPrintVolume());
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
            $healthgraphs['HardwareUtilizationCapacityPercent'] = $barGraph->getUrl();

            /**
             * -- AgeBarGraph
             */
            $deviceAges = array(
                "Less than 3 years old" => 0,
                "3-5 years old"         => 0,
                "6-8 years old"         => 0,
                "More than 8 years old" => 0
            );
            foreach ($this->proposal->getDevices()->allIncludedDeviceInstances as $device)
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
            $healthgraphs['AgeBarGraph'] = $barGraph->getUrl();

            /**
             * -- DuplexCapableDevicesGraph
             */
            $duplexPercentage = 0;
            if ($this->proposal->getDeviceCount())
            {
                $duplexPercentage = round((($this->proposal->getNumberOfDuplexCapableDevices() / $this->proposal->getDeviceCount()) * 100), 2);
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
            $healthgraphs['DuplexCapableDevicesGraph'] = $duplexCapableGraph->getUrl();

            /**
             * -- AverageMonthlyPagesBarGraph
             */
            $averagePageCount = round($pageCounts->Total->Combined->Monthly / $this->proposal->getDeviceCount(), 0);
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
            $healthgraphs['AverageMonthlyPagesBarGraph'] = $barGraph->getUrl();

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
            $healthgraphs ['AverageMonthlyPagesPerEmployeeBarGraph'] = $barGraph->getUrl();

            /**
             * -- EmployeesPerDeviceBarGraph
             */
            $devicesPerEmployee = round($employeeCount / $this->proposal->getDeviceCount(), 2);
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
            $healthgraphs['EmployeesPerDeviceBarGraph'] = $barGraph->getUrl();

            /**
             * -- CopyCapableDevicesGraph
             */
            if ($this->proposal->getDeviceCount())
            {
                $copyPercentage = round((($this->proposal->getNumberOfCopyCapableDevices() / $this->proposal->getDeviceCount()) * 100), 2);
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
            $healthgraphs['CopyCapableDevicesGraph'] = $copyCapableGraph->getUrl();

            $this->_graphs = array_merge($healthgraphs, $this->_graphs);
        }

        return $this->_graphs;
    }

    /**
     * Calculates the number of trees used.
     *
     * @return float
     */
    public function calculateNumberOfTreesUsed ()
    {
        return $this->proposal->getPageCounts()->Total->Combined->Yearly / self::TREE_PER_PAGE;
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
        return $this->proposal->getPageCounts()->Total->Combined->Yearly * self::GALLONS_WATER_PER_PAGE;
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