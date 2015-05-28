<?php

$colorPercentage = 0;
if ($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly() > 0)
{
    $colorPercentage = round((($this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getColorPageCount()->getMonthly() / $this->getDevices()->allIncludedDeviceInstances->getPageCounts()->getCombinedPageCount()->getMonthly()) * 100), 2);
}

$bwPercentage = 100 - $colorPercentage;

$MyData = $factory->newData();
$MyData->addPoints([$colorPercentage, $bwPercentage], 'Color pages printed');
$MyData->setSerieDescription("Color pages printed", "Black-and-white pages printed");
$MyData->addPoints(["Color pages printed", "Black-and-white pages printed"], "Labels");
$MyData->setAbscissa("Labels");

$graphColorDeviceColor = $hexToRGBConverter->hexToRgb($dealerBranding->graphColorDeviceColor);
$graphMonoDeviceColor  = $hexToRGBConverter->hexToRgb($dealerBranding->graphMonoDeviceColor);
$graphColorSetting     = ['R' => $graphColorDeviceColor['r'], 'G' => $graphColorDeviceColor['g'], 'B' => $graphColorDeviceColor['b']];
$graphMonoSetting      = ['R' => $graphMonoDeviceColor['r'], 'G' => $graphMonoDeviceColor['g'], 'B' => $graphMonoDeviceColor['b']];

$myPicture = $factory->newImage(400, 210, $MyData, true);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);
$myPicture->drawText(180, 20, "Color vs Black/White Pages", ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0, "Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
$PieChart = $factory->newChart("pie", $myPicture, $MyData);
$PieChart->setSliceColor(0, $graphColorSetting);
$PieChart->setSliceColor(1, $graphMonoSetting);

$PieChart->drawPieLegend(120, 170, $pieLegendStyles);
$PieChart->draw3DPie(180, 100, array_merge($pieChartStyles, ["Radius" => 100]));

$this->pImageGraphs[] = $myPicture;
