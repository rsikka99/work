<?php

class Proposalgen_Form_MasterDeviceManagement_HardwareOptimizationsFormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return Proposalgen_Form_MasterDeviceManagement_HardwareOptimization
     */
    public function getForm ()
    {
        return new Proposalgen_Form_MasterDeviceManagement_HardwareOptimization();
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function goodData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_hardwareOptimizationsFormTest.xml");
        $data = array();

        foreach ($xml->hardwareOptimization as $row)
        {
            $row    = json_decode(json_encode($row), 1);
            $data[] = $row;
        }

        return $data;
    }

    /**
     * @dataProvider goodData
     *               Tests whether the form accepts valid data
     */
    public function testFormAcceptsValidData ($data)
    {
        $form = $this->getForm();
        $this->assertTrue($form->isValid($data), implode(' | ', $form->getErrorMessages()));
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function badData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_hardwareOptimizationsFormTest.xml");
        $data = array();

        foreach ($xml->hardwareOptimization as $row)
        {
            $row    = json_decode(json_encode($row), 1);
            $data[] = $row;
        }

        return $data;
    }

    /**
     * @dataProvider badData
     *               Tests if the form errors on invalid data
     */
    public function testFormRejectsBadData ($data)
    {
        $form = $this->getForm();
        $this->assertFalse($form->isValid($data), implode(' | ', $form->getErrorMessages()));
    }
}

