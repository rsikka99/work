<?php

$value1 = count($model->kept);
$value2 = count($model->replaced);
$value3 = count($model->flagged);
$value4 = count($model->retired);

$highest = max($value1,$value2,$value3,$value4);

$MyData = $factory->newData();
$MyData->addPoints([$value1], 'Keep');
$MyData->addPoints([$value2], 'Replace');
$MyData->addPoints([$value3], 'Do Not Repair (Replace when broken)');
$MyData->addPoints([$value4], 'Retire/Migrate (Low Page Volume)');

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$MyData->setPalette('Keep', $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphColorDeviceColor));
$MyData->setPalette('Replace', $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphReplacedDeviceColor));
$MyData->setPalette('Do Not Repair (Replace when broken)', $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphDoNotRepairDeviceColor));
$MyData->setPalette('Retire/Migrate (Low Page Volume)', $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphRetireDeviceColor));

$myPicture            = $factory->newImage(375, 250, $MyData);
$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(60, 50, 280-25, 210-55);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$myPicture->drawText(90, 30, "Optimization Fleet Summary", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);

/* Write the chart legend - this sets the x/y position */
$myPicture->drawLegend(50, 210-40, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
$myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);

