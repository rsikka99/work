<?php

$deviceVendorCount = $this->getDeviceVendorCount();

$MyData = $factory->newData();
$MyData->addPoints($deviceVendorCount, 'DeviceVendorCount');
$MyData->setSerieDescription("ScoreA", "Application A");

/* Define the absissa serie */
$MyData->addPoints($uniqueModelArray, "Labels");
$MyData->setAbscissa("Labels");

$myPicture = $factory->newImage(400, 250, $MyData, true);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);

$PieChart = $factory->newChart("pie", $myPicture, $MyData);
for ($i = 0; $i < count($deviceVendorCount); $i++)
{
    $hexColor = $hexToRGBConverter->hexToRgb_uppercase(Healthcheck_ViewModel_Healthcheck::$COLOR_ARRAY[$i]);
    $PieChart->setSliceColor($i, $hexColor);
}

$numberOfIncludedDevices = $this->getDevices()->allIncludedDeviceInstances->getCount();
$myPicture->drawText(10, 30, "Percent per Device Brand on your Network\nTotal Devices - " . $numberOfIncludedDevices, ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0]);

$PieChart->draw3DPie(200, 155, ["SecondPass" => true, "Radius" => 170]);
