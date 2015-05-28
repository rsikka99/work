<?php

$purchaseDeviceCount = count($this->getDevices()->purchasedDeviceInstances->getDeviceInstances());

$value1=$model->deviceAgesOptimized[0];
$value2=$model->deviceAgesOptimized[2];
$value3=$model->deviceAgesOptimized[4];
$value4=$model->deviceAgesOptimized[8];
$highest            = max($value1,$value2,$value3,$value4);

$MyData = $factory->newData();
$MyData->addPoints([$value1], '0-2 Years');
$MyData->addPoints([$value2], '2-4 Years');
$MyData->addPoints([$value3], '4-8 Years');
$MyData->addPoints([$value3], '8+ Years');
$MyData->setAxisName(0,'%');

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$MyData->setPalette('0-2 Years', $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphAgeOfDevices1));
$MyData->setPalette('2-4 Years', $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphAgeOfDevices2));
$MyData->setPalette('4-8 Years', $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphAgeOfDevices3));
$MyData->setPalette('8+ Years', $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphAgeOfDevices4));

$myPicture            = $factory->newImage(225, 325, $MyData);
#$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(40, 50, 225-30, 325-80);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$myPicture->drawText(90, 30, 'Age Of Devices (Optimized)', ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);

/* Write the chart legend - this sets the x/y position */
$myPicture->drawLegend(50, 260, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
$myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);
