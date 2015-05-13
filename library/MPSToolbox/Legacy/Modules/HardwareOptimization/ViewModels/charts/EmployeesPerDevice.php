<?php

$devicesPerEmployee = round($employeeCount / $this->getDevices()->allIncludedDeviceInstances->getCount(), 2);
$devicesPerEmployeeOptimized = round($employeeCount / ($this->getDevices()->allIncludedDeviceInstances->getCount() + count($model->leased) + count($model->excluded)), 1);
$highest            = max(self::AVERAGE_EMPLOYEES_PER_DEVICE,$devicesPerEmployee,$devicesPerEmployeeOptimized);

$MyData = $factory->newData();
$MyData->addPoints([$devicesPerEmployee], "Current");
$MyData->addPoints([self::AVERAGE_EMPLOYEES_PER_DEVICE], "Average");
$MyData->addPoints([$devicesPerEmployeeOptimized], "Optimized");

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$MyData->setPalette("Current", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphCurrentSituationColor));
$MyData->setPalette("Average", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphIndustryAverageColor));
$MyData->setPalette("Optimized", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphNewSituationColor));

$myPicture            = $factory->newImage(200, 300, $MyData);
#$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(30, 50, 200-30, 300-50);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$myPicture->drawText(90, 30, " Employees per Device", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);

/* Write the chart legend - this sets the x/y position */
$myPicture->drawLegend(50, 260, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
$myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);
