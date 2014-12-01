<?php

/**
 * Class Healthcheck_Form_Healthcheck_SettingsTest
 */
class Healthcheck_Form_Healthcheck_SettingsTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return Healthcheck_Form_Healthcheck_Settings|Zend_Form
     */
    public function getForm ()
    {
        $mockAdapter = $this->getMock('My_Feature_AdapterInterface', array('getFeatures'));
        $mockAdapter->expects($this->any())->method('getFeatures')->will($this->returnValue(array(My_Feature::HEALTHCHECK_PRINTIQ)));
        My_Feature::setAdapter($mockAdapter);

        $defaultSettings = $this->getMock('Healthcheck_Model_Healthcheck_Setting');

        return new Healthcheck_Form_Healthcheck_Settings($defaultSettings);
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_HealthcheckFormSettingsTest.xml");

    }

    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_HealthcheckFormSettingsTest.xml");

    }

    /**
     * Tests that the customer fields exist when the feature is allowed
     */
    public function testCustomerFieldsExists ()
    {
        $this->assertInstanceOf("Zend_Form_Element_Text", $this->getForm()->getElement('customerMonochromeCostPerPage'));
        $this->assertInstanceOf("Zend_Form_Element_Text", $this->getForm()->getElement('customerColorCostPerPage'));
    }
}