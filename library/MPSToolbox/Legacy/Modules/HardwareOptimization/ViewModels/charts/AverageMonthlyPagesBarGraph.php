<?php

$averagePageCount = round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getCount(), 0);
$highest          = max($averagePageCount, self::AVERAGE_MONTHLY_PAGES_PER_DEVICE);

$MyData = $factory->newData();
$MyData->addPoints([$averagePageCount], $companyName);
$MyData->addPoints([self::AVERAGE_MONTHLY_PAGES_PER_DEVICE], "Average");

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$graphCustomerColor        = $hexToRGBConverter->hexToRgb($dealerBranding->graphCustomerColor);
$graphIndustryAverageColor = $hexToRGBConverter->hexToRgb($dealerBranding->graphIndustryAverageColor);
$customerColorSetting      = ['R' => $graphCustomerColor['r'], 'G' => $graphCustomerColor['g'], 'B' => $graphCustomerColor['b']];
$averageColorSetting       = ['R' => $graphIndustryAverageColor['r'], 'G' => $graphIndustryAverageColor['g'], 'B' => $graphIndustryAverageColor['b']];
$MyData->setPalette($companyName, $customerColorSetting);
$MyData->setPalette("Average", $averageColorSetting);

$myPicture            = $factory->newImage(175, 300, $MyData);
$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(60, 50, 150, 245);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$myPicture->drawText(10, 30, "Average Monthly Pages \n per Networked Printer", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0]);

/* Write the chart legend - this sets the x/y position */
$myPicture->drawLegend(50, 260, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
$myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);
