<?php

$insufficientData               = $this->getDevices()->allDevicesWithShortMonitorInterval->getCount() + $this->getDevices()->unmappedDeviceInstances->getCount() + $this->getDevices()->excludedDeviceInstances->getCount();
$managedByThirdPartyDeviceCount = $this->getDevices()->managedByThirdPartyDeviceInstances->getCount();
$isManagedDeviceCount           = $this->getDevices()->isManagedDeviceInstances->getCount();
$unmanagedDeviceCount           = $this->getDevices()->unmanagedDeviceInstances->getCount();
$highest  = max($isManagedDeviceCount, $unmanagedDeviceCount, $insufficientData, $managedByThirdPartyDeviceCount);

$MyData = $factory->newData();
$MyData->addPoints([$isManagedDeviceCount], "Managed/on " . My_Brand::$jit);
$MyData->addPoints([$unmanagedDeviceCount], "Manageable");
$MyData->addPoints([$managedByThirdPartyDeviceCount], "Future review");
if ($insufficientData) $MyData->addPoints([$insufficientData], "Insufficient data");

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$MyData->setPalette("Managed/on " . My_Brand::$jit, $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphManagedDeviceColor));
$MyData->setPalette("Manageable", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphManageableDeviceColor));
$MyData->setPalette("Future review", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphFutureReviewDeviceColor));
if ($insufficientData) $MyData->setPalette("Insufficient data", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphExcludedDeviceColor));

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
