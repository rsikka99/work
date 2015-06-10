<?php

$deviceVendorCount = [];
foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
{
    if (array_key_exists($device->getMasterDevice()->modelName, $deviceVendorCount))
    {
        $deviceVendorCount [$device->getMasterDevice()->modelName] += 1;
    }
    else
    {
        $deviceVendorCount [$device->getMasterDevice()->modelName] = 1;
    }
}
arsort($deviceVendorCount);

$MyData = $factory->newData();
$MyData->addPoints($deviceVendorCount, 'Unique models');
$MyData->setSerieDescription("ScoreA", "Application A");

/* Define the absissa serie */
$MyData->addPoints($deviceVendorCount, "Labels");
$MyData->setAbscissa("Labels");

$myPicture = $factory->newImage(625, 270, $MyData, true);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);

$PieChart = $factory->newChart("pie", $myPicture, $MyData);

for ($i = 0; $i < count($deviceVendorCount); $i++)
{
    $hexColor = $hexToRGBConverter->hexToRgb_uppercase(Healthcheck_ViewModel_Healthcheck::$COLOR_ARRAY[$i]);
    $PieChart->setSliceColor($i, $hexColor);
}

$numberOfIncludedDevices = $this->getDevices()->allIncludedDeviceInstances->getCount();
$myPicture->drawText(10, 30, "Percent per Printing Device Model on your Network\nTotal Devices - " . $numberOfIncludedDevices, ["FontSize" => 9, "R" => 0, "G" => 0, "B" => 0]);

$PieChart->draw3DPie(280, 150, ["SecondPass" => true, "Radius" => 170]);
