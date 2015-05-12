<?php

if ($this->getDevices()->allIncludedDeviceInstances->getCount())
{
    $scanPercentage = round((($this->getNumberOfScanCapableDevices() / $this->getDevices()->allIncludedDeviceInstances->getCount()) * 100), 2);
}
else
{
    $scanPercentage = 0;
}
$notScanPercentage = 100 - $scanPercentage;

$MyData = $factory->newData();
$MyData->addPoints([$scanPercentage, $notScanPercentage], 'Scan-Capable Printing Devices');
$MyData->setSerieDescription("Scan-capable", "Not scan-capable");
$MyData->addPoints(["Scan-capable", "Not scan-capable"], "Labels");
$MyData->setAbscissa("Labels");

$graphCopyCapableDeviceColor   = $hexToRGBConverter->hexToRgb($dealerBranding->graphCopyCapableDeviceColor);
$graphNotCompatibleDeviceColor = $hexToRGBConverter->hexToRgb($dealerBranding->graphNotCompatibleDeviceColor);
$copyCapableSetting            = ['R' => $graphCopyCapableDeviceColor['r'], 'G' => $graphCopyCapableDeviceColor['g'], 'B' => $graphCopyCapableDeviceColor['b']];
$notCompatibleSetting          = ['R' => $graphNotCompatibleDeviceColor['r'], 'G' => $graphNotCompatibleDeviceColor['g'], 'B' => $graphNotCompatibleDeviceColor['b']];

$myPicture = $factory->newImage(305, 210, $MyData, true);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->drawText(150, 20, "Scan-Capable Printing Devices", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
$PieChart = $factory->newChart("pie", $myPicture, $MyData);
$PieChart->setSliceColor(0, $copyCapableSetting);
$PieChart->setSliceColor(1, $notCompatibleSetting);
$PieChart->drawPieLegend(90, 170, $pieLegendStyles);

$PieChart->draw3DPie(150, 100, array_merge($pieChartStyles, ["Radius" => 100]));
