<?php

$value1 = round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly());
$value2 = round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() - $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly());
$highest  = max($value1,$value2);

$MyData = $factory->newData();
$MyData->addPoints([$value1], 'Color pages printed');
$MyData->addPoints([$value2], 'Black-and-white pages printed');

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$graphColorDeviceColor        = $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphColorDeviceColor);
$graphMonoDeviceColor = $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphMonoDeviceColor);
$MyData->setPalette('Color pages printed', $graphColorDeviceColor);
$MyData->setPalette('Black-and-white pages printed', $graphMonoDeviceColor);

$myPicture            = $factory->newImage(280, 210, $MyData);
$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(60, 50, 280-25, 210-55);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$myPicture->drawText(90, 30, "Color vs\nBlack/White Pages", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);

/* Write the chart legend - this sets the x/y position */
$myPicture->drawLegend(50, 210-40, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
$myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);
