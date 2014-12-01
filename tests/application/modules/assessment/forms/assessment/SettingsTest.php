<?php

/**
 * Class Assessment_Form_Assessment_SettingsTest
 */
class Assessment_Form_Assessment_SettingsTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @var Assessment_Form_Assessment_Settings
     */
    protected $_form;

    /**
     * Gets the form to be used for testing
     *
     * @return Assessment_Form_Assessment_Settings|Zend_Form
     */
    public function getForm ()
    {
        /**
         * @var PHPUnit_Framework_MockObject_MockObject | Assessment_Model_Assessment_Setting $defaultSettings
         */
        $defaultSettings = $this->getMock('Assessment_Model_Assessment_Setting');
        $this->_form     = new Assessment_Form_Assessment_Settings($defaultSettings);

        return $this->_form;
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_SettingsTest.xml");
    }

    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_SettingsTest.xml");
    }
}