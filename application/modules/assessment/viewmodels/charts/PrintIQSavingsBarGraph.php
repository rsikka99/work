<?php

$MyData  = $factory->newData();
$highest = ($this->getPrintIQTotalCost() > $this->getTotalPurchasedAnnualCost() ? $this->getPrintIQTotalCost() : $this->getTotalPurchasedAnnualCost());

$MyData->addPoints([round($this->getTotalPurchasedAnnualCost()), round($this->getPrintIQTotalCost())], "Annual Printing Costs for Purchased Hardware");
$MyData->setAxisPosition(0, AXIS_POSITION_BOTTOM);
$MyData->setAxisName(0, "");
$MyData->addPoints(["Current", My_Brand::getDealerBranding()->mpsProgramName], "Costs");
$MyData->setSerieDescription("Costs", "Costs");
$MyData->setAxisDisplay(0, AXIS_FORMAT_CUSTOM, 'pCharts_formatDisplayCurrency');
$MyData->setAbscissa("Costs");

$myPicture = new \CpChart\Classes\pImage(650, 160, $MyData);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);

/* Turn off Antialiasing */
$myPicture->Antialias = false;

$myPicture->drawText(175, 35, "Annual Printing Costs for Purchased Hardware", ["FontSize" => 11, "R" => 0, "G" => 0, "B" => 0]);

$axisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.4], 1 => ["Min" => 0, "Max" => $highest * 1.4]];

$myPicture->setGraphArea(100, 70, 750, 175);
$myPicture->drawScale(["DrawSubTicks" => false, "Pos" => SCALE_POS_TOPBOTTOM, "Mode" => SCALE_MODE_MANUAL, "ManualScale" => $axisBoundaries, "TickR" => 127, "TickG" => 127, "TickB" => 127, "MinDivHeight" => 100, "OuterTickWidth" => 0, "InnerTickWidth" => 3, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127]);

// Set the colors of the graph bars
$negColor = $hexToRGBConverter->hexToRgb($dealerBranding->graphNegativeColor);
$posColor = $hexToRGBConverter->hexToRgb($dealerBranding->graphPositiveColor);
$palette  = ["0" => ['R' => $negColor['r'], 'G' => $negColor['g'], 'B' => $negColor['b']], ['R' => $posColor['r'], 'G' => $posColor['g'], 'B' => $posColor['b']],];

/* Draw the chart */
$myPicture->drawBarChart(["DisplayPos" => LABEL_POS_RIGHT, "DisplayValues" => true, "OverrideColors" => $palette, "Surrounding" => 0]);
