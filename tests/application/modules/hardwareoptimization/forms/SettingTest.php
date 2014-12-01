<?php

/**
 * Class Hardwareoptimization_Form_SettingTest
 */
class Hardwareoptimization_Form_SettingTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * Gets the form for the test
     *
     * @return Hardwareoptimization_Form_Setting
     */
    public function getForm ()
    {
        return new Hardwareoptimization_Form_Setting();
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_hardwareFormTest.xml");
    }

    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_hardwareFormTest.xml");
    }
}