<?php

class Preferences_Form_ReportSettingFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @return Preferences_Form_ReportSetting
     */
    public function getForm ()
    {
        return new Preferences_Form_ReportSetting(1);
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_reportSettingFormTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_reportSettingFormTest.xml");
    }


    public function testCanCreateForm ()
    {
        $this->assertInstanceOf('Preferences_Form_ReportSetting', $this->getForm());
    }

    public function nullData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/nullData_reportSettingFormTest.xml");
        $data = array();

        foreach ($xml->reportSetting as $row)
        {
            $data[] = (array)$row;
        }

        return $data;
    }

    /**
     * @param $data
     *
     * @dataProvider nullData
     */
    public function testFormAcceptNullValues ($data)
    {
        $form = new Preferences_Form_ReportSetting(1);
        $form->allowNullValues();
        $this->assertTrue($form->isValid((array)$data));
    }
}