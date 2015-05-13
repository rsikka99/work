<?php

$deviceAges = [
    "Less than 5 years old" => 0,
    "5-6 years old"         => 0,
    "7-8 years old"         => 0,
    "More than 8 years old" => 0
];
foreach ($this->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $device)
{
    if ($device->getAge() < 5)
    {
        $deviceAges ["Less than 5 years old"]++;
    }
    else if ($device->getAge() <= 6)
    {
        $deviceAges ["5-6 years old"]++;
    }
    else if ($device->getAge() <= 8)
    {
        $deviceAges ["7-8 years old"]++;
    }
    else
    {
        $deviceAges ["More than 8 years old"]++;
    }
}
$dataSet     = [];
$legendItems = [];
$labels      = [];

foreach ($deviceAges as $legendItem => $count)
{
    $legendItems [] = $legendItem;
    $dataSet []     = $count;
    if ($count > 0)
    {
        $percentage = round(($count / $this->getDevices()->purchasedDeviceInstances->getCount()) * 100, 2);
        $labels []  = "$percentage%";
    }
}
$MyData = $factory->newData();
$MyData->addPoints($dataSet, 'Age of printing devices');
$MyData->setSerieDescription($deviceAges);
$keys = [];
foreach ($deviceAges as $key => $value)
{
    $keys[] = $key;
}
$MyData->addPoints($keys, "Labels");
$MyData->setAbscissa("Labels");

$color1 = $hexToRGBConverter->hexToRgb($dealerBranding->graphAgeOfDevices1);
$color2 = $hexToRGBConverter->hexToRgb($dealerBranding->graphAgeOfDevices2);
$color3 = $hexToRGBConverter->hexToRgb($dealerBranding->graphAgeOfDevices3);
$color4 = $hexToRGBConverter->hexToRgb($dealerBranding->graphAgeOfDevices4);

$rgbColor1 = ['R' => $color1['r'], 'G' => $color1['g'], 'B' => $color1['b']];
$rgbColor2 = ['R' => $color2['r'], 'G' => $color2['g'], 'B' => $color2['b']];
$rgbColor3 = ['R' => $color3['r'], 'G' => $color3['g'], 'B' => $color3['b']];
$rgbColor4 = ['R' => $color4['r'], 'G' => $color4['g'], 'B' => $color4['b']];

$myPicture = $factory->newImage(400, 270, $MyData, true);
$myPicture->setFontProperties(["FontName" => APPLICATION_BASE_PATH."/assets/fonts/DejaVuSans.ttf", "FontSize" => 8, "R" => 127, "G" => 127, "B" => 127]);

$PieChart = $factory->newChart("pie", $myPicture, $MyData);
$PieChart->setSliceColor(0, $rgbColor1);
$PieChart->setSliceColor(1, $rgbColor2);
$PieChart->setSliceColor(2, $rgbColor3);
$PieChart->setSliceColor(3, $rgbColor4);

$PieChart->drawPieLegend(120, 170, $pieLegendStyles);

$PieChart->draw3DPie(180, 100, array_merge($pieChartStyles, ["Radius" => 100, "ValueR" => 127, "ValueG" => 127, "ValueB" => 127]));
