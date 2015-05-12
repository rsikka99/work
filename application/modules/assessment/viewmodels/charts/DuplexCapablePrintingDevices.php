<?php

$duplexPercentage = 0;
if ($this->getDevices()->allIncludedDeviceInstances->getCount())
{
    $duplexPercentage = round((($this->getNumberOfDuplexCapableDevices() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
}

$notDuplexPercentage = 100 - $duplexPercentage;

$MyData = $factory->newData();
$MyData->addPoints([$duplexPercentage, $notDuplexPercentage], 'Duplex-Capable Printing Devices');
$MyData->setSerieDescription("Duplex-capable", "Not duplex-capable");
$MyData->addPoints(["Duplex-capable", "Not duplex-capable"], "Labels");
$MyData->setAbscissa("Labels");

$graphDuplexDeviceColor    = $hexToRGBConverter->hexToRgb($dealerBranding->graphDuplexCapableDeviceColor);
$graphNotDuplexDeviceColor = $hexToRGBConverter->hexToRgb($dealerBranding->graphNotCompatibleDeviceColor);
$graphDuplexSetting        = ['R' => $graphDuplexDeviceColor['r'], 'G' => $graphDuplexDeviceColor['g'], 'B' => $graphDuplexDeviceColor['b']];
$graphNotDuplexSetting     = ['R' => $graphNotDuplexDeviceColor['r'], 'G' => $graphNotDuplexDeviceColor['g'], 'B' => $graphNotDuplexDeviceColor['b']];

$myPicture = $factory->newImage(305, 210, $MyData, true);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->drawText(150, 20, "Duplex-Capable Printing Devices", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
$PieChart = $factory->newChart("pie", $myPicture, $MyData);
$PieChart->setSliceColor(0, $graphDuplexSetting);
$PieChart->setSliceColor(1, $graphNotDuplexSetting);
$PieChart->drawPieLegend(90, 170, $pieLegendStyles);

$PieChart->draw3DPie(150, 100, array_merge($pieChartStyles, ["Radius" => 100]));
