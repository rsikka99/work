<?php

$colorPercentage = 0;
if ($this->getDevices()->allIncludedDeviceInstances->getCount())
{
    $colorPercentage = round((($this->getDevices()->scanCapableDeviceInstances->getCount() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
}

$notColorPercentage = 100 - $colorPercentage;

$MyData = $factory->newData();
$MyData->addPoints([$colorPercentage, $notColorPercentage], 'color-capable-devices');
$MyData->setSerieDescription("Scan-capable", "Not scan-capable");
$MyData->addPoints(["Scan-capable", "Not scan-capable"], "Labels");
$MyData->setAbscissa("Labels");

$graphColorDeviceColor = $hexToRGBConverter->hexToRgb($dealerBranding->graphColorDeviceColor);
$graphMonoDeviceColor  = $hexToRGBConverter->hexToRgb($dealerBranding->graphMonoDeviceColor);
$graphColorSetting     = ['R' => $graphColorDeviceColor['r'], 'G' => $graphColorDeviceColor['g'], 'B' => $graphColorDeviceColor['b']];
$graphMonoSetting      = ['R' => $graphMonoDeviceColor['r'], 'G' => $graphMonoDeviceColor['g'], 'B' => $graphMonoDeviceColor['b']];

$myPicture = $factory->newImage(270, 175, $MyData, true);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->drawText(130, 20, "Scan-capable Printing Devices", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
$PieChart = $factory->newChart("pie", $myPicture, $MyData);
$PieChart->setSliceColor(0, $graphColorSetting);
$PieChart->setSliceColor(1, $graphMonoSetting);
$PieChart->drawPieLegend(60, 130, $pieLegendStyles);

$PieChart->draw3DPie(135, 80, array_merge($pieChartStyles, ["Radius" => 70]));
