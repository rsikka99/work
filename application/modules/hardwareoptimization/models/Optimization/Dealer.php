<?php
class Hardwareoptimization_Model_Optimization_Dealer extends
    Hardwareoptimization_Model_Optimization_Abstract
{
    /**
     * a list of string to image resources to the graphs
     *
     * @var string []
     */
    protected $_graphs;

    public function getGraphs ()
    {
        if (!isset($this->graphs))
        {
            $numberValueMarker = "N *sz0";

            /**
             * -- Optimization Chart Summary
             */
            $highest  = (count($this->replaced) > count($this->kept)) ? count($this->replaced) : count($this->kept);
            $highest  = (count($this->flagged) > $highest) ? count($this->flagged) : $highest;
            $highest  = (count($this->retired) > $highest) ? count($this->retired) : $highest;
            $barGraph = new gchart\gBarChart(375, 250);
            $barGraph->setTitle("Optimization Fleet Summary");
            $barGraph->setVisibleAxes(array('y'));
            $colors = array("009999", "1240AB", "FFAA00", "FF7400");
            foreach ($colors as $color)
            {
                $barGraph->addColors(array($color));
            }
            $barGraph->addDataSet(array(count($this->kept)));
            $barGraph->addDataSet(array(count($this->replaced)));
            $barGraph->addDataSet(array(count($this->flagged)));
            $barGraph->addDataSet(array(count($this->retired)));

            $barGraph->setLegend(array("Keep", "Replaced", "Flagged", "Retired"));
            $barGraph->setLegendPosition("t");
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(65, 10);
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "3", "-1", "11");
            $this->_graphs [] = $barGraph->getUrl();

            /**
             * -- Other devices that are in the fleet
             */
            $highest  = (count($this->excess) > count($this->excluded)) ? count($this->excess) : count($this->excluded);
            $highest  = (count($this->leasedDevices) > $highest) ? count($this->leasedDevices) : $highest;
            $barGraph = new gchart\gBarChart(325, 250);
            $barGraph->setTitle("Other Devices");
            $barGraph->setVisibleAxes(array('y'));
            $colors = array("009999", "1240AB", "FFAA00");
            foreach ($colors as $color)
            {
                $barGraph->addColors(array($color));
            }
            $barGraph->addDataSet(array(count($this->excess)));
            $barGraph->addDataSet(array(count($this->excluded)));
            $barGraph->addDataSet(array(count($this->leasedDevices)));

            $barGraph->setLegend(array("Excess", "Excluded", "Leased"));
            $barGraph->setLegendPosition("t");
            $barGraph->addAxisRange(0, 0, $highest * 1.1);
            $barGraph->setDataRange(0, $highest * 1.1);
            $barGraph->setBarScale(65, 10);
            $barGraph->addValueMarkers($numberValueMarker, "000000", "0", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "1", "-1", "11");
            $barGraph->addValueMarkers($numberValueMarker, "000000", "2", "-1", "11");
            $this->_graphs [] = $barGraph->getUrl();

        }

        return $this->_graphs;
    }
}