<?php

$pagesPrintedOnManaged       = "Managed/on " . My_Brand::$jit;
$pagesPrintedOnNotCompatible = "Not " . My_Brand::$jit . " compatible";
$pagesPrintedOnCompatible    = My_Brand::$jit . " compatible";

$pagesPrinted = [
    $pagesPrintedOnManaged       => 0,
    $pagesPrintedOnNotCompatible => 0,
    $pagesPrintedOnCompatible    => 0,
];

$pagesPrinted[$pagesPrintedOnManaged]       = round($this->getDevices()->isManagedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly());
$pagesPrinted[$pagesPrintedOnNotCompatible] = round($this->getDevices()->notCompatibleDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly());
$pagesPrinted[$pagesPrintedOnCompatible]    = round($this->getDevices()->compatibleDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly());

$highest = max($pagesPrinted[$pagesPrintedOnManaged], $pagesPrinted [$pagesPrintedOnNotCompatible], $pagesPrinted[$pagesPrintedOnCompatible]);

$MyData = $factory->newData();
$MyData->addPoints([$pagesPrinted[$pagesPrintedOnManaged]], $pagesPrintedOnManaged);
$MyData->addPoints([$pagesPrinted[$pagesPrintedOnNotCompatible]], $pagesPrintedOnNotCompatible);
$MyData->addPoints([$pagesPrinted[$pagesPrintedOnCompatible]], $pagesPrintedOnCompatible);

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$MyData->setPalette($pagesPrintedOnManaged, $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphManagedDeviceColor));
$MyData->setPalette($pagesPrintedOnNotCompatible . " compatible", $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphNotCompatibleDeviceColor));
$MyData->setPalette($pagesPrintedOnCompatible, $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphJitCompatibleDeviceColor));

$myPicture            = $factory->newImage(280, 230, $MyData);
$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(60, 40, 280-20, 230-60);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$MyData->setAxisDisplay(0, AXIS_FORMAT_CUSTOM, 'number_format');

$myPicture->drawText(10, 20, "Total Pages Printed", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0]);

/* Write the chart legend - this sets the x/y position */
$myPicture->drawLegend(50, 230-50, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
$myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);
