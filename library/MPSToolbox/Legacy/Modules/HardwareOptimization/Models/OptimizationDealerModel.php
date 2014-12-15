<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use gchart;
use My_Brand;

/**
 * Class OptimizationDealerModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 */
class OptimizationDealerModel extends OptimizationAbstractModel
{
    /**
     * a list of string to image resources to the graphs
     *
     * @var string []
     */
    protected $_graphs;

    /**
     * Gets the array of graphs
     *
     * @return array
     */
    public function getGraphs ()
    {
        if (!isset($this->graphs))
        {
            $dealerBranding = My_Brand::getDealerBranding();
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
            $colors = array(
                str_replace('#', '', $dealerBranding->graphKeepDeviceColor),
                str_replace('#', '', $dealerBranding->graphReplacedDeviceColor),
                str_replace('#', '', $dealerBranding->graphDoNotRepairDeviceColor),
                str_replace('#', '', $dealerBranding->graphRetireDeviceColor),
            );
            foreach ($colors as $color)
            {
                $barGraph->addColors(array($color));
            }
            $barGraph->addDataSet(array(count($this->kept)));
            $barGraph->addDataSet(array(count($this->replaced)));
            $barGraph->addDataSet(array(count($this->flagged)));
            $barGraph->addDataSet(array(count($this->retired)));

            $barGraph->setLegend(array("Keep", "Replace", "Do Not Repair (Replace when broken)", "Retire/Migrate (Low Page Volume)"));
            $barGraph->setLegendPosition("tv");
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
            $highest  = (count($this->leased) > $highest) ? count($this->leased) : $highest;
            $barGraph = new gchart\gBarChart(325, 250);
            $barGraph->setTitle("Other Devices");
            $barGraph->setVisibleAxes(array('y'));
            $colors = array(
                str_replace('#', '', $dealerBranding->graphReplacedDeviceColor),
                str_replace('#', '', $dealerBranding->graphExcludedDeviceColor),
                str_replace('#', '', $dealerBranding->graphLeasedDeviceColor),
            );
            foreach ($colors as $color)
            {
                $barGraph->addColors(array($color));
            }
            $barGraph->addDataSet(array(count($this->excess)));
            $barGraph->addDataSet(array(count($this->excluded)));
            $barGraph->addDataSet(array(count($this->leased)));

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