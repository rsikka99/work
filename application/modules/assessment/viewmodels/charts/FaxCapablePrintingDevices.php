<?php

$faxPercentage = 0;
if ($this->getDevices()->allIncludedDeviceInstances->getCount())
{
    $faxPercentage = round((($this->getNumberOfFaxCapableDevices() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
}

$notFaxPercentage = 100 - $faxPercentage;

$MyData = $factory->newData();
$MyData->addPoints([$faxPercentage, $notFaxPercentage], 'Fax-Capable Printing Devices');
$MyData->setSerieDescription("Fax-capable", "Not fax-capable");
$MyData->addPoints(["Fax-capable", "Not fax-capable"], "Labels");
$MyData->setAbscissa("Labels");

$graphFaxCapableDeviceColor    = $hexToRGBConverter->hexToRgb($dealerBranding->graphFaxCapableDeviceColor);
$graphNotCompatibleDeviceColor = $hexToRGBConverter->hexToRgb($dealerBranding->graphNotCompatibleDeviceColor);
$copyCapableSetting            = ['R' => $graphFaxCapableDeviceColor['r'], 'G' => $graphFaxCapableDeviceColor['g'], 'B' => $graphFaxCapableDeviceColor['b']];
$notCompatibleSetting          = ['R' => $graphNotCompatibleDeviceColor['r'], 'G' => $graphNotCompatibleDeviceColor['g'], 'B' => $graphNotCompatibleDeviceColor['b']];

$myPicture = $factory->newImage(190, 160, $MyData, true);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->drawText(95, 20, "Fax-Capable Printing Devices", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
$PieChart = $factory->newChart("pie", $myPicture, $MyData);
$PieChart->setSliceColor(0, $copyCapableSetting);
$PieChart->setSliceColor(1, $notCompatibleSetting);
$PieChart->drawPieLegend(60, 130, $pieLegendStyles);

$PieChart->draw3DPie(100, 90, array_merge($pieChartStyles, ["Radius" => 60]));
