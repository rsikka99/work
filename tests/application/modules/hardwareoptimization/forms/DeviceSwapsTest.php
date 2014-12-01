<?php

/**
 * Class Hardwareoptimization_Form_DeviceSwapsTest
 */
class Hardwareoptimization_Form_DeviceSwapsTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * Gets the form to be used in the test
     *
     * @return Hardwareoptimization_Form_DeviceSwaps|Zend_Form
     */
    public function getForm ()
    {
        return new Hardwareoptimization_Form_DeviceSwaps();
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_deviceSwapsSettingTest.xml");
    }

    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_deviceSwapsSettingTest.xml");
    }
}