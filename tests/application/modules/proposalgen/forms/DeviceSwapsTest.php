<?php

/**
 * Class Proposalgen_Form_DeviceSwapTest
 */
class Proposalgen_Form_DeviceSwapTest extends PHPUnit_Framework_TestCase
{
    public function goodData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_deviceSwapsSettingTest.xml");
        $data = array();
        foreach ($xml->deviceSwap as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    public function badData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_deviceSwapsSettingTest.xml");
        $data = array();
        foreach ($xml->deviceSwap as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * @dataProvider badData
     */
    public function testFormRejectsBadData ($data)
    {
        $this->assertFalse($this->getForm()->isValid((array)$data), implode(' | ', $this->getForm()->getErrorMessages()));
    }

    /**
     * @dataProvider goodData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->getForm()->isValid((array)$data), implode(' | ', $this->getForm()->getErrorMessages()));
    }

    private function getForm ()
    {
        return new Hardwareoptimization_Form_DeviceSwaps();
    }
}