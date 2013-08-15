<?php

class Preferences_Form_ReportSettingFormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Preferences_Form_ReportSetting
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Preferences_Form_ReportSetting(1);
        parent::setUp();
    }

    public function testCanCreateForm ()
    {
        $this->assertInstanceOf('Preferences_Form_ReportSetting', $this->_form);
    }

    public function goodData ()
    {
        $xml      = simplexml_load_file(__DIR__ . "/_files/goodData_reportSettingFormTest.xml");
        $data = array();
        foreach ($xml->reportSetting as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * @dataProvider goodData
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->assertTrue($this->_form->isValid((array)$data));
    }

    public function badData ()
    {
        $xml      = simplexml_load_file(__DIR__ . "/_files/badData_reportSettingFormTest.xml");
        $data = array();
        foreach ($xml->reportSetting as $row)
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
        $this->assertFalse($this->_form->isValid((array)$data));
    }

    public function nullData ()
    {
        $xml      = simplexml_load_file(__DIR__ . "/_files/nullData_reportSettingFormTest.xml");
        $data = array();

        foreach ($xml->reportSetting as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * @dataProvider nullData
     */
    public function testFormAcceptNullValues ($data)
    {
        $form = new Preferences_Form_ReportSetting(1);
        $form->allowNullValues();
        $this->assertTrue($form->isValid((array)$data));
    }
}