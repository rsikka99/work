<?php
class Proposalgen_Model_Optimization_Customer extends Proposalgen_Model_Optimization_Abstract
{
    /**
     * A list of string to image resources to the graphs
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
            $barGraph->setVisibleAxes( array('y') );
            $barGraph->addDataSet( array(round($pageCounts->Leased->Combined->Monthly)) );
            $barGraph->addColors( array("E21736") );
            $barGraph->addDataSet( array(round($pageCounts->Purchased->Combined->Monthly)) );
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
            $graph->addColors(array("E21736"));

            $count = count($this->replaced);
            $graph->addDataSet(array($count / $purchaseDeviceCount));
            $percentValueMarker2 = "N  *p0* ({$count})";
            $graph->addColors(array("0194D2"));

            $count = count($this->flagged);
            $graph->addDataSet(array(count($this->flagged) / $purchaseDeviceCount));
            $percentValueMarker3 = "N  *p0* ({$count})";
            $graph->addColors(array("EF6B18"));

            $count = count($this->retired);
            $graph->addDataSet(array($count / $purchaseDeviceCount));
            $percentValueMarker4 = "N  *p0* ({$count})";
            $graph->addColors(array("FFCF00"));

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
            $barGraph->setVisibleAxes(array('x'));
            $barGraph->addDataSet(array(0));
            $barGraph->addDataSet(array(0));
            $barGraph->addDataSet(array(0));
            $barGraph->addDataSet(array(30));
            $barGraph->addDataSet(array(0));
            $barGraph->addDataSet(array(20));
            $barGraph->addColors(array("E21736"));
            $barGraph->addColors(array("0194D2"));
            $barGraph->addColors(array("EF6B18"));
            $barGraph->addColors(array("FFFFFF"));
            $barGraph->addColors(array("FFFFFF"));
            $barGraph->setLegend(array(
                                      "Your Estimated Monthly Usage (% of Capacity)",
                                      "Your Estimated Optimized Monthly Usage (% of Capacity)",
                                      "Optimal Monthly Fleet Usage (% of Capacity)"
                                 ));
            $barGraph->addAxisRange(0, 0, $highest);
            $barGraph->setDataRange(0, $highest);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");

            $dotProperties = '@d,E21736,0,.5:' . number_format($percentage, 2) . ',30|';
            $dotProperties .= '@t' . number_format($percentage * 100, 2) . '%,E21736,0,1:' . number_format($percentage - .03, 2) . ',10|';
//            $percentage = ($proposal->getPageCounts()->Total->Combined->Monthly / $proposal->calculateMaximumMonthlyPrintVolumeWithReplacements());
            $dotProperties .= '@d,EF6B18,0,.5:' . number_format($percentage, 2) . ',20|';
            $dotProperties .= '@t' . number_format($percentage * 100, 2) . '%,EF6B18,0,-2.5:' . number_format($percentage - .03, 2) . ',10';
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

            $devicesPerEmployee          = round($employeeCount / ($this->getDeviceCount() + count($this->retired) + count($this->leased) + count($this->excluded)), 1);
            $devicesPerEmployeeOptimized = round($employeeCount / ($this->getDeviceCount() + count($this->leased) + count($this->excluded)), 1);
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
            $highest = ($proposal->getLeasedDeviceCount() > $proposal->getPurchasedDeviceCount()) ? $proposal->getLeasedDeviceCount() : $proposal->getPurchasedDeviceCount();

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
            $barGraph = new gchart\gBarChart(200, 300);
            $barGraph->setTitle("Technology Features");
            $barGraph->setVisibleAxes(array('y'));
            $barGraph->addDataSet(array($this->deviceCategories["current"]["copy"] / $purchaseDeviceCount));
            $barGraph->addColors(array("E21736"));
            $barGraph->addDataSet(array($this->deviceCategories["current"]["duplex"] / $purchaseDeviceCount));
            $barGraph->addColors(array("0194D2"));
            $barGraph->addDataSet(array($this->deviceCategories["current"]["color"] / $purchaseDeviceCount));
            $barGraph->addColors(array("EF6B18"));
            $barGraph->setDataRange(0, 1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
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
             * -- Categories of technology features
             */
            $barGraph = new gchart\gBarChart(200, 300);
            $barGraph->setTitle("Technology Features");
            $barGraph->setVisibleAxes(array('y'));
            $barGraph->addDataSet(array($this->deviceCategories["optimized"]["copy"] / $purchaseDeviceCount));
            $barGraph->addColors(array("E21736"));
            $barGraph->addDataSet(array($this->deviceCategories["optimized"]["duplex"] / $purchaseDeviceCount));
            $barGraph->addColors(array("0194D2"));
            $barGraph->addDataSet(array($this->deviceCategories["optimized"]["color"] / $purchaseDeviceCount));
            $barGraph->addColors(array("EF6B18"));
            $barGraph->setDataRange(0, 1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
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
            // Graph9
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- Age vertical bar chart
             */
            $highest  = ($this->deviceAges[0] > $this->deviceAges[2]) ? $this->deviceAges[0] : $this->deviceAges[2];
            $highest  = ($this->deviceAges[4] > $highest) ? $this->deviceAges[4] : $highest;
            $highest  = ($this->deviceAges[8] > $highest) ? $this->deviceAges[8] : $highest;
            $highestOptimized = ($this->deviceAgesOptimized[0] > $this->deviceAgesOptimized[2]) ? $this->deviceAgesOptimized[0] : $this->deviceAgesOptimized[2];
            $highestOptimized = ($this->deviceAgesOptimized[4] > $highestOptimized) ? $this->deviceAgesOptimized[4] : $highestOptimized;
            $highestOptimized = ($this->deviceAgesOptimized[8] > $highestOptimized) ? $this->deviceAgesOptimized[8] : $highestOptimized;
            $highest = ($highest > $highestOptimized) ? $highest : $highestOptimized;
            $barGraph = new gchart\gBarChart(225, 325);
            $barGraph->setTitle("Age of devices");
            $barGraph->setVisibleAxes(array('y'));

            $colors = array("E21736", "0194D2", "EF6B18", "FFCF00");
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
            $barGraph->setBarScale(40, 10);
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            // Graph10
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- Age vertical bar chart optimized
             */

            $barGraph         = new gchart\gBarChart(225, 325);
            $barGraph->setTitle("Age of optimized devices");
            $barGraph->setVisibleAxes(array('y'));

            $colors = array("E21736", "0194D2", "EF6B18", "FFCF00");
            foreach ($colors as $color)
            {
                $barGraph->addColors(array($color));
            }
            foreach (array_reverse($this->deviceAgesOptimized) as $deviceCount)
            {
                $barGraph->addDataSet(array($deviceCount));
            }
            $barGraph->setLegend(array_reverse(Proposalgen_Model_Optimization_Abstract::$ageRanks));
            $barGraph->setLegendPosition("b");
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            // Graph11
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- UniqueDevicesGraph
             */
            // Graph12
            $this->_graphs [] = $proposalGraphs[3];

            /**
             * -- UniqueDevicesGraph With Replacements
             */
            $uniqueModelArray = array();
            foreach ($proposal->getPurchasedDevices() as $device)
            {
                if ($device->getReplacementMasterDevice() instanceof Proposalgen_Model_MasterDevice)
                {
                    $replacementDevice = $device->getReplacementMasterDevice();
                    if (array_key_exists($replacementDevice->modelName, $uniqueModelArray))
                    {
                        $uniqueModelArray [$replacementDevice->modelName] += 1;
                    }
                    else
                    {
                        $uniqueModelArray [$replacementDevice->modelName] = 1;
                    }
                }
                else
                {
                    if (array_key_exists($device->getMasterDevice()->modelName, $uniqueModelArray))
                    {
                        $uniqueModelArray [$device->getMasterDevice()->modelName] += 1;
                    }
                    else
                    {
                        $uniqueModelArray [$device->getMasterDevice()->modelName] = 1;
                    }
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
            // Graph13
            $this->_graphs [] = $uniqueDevicesGraph->getUrl();

            /**
             * -- Color Capable Devices Graph
             */
            // Graph14
            $this->_graphs [] = $proposalGraphs[7];

            /**
             * -- Color Capable Devices Graph With Replacements
             */
            $colorPercentage = 0;
            if ($this->getDeviceCount())
            {
                $colorPercentage = round((($proposal->getNumberOfColorCapableDevicesWithReplacements() / $this->getDeviceCount()) * 100), 2);
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

            /**
             * -- Color Capable Devices Graph With Replacement
             */
            // Graph15
            $this->_graphs [] = $colorCapableGraph->getUrl();
        }

        return $this->_graphs;
    }
}