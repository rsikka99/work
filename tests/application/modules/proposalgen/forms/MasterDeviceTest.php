<?php

/**
 * Class Proposalgen_Form_MasterDeviceTest
 */
class Proposalgen_Form_MasterDeviceTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    /**
     * @return Proposalgen_Form_MasterDevice
     */
    public function getForm ()
    {
        return new Proposalgen_Form_MasterDevice();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_masterDeviceTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_masterDeviceTest.xml");
    }
}
