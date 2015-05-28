<?php

$colorPercentage = 0;
if ($this->getDevices()->allIncludedDeviceInstances->getCount())
{
    $colorPercentage = round((($this->getNumberOfColorCapableDevices() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
}
$notColorPercentage = 100 - $colorPercentage;

$MyData = $factory->newData();
$MyData->addPoints([$colorPercentage, $notColorPercentage], 'color-capable-devices');
$MyData->setSerieDescription("Color-capable", "Black-and-white only");
$MyData->addPoints(["Color-capable", "Black-and-white only"], "Labels");
$MyData->setAbscissa("Labels");

$graphColorDeviceColor = $hexToRGBConverter->hexToRgb($dealerBranding->graphColorDeviceColor);
$graphMonoDeviceColor  = $hexToRGBConverter->hexToRgb($dealerBranding->graphMonoDeviceColor);
$graphColorSetting     = ['R' => $graphColorDeviceColor['r'], 'G' => $graphColorDeviceColor['g'], 'B' => $graphColorDeviceColor['b']];
$graphMonoSetting      = ['R' => $graphMonoDeviceColor['r'], 'G' => $graphMonoDeviceColor['g'], 'B' => $graphMonoDeviceColor['b']];

$myPicture = $factory->newImage(305, 210, $MyData, true);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->drawText(95, 40, "Color-capable\nPrinting Devices", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
$PieChart = $factory->newChart("pie", $myPicture, $MyData);
$PieChart->setSliceColor(0, $graphColorSetting);
$PieChart->setSliceColor(1, $graphMonoSetting);
$PieChart->drawPieLegend(60, 210-40, $pieLegendStyles);

$PieChart->draw3DPie(120, 110, array_merge($pieChartStyles, ["Radius" => 90]));
