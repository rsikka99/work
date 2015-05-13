<?php

$masterDevices = $model->getUniquePurchasedMasterDevices($model->getAllMasterDevicesWithReplacements());
$value1 = $model->countUniqueTonerList($masterDevices);
$highest           = $model->getMaximumSupplyCount($masterDevices);
$graphMaximum      = $highest + $highest * .15;
$graphMaximum      = ($graphMaximum > 0) ? $graphMaximum : 1;
$diamond           = $value / $graphMaximum;
$targetUniqueness = $highest * 0.15;

$MyData  = $factory->newData();
$MyData->addPoints([$targetUniqueness, $value1], 'Value');
$MyData->setAxisPosition(0, AXIS_POSITION_BOTTOM);
$MyData->addPoints(array('Ideal supply uniqueness','Your supply uniqueness'),'Values');
$MyData->setSerieDescription('Value');
$MyData->setAbscissa('Values');

$MyData->addPoints(array($targetUniqueness,$value1),"Floating 0");
$MyData->setSerieDrawable("Floating 0",FALSE);

$MyData->setAxisDisplay(0, AXIS_FORMAT_CUSTOM, 'number_format');

$myPicture            = $factory->newImage(650, 160, $MyData);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);

$myPicture->Antialias = false;
$myPicture->setGraphArea(210, 40, 650-20, 160-30);
$AxisBoundaries = [0 => ["Min" => 0, "Max" => $graphMaximum]];
$myPicture->drawScale(["Pos"=>SCALE_POS_TOPBOTTOM, "Mode" => SCALE_MODE_MANUAL, "ManualScale" => $AxisBoundaries, "AxisR" => 127, "AxisG" => 127, "AxisB" => 127, "InnerTickWidth" => 0, "OuterTickWidth" => 0, "TickR" => 127, "TickG" => 127, "TickB" => 127]);

$myPicture->drawText(90, 35, "Unique Supply Types\n(Optimized)", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);

$palette = [
    $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphOptimalSituationColor),
    $hexToRGBConverter->hexToRgb_uppercase($dealerBranding->graphNewSituationColor),
];

/* Write the chart legend - this sets the x/y position */
$myPicture->drawBarChart(["Floating0Serie"=>"Floating 0","DisplayValues" => true,"OverrideColors" => $palette, "Surrounding" => 30]);

