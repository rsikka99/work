<?php

/**
 * Class Preferences_Form_QuoteSettingTest
 */
class Preferences_Form_QuoteSettingTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return Preferences_Form_QuoteSetting
     */
    public function getForm ()
    {
        return new Preferences_Form_QuoteSetting();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_QuoteSettingPrefs.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_QuoteSettingPrefs.xml");
    }

}