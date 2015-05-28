<?php

$uniqueModelArray = [];
foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
{
    if (array_key_exists($device->getMasterDevice()->modelName, $uniqueModelArray))
    {
        $uniqueModelArray [$device->getMasterDevice()->modelName] += 1;
    }
    else
    {
        $uniqueModelArray [$device->getMasterDevice()->modelName] = 1;
    }
    $abscissaArray[] = $device->assetId;
}
$MyData = $factory->newData();
$MyData->addPoints($uniqueModelArray, 'Unique models');
$MyData->setSerieDescription("ScoreA", "Application A");

/* Define the absissa serie */
$MyData->addPoints($uniqueModelArray, "Labels");
$MyData->setAbscissa("Labels");

$myPicture = $factory->newImage(700, 270, $MyData, true);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);

$colorArray = ["E21736", "b0bb21", "5c3f9b", "0191d3", "f89428", "e4858f", "fcc223", "B3C6FF", "ECFFB3", "386AFF", "FFB3EC", "cccccc", "00ff00", "000000", "E21736", "b0bb21", "5c3f9b", "0191d3", "f89428",
    "e4858f", "fcc223", "B3C6FF", "ECFFB3", "386AFF", "FFB3EC", "cccccc", "00ff00", "000000"];

$PieChart = $factory->newChart("pie", $myPicture, $MyData);

for ($i = 0; $i < count($uniqueModelArray); $i++)
{
    $hexColor = $hexToRGBConverter->hexToRgb($colorArray[$i]);
    $PieChart->setSliceColor(0, ["R" => $hexColor['r'], "G" => $hexColor['g'], "B" => $hexColor['b']]);
}

$PieChart->draw3DPie(280, 125, ["SecondPass" => true, "Radius" => 170]);
