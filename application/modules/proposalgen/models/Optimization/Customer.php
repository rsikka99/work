<?php
class Proposalgen_Model_Optimization_Customer extends
    Proposalgen_Model_Optimization_Abstract
{
    /**
     * a list of string to image resources to the graphs
     *
     * @var string []
     */
    protected $_graphs;

    public function getGraphs ()
    {
        if (!isset($this->_graphs))
        {
            $proposal            = $this->proposal;
            $proposalGraphs      = $proposal->getGraphs();
            $purchaseDeviceCount = count($proposal->getPurchasedDevices());

            // N = number, p = percent, 0 = number of decimal places.
            // p will take 0.1544 as a number and convert it to 15%
            $percentValueMarker = "N  *p0";
            $numberValueMarker  = "N *sz0";
            // Graph0
            $this->_graphs [] = $proposalGraphs[4];
            // Graph1
            $this->_graphs [] = $proposalGraphs[5];

            /**
             * -- LeasedVsPurchasedPageCountBarGraph
             */
            $pageCounts = $proposal->getPageCounts();
            $highest    = ($pageCounts->Leased->Combined->Monthly > $pageCounts->Purchased->Combined->Monthly) ? $pageCounts->Leased->Combined->Monthly : $pageCounts->Purchased->Combined->Monthly;
            $barGraph   = new gchart\gBarChart(200, 300);
            $barGraph->setTitle("Leased Vs Purchase|Page Counts");
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
            $barGraph->setBarScale(40, 10);
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
            // Graph2
            $this->_graphs [] = $barGraph->getUrl();


            /**
             * -- Hardware Optimization Device Summary Listing
             */
            $graph = new gchart\gBarChart(425, 325, "g", "h");
            $graph->setVisibleAxes(array('x'));

            $count = count($this->kept);
            $graph->addDataSet(array(($count / $purchaseDeviceCount)));
            $percentValueMarker1 = "N  *p0* ({$count})";
            $graph->addColors(array("009999"));

            $count = count($this->replaced);
            $graph->addDataSet(array($count / $purchaseDeviceCount));
            $percentValueMarker2 = "N  *p0* ({$count})";
            $graph->addColors(array("9bbb59"));

            $count = count($this->flagged);
            $graph->addDataSet(array(count($this->flagged) / $purchaseDeviceCount));
            $percentValueMarker3 = "N  *p0* ({$count})";
            $graph->addColors(array("FFAA00"));

            $count = count($this->retired);
            $graph->addDataSet(array($count / $purchaseDeviceCount));
            $percentValueMarker4 = "N  *p0* ({$count})";
            $graph->addColors(array("FF7400"));

            $graph->setDataRange(0, 1);
            $graph->setBarScale(50, 10);
            $graph->setLegendPosition("t");
            $graph->setLegend(array(
                                   "Keep",
                                   "Replace",
                                   "Flagged",
                                   "Retire",
                              ));
            $graph->setTitle("Optimized Fleet Summary");
            $graph->addValueMarkers($percentValueMarker1, "000000", "0", "-1", "11");
            $graph->addValueMarkers($percentValueMarker2, "000000", "1", "-1", "11");
            $graph->addValueMarkers($percentValueMarker3, "000000", "2", "-1", "11");
            $graph->addValueMarkers($percentValueMarker4, "000000", "3", "-1", "11");
            // Graph3
            $this->_graphs [] = $graph->getUrl();

            /**
             * -- Hardware Utilization and Capacity Percent version
             */
            $percentage = ($proposal->getPageCounts()->Total->Combined->Monthly / $proposal->getMaximumMonthlyPrintVolume());
            $percentage = 0.42;
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
            $barGraph->addColors(array(
                                      "000000"
                                 ));
            $barGraph->addDataSet(array(
                                       0
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
                                      "Your Estimated Optimized Monthly Usage (% of Capacity)",
                                      "Optimal Monthly Fleet Usage Range"
                                 ));
            $barGraph->addAxisRange(0, 0, $highest);
            $barGraph->setDataRange(0, $highest);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");

            $dotProperties = '@d,AB12AA,0,.5:' . number_format($percentage, 2) . ',30|';
            $dotProperties .= '@t' . number_format($percentage * 100, 2) . '%,AB12AA,0,1:' . number_format($percentage - .03, 2) . ',10|';
            $percentage = ($proposal->getPageCounts()->Total->Combined->Monthly / $proposal->calculateMaximumMonthlyPrintVolumeWithReplacements());
            $dotProperties .= '@d,000000,0,.5:' . number_format($percentage, 2) . ',20|';
            $dotProperties .= '@t' . number_format($percentage * 100, 2) . '%,000000,0,-2.5:' . number_format($percentage - .03, 2) . ',10';
            $barGraph->setProperty('chm', $dotProperties);

            $barGraph->addColors(array("0194D2"));
            $barGraph->setProperty('chxs', '0N*sz0*');
            // Graph4
            $this->_graphs [] = $barGraph->getUrl();


            /**
             * -- AverageMonthlyPagesPerEmployeeBarGraph
             */
            $employeeCount            = $proposal->report->getClient()->employeeCount;
            $averageEmployeePerDevice = 4.4;

            $devicesPerEmployee          = round($employeeCount / ($this->getDeviceCount() + count($this->retired) + count($this->leased) + count($this->excluded)), 2);
            $devicesPerEmployeeOptimized = round($employeeCount / ($this->getDeviceCount() + count($this->leased) + count($this->excluded)), 2);
            $highest                     = ($devicesPerEmployee > $averageEmployeePerDevice) ? $devicesPerEmployee : $averageEmployeePerDevice;
            $barGraph                    = new gchart\gBarChart(200, 300);
            $barGraph->setTitle("Employees per device");
            $barGraph->setVisibleAxes(array('y'));
            $barGraph->addDataSet(array($devicesPerEmployee));
            $barGraph->addColors(array("E21736"));
            $barGraph->addDataSet(array($averageEmployeePerDevice));
            $barGraph->addColors(array("0194D2"));
            $barGraph->addDataSet(array($devicesPerEmployeeOptimized));
            $barGraph->addColors(array("EF6B18"));

            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->setLegend(array(
                                      "Current",
                                      "Average",
                                      "Optimized"
                                 ));
            $numberFormat = "N";
            $barGraph->addValueMarkers($numberFormat, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberFormat, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberFormat, "000000", "2", "-1", "11");
            // Graph5
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- Printing List JIT Compatible
             */

            $jitCompatible          = $this->jitCompatibleCount;
            $nonJitCompatible       = count($proposal->getPurchasedDevices()) - $this->jitCompatibleCount;
            $optimizedJitCompatible = $this->jitCompatibleCount + $this->replacementJitCompatibleCount;
            $highest                = ($jitCompatible > $nonJitCompatible) ? $jitCompatible : $nonJitCompatible;
            $highest                = ($highest > $optimizedJitCompatible) ? $highest : $optimizedJitCompatible;
            $barGraph               = new gchart\gBarChart(200, 300);
            $barGraph->setTitle(" JIT compatibility");
            $barGraph->setVisibleAxes(array('y'));
            $barGraph->addDataSet(array($jitCompatible));
            $barGraph->addColors(array("E21736"));
            $barGraph->addDataSet(array($nonJitCompatible));
            $barGraph->addColors(array("0194D2"));
            $barGraph->addDataSet(array($optimizedJitCompatible));
            $barGraph->addColors(array("EF6B18"));

            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->setLegend(array(
                                      "JIT compatible",
                                      "Non JIT compatible",
                                      "Optimized",
                                 ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");

            // Graph6
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- LeasedVsPurchasedBarGraph
             */
            $highest  = ($proposal->getLeasedDeviceCount() > $proposal->getPurchasedDeviceCount()) ? $proposal->getLeasedDeviceCount() : $proposal->getPurchasedDeviceCount();

            $barGraph = new gchart\gBarChart(200, 300);
            $barGraph->setTitle('Leased / purchased devices ');
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array($proposal->getLeasedDeviceCount()));
            $barGraph->addColors(array("E21736"));
            $barGraph->addDataSet(array($proposal->getPurchasedDeviceCount()));
            $barGraph->addColors(array("0194D2"));
            $barGraph->addDataSet(array($proposal->getPurchasedDeviceCount() - count($this->retired)));
            $barGraph->addColors(array("EF6B18"));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->setLegend(array(
                                      "Leased",
                                      "Purchased",
                                      "Optimized"
                                 ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            // Graph7
            $this->_graphs [] = $barGraph->getUrl();
            /**
             * -- Categories of technology features
             */
            $barGraph = new gchart\gBarChart(375, 300);
            $barGraph->setTitle(" Green Features");
            $barGraph->setVisibleAxes(array('y'));
            $barGraph->addDataSet(array($this->deviceCategories["copy"] / $purchaseDeviceCount));
            $barGraph->addColors(array("E21736"));
            $barGraph->addDataSet(array($this->deviceCategories["duplex"] / $purchaseDeviceCount));
            $barGraph->addColors(array("A21736"));
            $barGraph->addDataSet(array($this->deviceCategories["color"] / $purchaseDeviceCount));
            $barGraph->addColors(array("6277A6"));
            $barGraph->setDataRange(0, 1);
            $barGraph->setBarScale(100, 10);
            $barGraph->setLegendPosition("b");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      "Copy Capable ",
                                      "Duplex Capable",
                                      "Color Capable"
                                 ));
            $barGraph->addValueMarkers($percentValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($percentValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($percentValueMarker, "000000", "2", "-1", "11");

            // Graph8
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- Age vertical bar chart
             */
            $highest  = ($this->deviceAges[0] > $this->deviceAges[2]) ? $this->deviceAges[0] : $this->deviceAges[2];
            $highest  = ($this->deviceAges[4] > $highest) ? $this->deviceAges[4] : $highest;
            $highest  = ($this->deviceAges[8] > $highest) ? $this->deviceAges[8] : $highest;
            $barGraph = new gchart\gBarChart(375, 250);
            $barGraph->setTitle("Age of printing devices");
            $barGraph->setVisibleAxes(array('y'));

            $colors = array("E21736", "A21746", "12AB36", "A2CB36");
            foreach ($colors as $color)
            {
                $barGraph->addColors(array($color));
            }
            foreach (array_reverse($this->deviceAges) as $deviceCount)
            {
                $barGraph->addDataSet(array($deviceCount));
            }
            $barGraph->setLegend(array_reverse(Proposalgen_Model_Optimization_Abstract::$ageRanks));
            $barGraph->setLegendPosition("b");
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(65, 10);
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");

            // Graph9
            $this->_graphs [] = $barGraph->getUrl();
            // Graph10
            $this->_graphs [] = $proposalGraphs[3];
            // Graph11
            $this->_graphs [] = $proposalGraphs[7];
            // Graph12
            $this->_graphs [] = $proposalGraphs[8];
        }

        return $this->_graphs;
    }
}