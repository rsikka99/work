<?php

class Proposalgen_Form_MasterDeviceManagement_AvailableTonersFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @return Proposalgen_Form_MasterDeviceManagement_AvailableToners
     */
    public function getForm ()
    {
        return new Proposalgen_Form_MasterDeviceManagement_AvailableToners();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_availableTonersFormTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_availableTonersFormTest.xml");
    }

}

