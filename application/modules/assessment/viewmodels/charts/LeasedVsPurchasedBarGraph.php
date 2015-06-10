<?php

$highest = ($this->getDevices()->leasedDeviceInstances->getCount() > $this->getDevices()->purchasedDeviceInstances->getCount()) ? $this->getDevices()->leasedDeviceInstances->getCount() : $this->getDevices()->purchasedDeviceInstances->getCount();
$MyData  = $factory->newData();
$MyData->addPoints([$this->getDevices()->leasedDeviceInstances->getCount()], "Future Consideration");
$MyData->addPoints([$this->getDevices()->purchasedDeviceInstances->getCount()], "Purchased devices");

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$MyData->setAxisDisplay(0,AXIS_FORMAT_METRIC);
$MyData->setAxisName(0,'Number of devices');

$leasedRGB             = $hexToRGBConverter->hexToRgb($dealerBranding->graphLeasedDeviceColor);
$purchasedRGB          = $hexToRGBConverter->hexToRgb($dealerBranding->graphPurchasedDeviceColor);
$leasedColorSetting    = ['R' => $leasedRGB['r'], 'G' => $leasedRGB['g'], 'B' => $leasedRGB['b']];
$purchasedColorSetting = ['R' => $purchasedRGB['r'], 'G' => $purchasedRGB['g'], 'B' => $purchasedRGB['b']];
$MyData->setPalette("Future Consideration", $leasedColorSetting);
$MyData->setPalette("Purchased devices", $purchasedColorSetting);

$myPicture            = $factory->newImage(265, 265, $MyData);
$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(60, 10, 200, 200);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

/* Write the chart legend - this sets the x/y position */
$myPicture->drawLegend(40, 220, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
$myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);

$this->pImageGraphs[] = $myPicture;