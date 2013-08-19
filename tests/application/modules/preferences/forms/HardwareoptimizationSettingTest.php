<?php

/**
 * Class Preferences_Form_HardwareoptimizationSettingTest
 */
class Preferences_Form_HardwareoptimizationSettingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Preferences_Form_HardwareoptimizationSetting
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Preferences_Form_HardwareoptimizationSetting();
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
    public function goodHOSettingPrefData ()
    {
        $xml = simplexml_load_file(__DIR__ . "/_files/goodData_hardwareOptSettingPrefFormTest.xml");
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
    public function badHOSettingPrefData ()
    {
        $xml = simplexml_load_file(__DIR__ . "/_files/badData_hardwareOptSettingPrefFormTest.xml");
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
     * @dataProvider goodHOSettingPrefData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badHOSettingPrefData
     */
    public function testFormRejectsBadData ($data)
    {
        $this->assertFalse($this->_form->isValid((array)$data), implode(' | ', $this->_form->getErrorMessages()));
    }

}