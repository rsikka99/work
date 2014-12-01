<?php

/**
 * Class Quotegen_Form_DeviceSetupTest
 */
class Quotegen_Form_DeviceSetupTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return Quotegen_Form_DeviceSetup
     */
    public function getForm ()
    {
        return new Quotegen_Form_DeviceSetup();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_DeviceSetupTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_DeviceSetupTest.xml");
    }
}