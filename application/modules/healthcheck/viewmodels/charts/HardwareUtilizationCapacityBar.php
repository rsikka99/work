<?php

$value1 = round($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly());
$value2 = round($this->getMaximumMonthlyPrintVolume());
$highest  = max($value1, $value2);

$MyData  = $factory->newData();
$MyData->addPoints([$value1, $value2], 'Pages');
$MyData->setAxisName(0,'Pages');
$MyData->setAxisPosition(0, AXIS_POSITION_BOTTOM);
$MyData->addPoints(array('Estimated actual monthly usage','Maximum monthly fleet capacity'),'Values');
$MyData->setSerieDescription("Pages","Pages");
$MyData->setAbscissa('Values');

$MyData->setAxisDisplay(0, AXIS_FORMAT_CUSTOM, 'number_format');

$myPicture            = $factory->newImage(650, 230, $MyData);
$myPicture->Antialias = false;
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->setGraphArea(200, 40, 650-20, 230-60);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $highest * 1.1]];
$myPicture->drawScale(['RemoveYAxis'=>true, "Pos"=>SCALE_POS_TOPBOTTOM, "Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$palette = [
    $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphCustomerColor),
    $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphPositiveColor),
];

/* Write the chart legend - this sets the x/y position */
$myPicture->drawBarChart(["DisplayValues" => true, "OverrideColors" => $palette, "Surrounding" => 30]);

