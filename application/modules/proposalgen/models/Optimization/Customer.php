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
            $numberValueMarker = "N *sz0";

            // Hardware Optimization Device Summary Listing (before hardware optimization
            $highest = $this->actionKeepCount;
            $graph   = new gchart\gBarChart(520, 320, "g", "h");
            $graph->setVisibleAxes(array(
                                        'x'
                                   ));
            $graph->addDataSet(array(
                                    $this->actionKeepCount
                               ));
            $graph->addColors(array(
                                   "8064a2"
                              ));
            $graph->setVisibleAxes(array(
                                        'x'
                                   ));
            $graph->addDataSet(array(
                                    $this->actionReplaceCount
                               ));
            $graph->addColors(array(
                                   "9bbb59"
                              ));
            $graph->setVisibleAxes(array(
                                        'x'
                                   ));
            $graph->addDataSet(array(
                                    $this->actionRetireCount
                               ));
            $graph->addColors(array(
                                   "c0504d"
                              ));
            $graph->setVisibleAxes(array(
                                        'x'
                                   ));
            $graph->addDataSet(array(
                                    0
                               ));
            $graph->addColors(array(
                                   "4f81bd"
                              ));
            $graph->addAxisRange(0, 0, $highest * 1.3);
            $graph->setDataRange(0, $highest * 1.3);
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
            // Graphs[15]
            $this->_graphs [] = $graph->getUrl();


            // Hardware Optimization Device Summary Listing

            $highest = $this->actionKeepCount;
            $graph   = new gchart\gBarChart(520, 320, "g", "h");
            $graph->setVisibleAxes(array(
                                        'x'
                                   ));
            $graph->addDataSet(array(
                                    count($this->kept)
                               ));
            $graph->addColors(array(
                                   "8064a2"
                              ));
            $graph->setVisibleAxes(array(
                                        'x'
                                   ));
            $graph->addDataSet(array(
                                    count($this->flagged)
                               ));
            $graph->addColors(array(
                                   "9bbb59"
                              ));
            $graph->setVisibleAxes(array(
                                        'x'
                                   ));
            $graph->addDataSet(array(
                                    count($this->retired)
                               ));
            $graph->addColors(array(
                                   "c0504d"
                              ));
            $graph->setVisibleAxes(array(
                                        'x'
                                   ));
            $graph->addDataSet(array(
                                    count($this->replaced)
                               ));
            $graph->addColors(array(
                                   "4f81bd"
                              ));
            $graph->addAxisRange(0, 0, $highest * 1.3);
            $graph->setDataRange(0, $highest * 1.3);
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
            // Graphs[16]
            $this->_graphs [] = $graph->getUrl();
        }

        return $this->_graphs;
    }
}