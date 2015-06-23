<?php

abstract class Tangent_PHPUnit_Framework_TestCase extends My_DatabaseTestCase
{
    /**
     * Loads test data from an xml file. Expects a key called testData
     *
     * @param string $filename
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function loadFromXmlFile ($filename)
    {
        if (!file_exists($filename))
        {
            throw new InvalidArgumentException(sprintf('Invalid file "%s"', $filename));
        }

        $xml  = simplexml_load_file($filename);
        $data = [];

        foreach ($xml->testData as $row)
        {
            $row    = json_decode(json_encode($row), 1);
            $data[] = $row;
        }

        return $data;
    }
}
