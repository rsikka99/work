<?php

/**
 * Class Hardwareoptimization_Form_SettingTest
 */
class Hardwareoptimization_Form_SettingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Hardwareoptimization_Form_Setting
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Hardwareoptimization_Form_Setting();
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
    public function goodHOSettingData ()
    {
        $xml = simplexml_load_file(__DIR__ . "/_files/goodData_hardwareFormTest.xml");
        $data = array();
        foreach ($xml->setting as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * This function loads an XML file of bad data into arrays to be tested in the form
     */
    public function badHOSettingData ()
    {
        $xml = simplexml_load_file(__DIR__ . "/_files/badData_hardwareFormTest.xml");
        $data = array();
        foreach ($xml->setting as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid data
     *
     *
     * @dataProvider goodHOSettingData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badHOSettingData
     */
    public function testFormRejectsBadData ($data)
    {
        $this->assertFalse($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }

}