<?php

class Proposalgen_Form_MasterDeviceManagement_AvailableOptionsFormTest extends PHPUnit_Framework_TestCase
{
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Proposalgen_Form_MasterDeviceManagement_AvailableOptions();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function goodData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_availableOptionsFormTest.xml");
        $data = array();

        foreach ($xml->option as $row)
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
        $this->assertTrue($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function badData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_availableOptionsFormTest.xml");
        $data = array();

        foreach ($xml->option as $row)
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
        $this->assertFalse($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }
}

