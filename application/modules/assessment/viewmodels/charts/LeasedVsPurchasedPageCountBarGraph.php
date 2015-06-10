<?php

$value1 = $this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
$value2 = $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
$value3 = $this->getPercentageOfInkjetPrintVolume()/100 * ($value1+$value2);

$highest = max($value1,$value2);
$MyData  = $factory->newData();
$MyData->addPoints([round($value1)], "Future Consideration devices");
$MyData->addPoints([round($value2)], 'Purchased devices');
$MyData->addPoints([round($value3)], 'Non-network devices');
$MyData->setAxisDisplay(0,AXIS_FORMAT_METRIC);
$MyData->setAxisName(0,'Monthly page volume');

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$MyData->setPalette("Future Consideration devices", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphLeasedDeviceColor));
$MyData->setPalette("Purchased devices", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphPurchasedDeviceColor));
$MyData->setPalette("Non-network devices", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphIndustryAverageColor));

$myPicture            = $factory->newImage(265, 265, $MyData);
$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(60, 10, 200, 200);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);
/* Write the chart legend - this sets the x/y position */
$myPicture->drawLegend(40, 220, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
$myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);
