<?php

/**
 * Class Preferences_Form_HardwareoptimizationSettingTest
 */
class Preferences_Form_HardwareoptimizationSettingTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @return Preferences_Form_HardwareoptimizationSetting
     */
    public function getForm ()
    {
        return new Preferences_Form_HardwareoptimizationSetting();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_hardwareOptSettingPrefFormTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_hardwareOptSettingPrefFormTest.xml");
    }

  
}