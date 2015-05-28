<?php

$isManagedDeviceCount     = $this->getDevices()->isManagedDeviceInstances->getCount();
$notCompatibleDeviceCount = $this->getDevices()->notCompatibleDeviceInstances->getCount();
$jitCompatibleDeviceCount = $this->getDevices()->compatibleDeviceInstances->getCount();

$highest  = max($isManagedDeviceCount, $notCompatibleDeviceCount, $jitCompatibleDeviceCount);

$MyData = $factory->newData();
$MyData->addPoints([$isManagedDeviceCount], "Managed/on " . My_Brand::$jit);
$MyData->addPoints([$notCompatibleDeviceCount], "Not " . My_Brand::$jit . " compatible");
$MyData->addPoints([$jitCompatibleDeviceCount], My_Brand::$jit . " compatible");

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$MyData->setPalette("Managed/on " . My_Brand::$jit, $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphManagedDeviceColor));
$MyData->setPalette("Not " . My_Brand::$jit . " compatible", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphNotCompatibleDeviceColor));
$MyData->setPalette(My_Brand::$jit . " compatible", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphJitCompatibleDeviceColor));

$myPicture            = $factory->newImage(280, 230, $MyData);
$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(60, 40, 280-20, 230-60);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$myPicture->drawText(10, 20, "Total Printers on Network", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0]);

/* Write the chart legend - this sets the x/y position */
$myPicture->drawLegend(50, 230-50, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
$myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);
