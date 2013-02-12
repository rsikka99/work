<?php
class Proposalgen_Model_Optimization_Customer extends
    Proposalgen_Model_Optimization_Abstract
{
    /**
     * a list of string to image resources to the graphs
     *
     * @var string []
     */
    protected  $_graphs;

    public function getGraphs ()
    {
        if (!isset($this->graphs))
        {
            $proposalGraphs = $this->proposal->getGraphs();
            $purchaseDeviceCount = count($this->proposal->getPurchasedDevices());

            // N = number, p = percent, 0 = number of decimal places.
            // p will take 0.1544 as a number and convert it to 15%
            $numberValueMarker = "N*p0";
            /* Hardware Optimization Device Summary Listing (before hardware optimization */
            $graph   = new gchart\gBarChart(520, 320, "g", "h");
            $graph->setVisibleAxes(array('x'));
            // Amount of keep devices
            $graph->addDataSet(array($this->actionKeepCount / $purchaseDeviceCount));
            $graph->addColors(array("8064a2"));
            // Amount of replace devices
            $graph->addDataSet(array($this->actionReplaceCount / $purchaseDeviceCount));
            $graph->addColors(array("9bbb59"));
            // Amount of retire devices
            $graph->addDataSet(array($this->actionRetireCount / $purchaseDeviceCount));
            $graph->addColors(array("c0504d"));
            // Set the replaced amount to 0
            $graph->addDataSet(array(0));
            $graph->setDataRange(0, 1);
            $graph->setBarScale(50, 10);
            $graph->setLegendPosition("r");
            $graph->setLegend(array(
                                   "Keep Devices",
                                   "Flagged Devices",
                                   "Retired Devices",
                                   "Replaced Devices",
                              ));
            $graph->setTitle("Current Fleet Summary");
            $graph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $graph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $graph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $graph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            $this->_graphs [] = $graph->getUrl();


            // Hardware Optimization Device Summary Listing
            $highest = ($this->actionKeepCount / $purchaseDeviceCount);
            $graph   = new gchart\gBarChart(520, 320, "g", "h");
            $graph->setVisibleAxes(array('x'));
            $graph->addDataSet(array((count($this->kept) / $purchaseDeviceCount)));
            $graph->addColors(array("8064a2"));
            $graph->addDataSet(array(count($this->flagged) / $purchaseDeviceCount));
            $graph->addColors(array("9bbb59"));
            $graph->addDataSet(array(count($this->retired) / $purchaseDeviceCount));
            $graph->addColors(array("c0504d"));
            $graph->addDataSet(array(count($this->replaced) / $purchaseDeviceCount));
            $graph->addColors(array("4f81bd"));
            $graph->setDataRange(0, 1);
            $graph->setBarScale(50, 10);
            $graph->setLegendPosition("r");
            $graph->setLegend(array(
                                   "Keep Devices",
                                   "Flagged Devices",
                                   "Retired Devices",
                                   "Replaced Devices",
                              ));
            $graph->setTitle("Optimized Fleet Summary");
            $graph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $graph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $graph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $graph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            $this->_graphs [] = $graph->getUrl();

            $this->_graphs [] = $proposalGraphs[1];
            $this->_graphs [] = $proposalGraphs[2];
            $this->_graphs [] = $proposalGraphs[3];
            $this->_graphs [] = $proposalGraphs[4];
            $this->_graphs [] = $proposalGraphs[5];
            $this->_graphs [] = $proposalGraphs[6];


            /**
             * -- Printing List JIT Compatible
             */
            $jitCompatible = $this->jitCompatibleCount;
            $nonJitCompatible = count($this->proposal->getPurchasedDevices()) - $this->jitCompatibleCount;
            $highest            = ($jitCompatible > $nonJitCompatible) ? $jitCompatible : $nonJitCompatible;
            $barGraph           = new gchart\gBarChart(200, 300);
            $barGraph->setTitle(" JIT Compatibility");
            $barGraph->setVisibleAxes(array(
                                           'y'
                                      ));
            $barGraph->addDataSet(array(
                                       $jitCompatible
                                  ));
            $barGraph->addColors(array(
                                      "E21736"
                                 ));
            $barGraph->addDataSet(array(
                                       $nonJitCompatible
                                  ));
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 10);
            $barGraph->setLegendPosition("bv");
            $barGraph->addColors(array(
                                      "0194D2"
                                 ));
            $barGraph->setLegend(array(
                                      "JIT Compatible ",
                                      "Non JIT Compatible "
                                 ));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $this->_graphs [] = $barGraph->getUrl();

            $this->_graphs [] = $proposalGraphs[7];
            $this->_graphs [] = $proposalGraphs[8];

            // Placeholder for graph
            $this->_graphs [] = $proposalGraphs[0];



            /**
             * -- Age vertical bar chart
             */
//            $jitCompatible = $this->jitCompatibleCount;
//            $nonJitCompatible = count($this->proposal->getPurchasedDevices()) - $this->jitCompatibleCount;
            $numberValueMarker = "N *sz0";
            $highest            = ($jitCompatible > $nonJitCompatible) ? $jitCompatible : $nonJitCompatible;
            $barGraph           = new gchart\gBarChart(400, 300);
            $barGraph->setTitle("Age of printing devices");
            $barGraph->setVisibleAxes(array('x'));

            $barGraph->addDataSet(array(30,44,49,433));
            $barGraph->addColors(array("E21736","A21746","12AB36","A2CB36"));

            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(40, 20);
            $barGraph->addColors(array("0194D2"));
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            $barGraph->addAxisLabel(0,array("100","3","69","86"));
            $this->_graphs [] = $barGraph->getUrl();
            $this->_graphs [] = $proposalGraphs[0];

//            $this->_graphs [] = ;
//            $this->_graphs [] = ;
//            $this->_graphs [] = ;

        }

        return $this->_graphs;
    }
}