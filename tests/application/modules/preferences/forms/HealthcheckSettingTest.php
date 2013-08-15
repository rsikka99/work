<?php

/**
 * Class Preferences_Form_HealthcheckSettingTest
 */
class Preferences_Form_HealthcheckSettingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Preferences_Form_HealthcheckSetting
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Preferences_Form_HealthcheckSetting();
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
    public function goodHCFormSettingsPrefData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_HealthcheckFormSettingsPrefTest.xml");
        $data = array();
        foreach ($xml->healthcheck as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * This function loads an XML file of bad data into arrays to be tested in the form
     */
    public function badHCFormSettingsPrefData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_HealthcheckFormSettingsPrefTest.xml");
        $data = array();
        foreach ($xml->healthcheck as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * Test the form using valid data
     *
     *
     * @dataProvider goodHCFormSettingsPrefData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid((array)$data), "Healthcheck setting preferences form did not accept good data.");
    }


    /**
     * Test the form using bad data
     *
     * @dataProvider badHCFormSettingsPrefData
     */
    public function testFormRejectsBadData ($data)
    {
        $this->assertFalse($this->_form->isValid((array)$data), "Healthcheck setting preferences form accepted bad data!");
    }

}