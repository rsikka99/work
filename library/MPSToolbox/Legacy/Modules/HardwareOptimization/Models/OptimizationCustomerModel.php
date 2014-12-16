<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use gchart;
use My_Brand;

/**
 * Class OptimizationCustomerModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 */
class OptimizationCustomerModel extends OptimizationAbstractModel
{
    const AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE = 200;
    const AVERAGE_MONTHLY_PAGES              = 1500;

    /**
     * A list of string to image resources to the graphs
     *
     * @var string []
     */
    protected $_graphs;

    /**
     * Gets the array of graphs
     *
     * @return array|string[]
     */
    public function getGraphs ()
    {
        if (!isset($this->_graphs))
        {
            $dealerBranding = My_Brand::getDealerBranding();
            $companyName   = mb_strimwidth($this->_hardwareOptimization->getClient()->companyName, 0, 23, "...");
            $purchaseDeviceCount = count($this->_optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances());

            // N = number, p = percent, 0 = number of decimal places.
            // p will take 0.1544 as a number and convert it to 15%
            $percentValueMarker = "N  *p0";
            $numberValueMarker  = "N *sz0";

            /**
             * -- AverageMonthlyPagesBarGraph
             */
            $averagePageCount = round($this->_optimization->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->_optimization->getDevices()->allIncludedDeviceInstances->getCount(), 0);
            $highest          = ($averagePageCount > self::AVERAGE_MONTHLY_PAGES) ? $averagePageCount : self::AVERAGE_MONTHLY_PAGES;
            $barGraph         = new gchart\gBarChart(175, 300);
            $barGraph->setTitle("Average Monthly Pages|per Networked Printer");
            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(
                $averagePageCount
            ));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphCustomerColor),
            ));
            $barGraph->addDataSet(array(
                self::AVERAGE_MONTHLY_PAGES
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphIndustryAverageColor),
            ));
            $barGraph->setLegend(array(
                $companyName,
                "Average"
            ));

            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[4]
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- AverageMonthlyPagesPerEmployeeBarGraph
             */
            $pagesPerEmployee = round($this->_optimization->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->_hardwareOptimization->getClient()->employeeCount);
            $highest          = (self::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE > $pagesPerEmployee) ? self::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE : $pagesPerEmployee;
            $barGraph         = new gchart\gBarChart(175, 300);
            $barGraph->setTitle("Average Monthly Pages|per Employee");
            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(
                $pagesPerEmployee
            ));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphCustomerColor),
            ));
            $barGraph->addDataSet(array(
                self::AVERAGE_MONTHLY_PAGES_PER_EMPLOYEE
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphIndustryAverageColor),
            ));
            $barGraph->setLegend(array(
                $companyName,
                "Average"
            ));
            $barGraph->setProperty('chxs', '0N*sz0*');
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            // Graphs[5]
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- LeasedVsPurchasedPageCountBarGraph
             */
            $highest  = ($this->_optimization->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() > $this->_optimization->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) ? $this->_optimization->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() : $this->_optimization->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
            $barGraph = new gchart\gBarChart(200, 300);
            $barGraph->setTitle("Leased vs Purchase|Page Counts");
            $barGraph->setVisibleAxes(array('y'));
            $barGraph->addDataSet(array(round($this->_optimization->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphLeasedDeviceColor),
            ));
            $barGraph->addDataSet(array(round($this->_optimization->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())));
            $barGraph->addAxisRange(0, 0, $highest * 1.20);
            $barGraph->setDataRange(0, $highest * 1.20);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphPurchasedDeviceColor),
            ));
            $barGraph->setLegend(array(
                "Leased devices",
                "Purchased devices"
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
            $graph->addColors(array(
                str_replace('#', '', $dealerBranding->graphKeepDeviceColor),
            ));

            $count = count($this->replaced);
            $graph->addDataSet(array($count / $purchaseDeviceCount));
            $percentValueMarker2 = "N  *p0* ({$count})";
            $graph->addColors(array(
                str_replace('#', '', $dealerBranding->graphReplacedDeviceColor),
            ));

            $count = count($this->flagged);
            $graph->addDataSet(array(count($this->flagged) / $purchaseDeviceCount));
            $percentValueMarker3 = "N  *p0* ({$count})";
            $graph->addColors(array(
                str_replace('#', '', $dealerBranding->graphDoNotRepairDeviceColor),
            ));

            $count = count($this->retired);
            $graph->addDataSet(array($count / $purchaseDeviceCount));
            $percentValueMarker4 = "N  *p0* ({$count})";
            $graph->addColors(array(
                str_replace('#', '', $dealerBranding->graphRetireDeviceColor),
            ));

            $graph->setDataRange(0, 1);
            $graph->setBarScale(50, 10);
            $graph->setLegendPosition("t");
            $graph->setLegend(array(
                "Keep",
                "Replace",
                "Do Not Repair (Replace when broken)",
                "Retire/Migrate (Low Page Volume)",
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
            $percentage = ($this->_optimization->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->_optimization->getMaximumMonthlyPrintVolume());
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
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphCurrentSituationColor),
            ));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphNewSituationColor),
            ));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphOptimalSituationColor),
            ));
            $barGraph->addColors(array(
                "FFFFFF"
            ));
            $barGraph->addColors(array(
                "FFFFFF"
            ));
            $barGraph->setLegend(array(
                "Your estimated monthly usage (% of capacity)",
                "Your estimated optimized monthly usage (% of capacity)",
                "Optimal monthly fleet usage (% of capacity)"
            ));
            $barGraph->addAxisRange(0, 0, $highest);
            $barGraph->setDataRange(0, $highest);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");

            $dotProperties = '@d,' . str_replace('#', '', $dealerBranding->graphCurrentSituationColor) . ',0,.5:' . number_format($percentage, 2) . ',30|';
            $dotProperties .= '@t' . number_format($percentage * 100, 2) . '%,' . str_replace('#', '', $dealerBranding->graphCurrentSituationColor) . ',0,1:' . number_format($percentage - .03, 2) . ',10|';
            $optimizedPercentage = ($this->_optimization->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() /
                                    $this->_optimization->calculateMaximumMonthlyPrintVolumeWithReplacements());
            $dotProperties .= '@d,' . str_replace('#', '', $dealerBranding->graphNewSituationColor) . ',0,.5:' . number_format($optimizedPercentage, 2) . ',20|';
            $dotProperties .= '@t' . number_format($optimizedPercentage * 100, 2) . '%,' . str_replace('#', '', $dealerBranding->graphNewSituationColor) . ',0,-2.5:' . number_format($optimizedPercentage - .03, 2) . ',10';
            $barGraph->setProperty('chm', $dotProperties);

            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphOptimalSituationColor),
            ));
            $barGraph->setProperty('chxs', '0N*sz0*');
            // Graph4
            $this->_graphs [] = $barGraph->getUrl();


            /**
             * -- AverageMonthlyPagesPerEmployeeBarGraph
             */
            $employeeCount            = $this->_hardwareOptimization->getClient()->employeeCount;
            $averageEmployeePerDevice = 4.4;

            $devicesPerEmployee          = round($employeeCount / ($this->_optimization->getDevices()->allIncludedDeviceInstances->getCount() + count($this->retired) + count($this->leased) + count($this->excluded)), 1);
            $devicesPerEmployeeOptimized = round($employeeCount / ($this->_optimization->getDevices()->allIncludedDeviceInstances->getCount() + count($this->leased) + count($this->excluded)), 1);
            $highest                     = ($devicesPerEmployee > $averageEmployeePerDevice) ? $devicesPerEmployee : $averageEmployeePerDevice;
            $barGraph                    = new gchart\gBarChart(200, 300);
            $barGraph->setTitle("Employees per Device");
            $barGraph->setVisibleAxes(array('y'));
            $barGraph->addDataSet(array($devicesPerEmployee));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphCurrentSituationColor),
            ));
            $barGraph->addDataSet(array($averageEmployeePerDevice));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphIndustryAverageColor),
            ));
            $barGraph->addDataSet(array($devicesPerEmployeeOptimized));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphNewSituationColor),
            ));

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
            $nonJitCompatible       = count($this->_optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances()) - $this->jitCompatibleCount;
            $optimizedJitCompatible = $this->jitCompatibleCount + $this->replacementJitCompatibleCount;
            $highest                = ($jitCompatible > $nonJitCompatible) ? $jitCompatible : $nonJitCompatible;
            $highest                = ($highest > $optimizedJitCompatible) ? $highest : $optimizedJitCompatible;
            $barGraph               = new gchart\gBarChart(200, 300);
            $barGraph->setTitle(" " . My_Brand::$jit . " Compatibility");
            $barGraph->setVisibleAxes(array('y'));
            $barGraph->addDataSet(array($jitCompatible));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphCurrentSituationColor),
            ));
            $barGraph->addDataSet(array($nonJitCompatible));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphNotCompatibleDeviceColor),
            ));
            $barGraph->addDataSet(array($optimizedJitCompatible));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphNewSituationColor),
            ));

            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->setLegend(array(
                My_Brand::$jit . " compatible",
                "Non " . My_Brand::$jit . " compatible",
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
            $highest = (count($this->_optimization->getDevices()->leasedDeviceInstances->getDeviceInstances()) > count($this->_optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances())) ? count($this->_optimization->getDevices()->leasedDeviceInstances->getDeviceInstances()) : count($this->_optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances());

            $barGraph = new gchart\gBarChart(200, 300);
            $barGraph->setTitle('Device Overview');
            $barGraph->setVisibleAxes(array(
                'y'
            ));
            $barGraph->addDataSet(array(count($this->_optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances())));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphCurrentSituationColor),
            ));
            $barGraph->addDataSet(array(count($this->_optimization->getDevices()->leasedDeviceInstances->getDeviceInstances())));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphLeasedDeviceColor),
            ));
            $barGraph->addDataSet(array(count($this->_optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances()) - count($this->retired)));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphNewSituationColor),
            ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->setLegend(array(
                "Purchased",
                "Leased",
                "Optimized Purchased"
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
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphCopyCapableDeviceColor),
            ));
            $barGraph->addDataSet(array($this->deviceCategories["current"]["duplex"] / $purchaseDeviceCount));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphDuplexCapableDeviceColor),
            ));
            $barGraph->addDataSet(array($this->deviceCategories["current"]["color"] / $purchaseDeviceCount));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphColorDeviceColor),
            ));
            $barGraph->setDataRange(0, 1);
            $barGraph->setBarScale(43, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                "0194D2"
            ));
            $barGraph->setLegend(array(
                "Copy-capable ",
                "Duplex-capable",
                "Color-capable"
            ));
            $barGraph->addValueMarkers($percentValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($percentValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($percentValueMarker, "000000", "2", "-1", "11");
            // Graph8
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- Categories of technology features
             */
            $barGraph = new gchart\gBarChart(215, 300);
            $barGraph->setTitle("Technology Features (Optimized)");
            $barGraph->setVisibleAxes(array('y'));
            $barGraph->addDataSet(array($this->deviceCategories["optimized"]["copy"] / $purchaseDeviceCount));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphCopyCapableDeviceColor),
            ));
            $barGraph->addDataSet(array($this->deviceCategories["optimized"]["duplex"] / $purchaseDeviceCount));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphDuplexCapableDeviceColor),
            ));
            $barGraph->addDataSet(array($this->deviceCategories["optimized"]["color"] / $purchaseDeviceCount));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphColorDeviceColor),
            ));
            $barGraph->setDataRange(0, 1);
            $barGraph->setBarScale(43, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                "0194D2"
            ));
            $barGraph->setLegend(array(
                "Copy-capable ",
                "Duplex-capable",
                "Color-capable"
            ));
            $barGraph->addValueMarkers($percentValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($percentValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($percentValueMarker, "000000", "2", "-1", "11");
            // Graph9
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- Age vertical bar chart
             */
            $highest          = ($this->deviceAges[0] > $this->deviceAges[2]) ? $this->deviceAges[0] : $this->deviceAges[2];
            $highest          = ($this->deviceAges[4] > $highest) ? $this->deviceAges[4] : $highest;
            $highest          = ($this->deviceAges[8] > $highest) ? $this->deviceAges[8] : $highest;
            $highestOptimized = ($this->deviceAgesOptimized[0] > $this->deviceAgesOptimized[2]) ? $this->deviceAgesOptimized[0] : $this->deviceAgesOptimized[2];
            $highestOptimized = ($this->deviceAgesOptimized[4] > $highestOptimized) ? $this->deviceAgesOptimized[4] : $highestOptimized;
            $highestOptimized = ($this->deviceAgesOptimized[8] > $highestOptimized) ? $this->deviceAgesOptimized[8] : $highestOptimized;
            $highest          = ($highest > $highestOptimized) ? $highest : $highestOptimized;
            $barGraph         = new gchart\gBarChart(225, 325);
            $barGraph->setTitle("Age of Devices");
            $barGraph->setVisibleAxes(array('y'));

            $colors = array(
                str_replace('#', '', $dealerBranding->graphAgeOfDevices1),
                str_replace('#', '', $dealerBranding->graphAgeOfDevices2),
                str_replace('#', '', $dealerBranding->graphAgeOfDevices3),
                str_replace('#', '', $dealerBranding->graphAgeOfDevices4),
            );
            foreach ($colors as $color)
            {
                $barGraph->addColors(array($color));
            }

            foreach (array_reverse($this->deviceAges) as $deviceCount)
            {
                $barGraph->addDataSet(array($deviceCount));
            }
            $barGraph->setLegend(array_reverse(OptimizationAbstractModel::$ageRanks));
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
            $barGraph = new gchart\gBarChart(225, 325);
            $barGraph->setTitle("Age of Optimized Devices");
            $barGraph->setVisibleAxes(array('y'));

            $colors = array(
                str_replace('#', '', $dealerBranding->graphAgeOfDevices1),
                str_replace('#', '', $dealerBranding->graphAgeOfDevices2),
                str_replace('#', '', $dealerBranding->graphAgeOfDevices3),
                str_replace('#', '', $dealerBranding->graphAgeOfDevices4),
            );
            foreach ($colors as $color)
            {
                $barGraph->addColors(array($color));
            }

            foreach (array_reverse($this->deviceAgesOptimized) as $deviceCount)
            {
                $barGraph->addDataSet(array($deviceCount));
            }
            $barGraph->setLegend(array_reverse(OptimizationAbstractModel::$ageRanks));
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
             * -- Unique Supply Types
             */
            // Graph12

            $uniqueSupplyTypes = $this->getUniqueTonerList($this->getUniquePurchasedMasterDevices($this->_optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances()));
            $highest           = $this->getMaximumSupplyCount($this->getUniquePurchasedMasterDevices($this->_optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances()));
            $diamond           = count($uniqueSupplyTypes) / $highest;

            $targetUniqueness = $highest * 0.15;
            $barGraph         = new gchart\gStackedBarChart(600, 160);
            $barGraph->setHorizontal(true);
            $barGraph->setVisibleAxes(array('x'));
            $barGraph->setTitle("Unique Supply Types");

            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphOptimalSituationColor),
            ));
            $barGraph->addDataSet(array(0));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphCurrentSituationColor),
            ));
            $barGraph->addDataSet(array(0));
            $barGraph->addColors(array(
                "FFFFFF"
            ));
            $barGraph->addDataSet(array($targetUniqueness));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphOptimalSituationColor),
            ));
            $barGraph->addDataSet(array($targetUniqueness));

            $barGraph->setLegend(array(
                "Ideal supply uniqueness",
                "Your supply uniqueness"
            ));
            //Ticksize is used to scale the number of ticks on the x axis to never go above 21
            $tickSize = (int)($highest / 20 + 1);
            $barGraph->addAxisRange(0, 0, $highest, $tickSize);
            $barGraph->setDataRange(0, $highest);
            $barGraph->setBarScale(40, 5);
            $barGraph->setLegendPosition("t");

            $dotProperties = '@d,' . str_replace('#', '', $dealerBranding->graphCurrentSituationColor) . ',0,.5:' . number_format($diamond, 7) . ',30|';
            $dotProperties .= '@t' . count($uniqueSupplyTypes) . ',000000,0,-1:' . number_format($diamond - 0.012, 7) . ',10';
            $barGraph->setProperty('chm', $dotProperties);
            $barGraph->setProperty('chxs', '0N*sz0*');
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- Unique Supply Types With Replacements
             */
            $uniqueSupplyTypes = $this->getUniqueTonerList($this->getUniquePurchasedMasterDevices($this->getAllMasterDevicesWithReplacements()));
            $highest           = $this->getMaximumSupplyCount($this->getUniquePurchasedMasterDevices($this->getAllMasterDevicesWithReplacements()));
            $graphMaximum      = $highest + $highest * .15;
            $graphMaximum      = ($graphMaximum > 0) ? $graphMaximum : 1;
            $diamond           = count($uniqueSupplyTypes) / $graphMaximum;
            $targetUniqueness  = $highest * 0.15;

            $barGraph = new gchart\gStackedBarChart(600, 160);
            $barGraph->setTitle("Unique Supply Types (Optimized)");
            $barGraph->setHorizontal(true);
            $barGraph->setVisibleAxes(array('x'));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphOptimalSituationColor),
            ));
            $barGraph->addDataSet(array(0));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphNewSituationColor),
            ));
            $barGraph->addDataSet(array(0));
            $barGraph->addColors(array(
                "FFFFFF"
            ));
            $barGraph->addDataSet(array($targetUniqueness));
            $barGraph->addColors(array(
                str_replace('#', '', $dealerBranding->graphOptimalSituationColor),
            ));
            $barGraph->addDataSet(array($targetUniqueness));
            $barGraph->setLegend(array(
                "Ideal supply uniqueness",
                "Your optimized supply uniqueness"
            ));
            //Ticksize is used to scale the number of ticks on the x axis to never go above 21
            $tickSize = (int)($graphMaximum / 20 + 1);
            $barGraph->addAxisRange(0, 0, $graphMaximum, $tickSize);
            $barGraph->setDataRange(0, $graphMaximum);
            $barGraph->setBarScale(40, 5);
            $barGraph->setLegendPosition("t");

            $dotProperties = '@d,' . str_replace('#', '', $dealerBranding->graphNewSituationColor) . ',0,.5:' . number_format($diamond, 7) . ',30|';
            $dotProperties .= '@t' . count($uniqueSupplyTypes) . ',000000,0,-1:' . number_format($diamond - 0.012, 7) . ',10';
            $barGraph->setProperty('chm', $dotProperties);
            $barGraph->setProperty('chxs', $numberValueMarker);
            // Graph13
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- Color Capable Devices Graph
             */
            $colorPercentage = 0;
            if ($this->_optimization->getDevices()->allIncludedDeviceInstances->getCount())
            {
                $colorPercentage = round((($this->_optimization->getNumberOfColorCapableDevices() / $this->_optimization->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
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
                str_replace('#', '', $dealerBranding->graphColorDeviceColor),
                str_replace('#', '', $dealerBranding->graphMonoDeviceColor),
            ));
            $colorCapableGraph->setLegendPosition("bv");
            // Graphs[7]
            $this->_graphs [] = $colorCapableGraph->getUrl();
            /**
             * -- Color Capable Devices Graph With Replacements
             */
            $colorPercentage = 0;
            if ($this->_optimization->getDevices()->allIncludedDeviceInstances->getCount())
            {
                $colorPercentage = round((($this->_optimization->getNumberOfColorCapableDevicesWithReplacements() / $this->_optimization->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
            }

            $notColorPercentage = 100 - $colorPercentage;
            $colorCapableGraph  = new gchart\gPie3DChart(305, 210);
            $colorCapableGraph->setTitle("Color-Capable Printing Devices Optimized");
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
                str_replace('#', '', $dealerBranding->graphColorDeviceColor),
                str_replace('#', '', $dealerBranding->graphMonoDeviceColor),
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