<?php

use Tangent\Filter\Filename;

class Tangent_Filter_FilenameTest extends PHPUnit_Framework_TestCase
{

    /**
     * This function loads an XML file of raw filenames and their expected result into an array to be tested
     */
    public function arrayData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/filterData.xml");
        $data = array();

        foreach ($xml->filename as $row)
        {
            $row    = json_decode(json_encode($row), 1);
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     *    Test raw file names after string sanitization against expected results
     *
     * @dataProvider arrayData
     */
    public function testFilterArray ($rawFilename, $expectedResult)
    {
        $filter = new Filename();
        $this->assertSame($filter->filter($rawFilename), $expectedResult);
    }
}