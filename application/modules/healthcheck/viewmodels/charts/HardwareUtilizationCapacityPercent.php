<?php

$value1 = $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly();
$value2 = $this->getMaximumMonthlyPrintVolume();

$percentage = round(100 * $value1 / (max(1,$value2)));

$highest  = max($value1, $value2);

$MyData  = $factory->newData();
$MyData->addPoints([50,$percentage], 'Percent');
$MyData->setAxisName(0,'% capacity');
$MyData->setAxisPosition(0, AXIS_POSITION_BOTTOM);
$MyData->addPoints(array('Optimal monthly fleet usage range','Your estimated monthly usage'),'Values');
$MyData->setSerieDescription('Percent');
$MyData->setAbscissa('Values');

$MyData->addPoints(array(30,$percentage),"Floating 0");
$MyData->setSerieDrawable("Floating 0",FALSE);

$MyData->setAxisDisplay(0, AXIS_FORMAT_CUSTOM, 'number_format');

$myPicture            = $factory->newImage(650, 130, $MyData);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);

$myPicture->Antialias = false;
$myPicture->setGraphArea(200, 40, 650-20, 130-30);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => 100]];
$myPicture->drawScale(["Pos"=>SCALE_POS_TOPBOTTOM, "Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$palette = [
    $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphCustomerColor),
    $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphPositiveColor),
];

/* Write the chart legend - this sets the x/y position */
$myPicture->drawBarChart(["Floating0Serie"=>"Floating 0","DisplayValues" => true,"OverrideColors" => $palette, "Surrounding" => 30]);

