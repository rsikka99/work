<?php

$highest = ($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() > $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) ? $this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() : $this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
$MyData  = $factory->newData();
$MyData->addPoints([round($this->getDevices()->leasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())], "Leased devices");
$MyData->addPoints([round($this->getDevices()->purchasedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly())], 'Purchased devices');

//Fixes x access scale appearing - hacky - needs fixing
$MyData->addPoints([""], "Printer Types");
$MyData->setSerieDescription("Printer Types", "Type");
$MyData->setAbscissa("Printer Types");

$leasedRGB             = $hexToRGBConverter->hexToRgb($dealerBranding->graphLeasedDeviceColor);
$purchasedRGB          = $hexToRGBConverter->hexToRgb($dealerBranding->graphPurchasedDeviceColor);
$leasedColorSetting    = ['R' => $leasedRGB['r'], 'G' => $leasedRGB['g'], 'B' => $leasedRGB['b']];
$purchasedColorSetting = ['R' => $purchasedRGB['r'], 'G' => $purchasedRGB['g'], 'B' => $purchasedRGB['b']];
$MyData->setPalette("Leased devices", $leasedColorSetting);
$MyData->setPalette("Purchased devices", $purchasedColorSetting);

$myPicture            = $factory->newImage(175, 300, $MyData);
$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(60, 50, 150, 245);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(["Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$myPicture->drawText(90, 30, "Leased vs Purchase\nPage Counts", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);

/* Write the chart legend - this sets the x/y position */
$myPicture->drawLegend(50, 260, ["Style" => LEGEND_NOBORDER, "Mode" => LEGEND_VERTICAL, "FontR" => 127, "FontG" => 127, "FontB" => 127]);
$myPicture->drawBarChart(["DisplayValues" => true, "Surrounding" => -30]);
