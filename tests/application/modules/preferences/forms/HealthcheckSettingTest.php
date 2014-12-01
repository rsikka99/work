<?php

/**
 * Class Preferences_Form_HealthcheckSettingTest
 */
class Preferences_Form_HealthcheckSettingTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @return Preferences_Form_HealthcheckSetting
     */
    public function getForm ()
    {
        return new Preferences_Form_HealthcheckSetting();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_HealthcheckFormSettingsPrefTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_HealthcheckFormSettingsPrefTest.xml");
    }

}