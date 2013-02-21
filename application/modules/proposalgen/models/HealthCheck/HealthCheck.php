<?php
class Proposalgen_Model_HealthCheck_HealthCheck
{
    /**@var Proposalgen_Model_Proposal_OfficeDepot */
    public $proposal;
    public $graphs = array();
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
    public function getGraphs(){

        if($this->graphs == null){
            $this->graphs = $this->proposal->getGraphs();
            $healthgraphs = array();
            $numberValueMarker                          = "N *sz0";
            $currencyValueMarker = "N $*sz0";
            $pageCounts    = $this->proposal->getPageCounts();

            /**
             * -- PagesPrinterATRPieGraph
             */
            $deviceAges = array(
                "Pages Printed on ATR devices" => 0,
                "Pages Printed on non-ATR devices"         => 0
            );
            foreach ($this->proposal->getPurchasedDevices() as $device)
            {
                if ($device->getAge() < 5)
                {
                    $deviceAges ["Pages Printed on ATR devices"]++;
                }
                else if ($device->getAge() <= 6)
                {
                    $deviceAges ["Pages Printed on non-ATR devices"]++;
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
                    $percentage     = round(($count / $this->proposal->getPurchasedDeviceCount()) * 100, 2);
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
            //$averagePageCount = round($pageCounts->Total->Combined->Monthly / $this->getDeviceCount(), 0);
            $highest          = ($this->proposal->getDeviceCount() > $this->proposal->getNumberOfColorCapableDevices()) ? $this->proposal->getDeviceCount() : $this->proposal->getNumberOfColorCapableDevices();
            $barGraph         = new gchart\gBarChart(280, 210);
            $barGraph->setTitle("Color-Capable Printing Devices");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $this->proposal->getNumberOfColorCapableDevices()
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $this->proposal->getDeviceCount() - $this->proposal->getNumberOfColorCapableDevices()
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
            $blackAndWhitePageCount = $pageCounts->Total->Combined->Monthly - $pageCounts->Total->Color->Monthly;

            $highest          = ($pageCounts->Total->Color->Monthly > $blackAndWhitePageCount) ? $pageCounts->Total->Color->Monthly : $blackAndWhitePageCount;
            $barGraph         = new gchart\gBarChart(280, 210);
            $barGraph->setTitle("Color vs Black/White Pages");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $pageCounts->Total->Color->Monthly
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
             * -- PagesPrintedOnATR
             * FIXME USE ATR FUNCTIONS
             */
            $colorPercentage = 0;
            if ($pageCounts->Total->Combined->Monthly > 0)
            {
                $colorPercentage = round((($pageCounts->Total->Color->Monthly / $pageCounts->Total->Combined->Monthly) * 100), 2);
            }

            $bwPercentage        = 100 - $colorPercentage;
            $colorVSBWPagesGraph = new gchart\gPie3DChart(305, 210);
            $colorVSBWPagesGraph->addDataSet(array(
                                                  $colorPercentage,
                                                  $bwPercentage
                                             ));
            $colorVSBWPagesGraph->setLegend(array(
                                                 "Pages printed on ATR devices",
                                                 "Pages printed on non-ATR devices"
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
            // PagesPrintedOnATR
            $healthgraphs['PagesPrintedOnATR'] = $colorVSBWPagesGraph->getUrl();

            /**
             * -- CompatibleATRBarGraph
             */
            $highest  = ($this->proposal->getLeasedDeviceCount() > $this->proposal->getPurchasedDeviceCount()) ? $this->proposal->getLeasedDeviceCount() : $this->proposal->getPurchasedDeviceCount();
            $barGraph = new gchart\gBarChart(220, 220);
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $this->proposal->getLeasedDeviceCount()
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $this->proposal->getPurchasedDeviceCount()
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(70, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      "Compatible with auto toner replenishment",
                                      "Not compatible with auto toner replenishment"
                                 ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // CompatibleATRBarGraph
            $healthgraphs['CompatibleATRBarGraph'] = $barGraph->getUrl();

            /**
             * -- DifferenceBarGraph
             */
            $highest  = ($this->proposal->getLeasedDeviceCount() > $this->proposal->getPurchasedDeviceCount()) ? $this->proposal->getLeasedDeviceCount() : $this->proposal->getPurchasedDeviceCount();
            $barGraph = new gchart\gBarChart(280, 230);
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $this->proposal->getLeasedDeviceCount()
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $this->proposal->getPurchasedDeviceCount()
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(70, 10);
            $barGraph->setLegendPosition("b");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      "OEM Toner",
                                      "Compatible Toner"
                                 ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // DifferenceBarGraph
            $healthgraphs['DifferenceBarGraph'] = $barGraph->getUrl();

            /**
             * -- HardwareUtilizationCapacityPercent
             */
            $percentage = ($pageCounts->Total->Combined->Monthly / $this->proposal->getMaximumMonthlyPrintVolume());
            $highest  = 100;
            $barGraph = new gchart\gStackedBarChart(600, 160);
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
            $dotProperties = '@o,E21736,0,.5:' . number_format($percentage,2) . ',15|';
            //Add onto the last property, @t = a text message, color,0,height,positon - halfish of the text width, size
            $dotProperties .= '@t' . number_format($percentage * 100,2) . '%,000000,0,-2.5:' . number_format($percentage - .03,2)  . ',10';
            $barGraph->setProperty('chm',$dotProperties);
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
            foreach ($this->proposal->getPurchasedDevices() as $device)
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
            $highest  = ($this->proposal->getLeasedDeviceCount() > $this->proposal->getPurchasedDeviceCount()) ? $this->proposal->getLeasedDeviceCount() : $this->proposal->getPurchasedDeviceCount();
            $highest = $deviceAges ["Less than 3 years old"];
            if($highest < $deviceAges["3-5 years old"])
                $highest = $deviceAges["3-5 years old"];
            if($highest < $deviceAges["6-8 years old"])
                $highest = $deviceAges["6-8 years old"];
            if($highest < $deviceAges["More than 8 years old"])
                $highest = $deviceAges["More than 8 years old"];
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

            $this->graphs['healthCheck'] = $healthgraphs;
        }

        return $this->graphs;
    }
}