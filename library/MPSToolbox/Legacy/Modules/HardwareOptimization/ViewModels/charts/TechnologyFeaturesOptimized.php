<?php

$purchaseDeviceCount = count($this->getDevices()->purchasedDeviceInstances->getDeviceInstances());

$value1=round(100 * $model->deviceCategories["optimized"]["copy"] / $purchaseDeviceCount);
$value2=round(100 * $model->deviceCategories["optimized"]["duplex"] / $purchaseDeviceCount);
$value3=round(100 * $model->deviceCategories["optimized"]["color"] / $purchaseDeviceCount);
$highest            = max($value1,$value2,$value3);

$MyData = $factory->newData();
$MyData->addPoints([$value1], "Copy-capable");
$MyData->addPoints([$value2], 'Duplex-capable');
$MyData->addPoints([$value3], 'Color-capable');
$MyData->setAxisName(0,'%');

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$MyData->setPalette("Copy-capable", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphCopyCapableDeviceColor));
$MyData->setPalette('Duplex-capable', $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphDuplexCapableDeviceColor));
$MyData->setPalette('Color-capable', $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphColorDeviceColor));

$myPicture            = $factory->newImage(200, 300, $MyData);
#$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(40, 50, 200-30, 300-50);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$myPicture->drawText(90, 35, "Technology Features\n(Optimized)", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);

/* Write the chart legend - this sets the x/y position */
$myPicture->drawLegend(50, 260, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
$myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);
