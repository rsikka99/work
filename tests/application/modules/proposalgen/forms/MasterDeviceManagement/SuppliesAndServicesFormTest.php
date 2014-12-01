<?php

class Proposalgen_Form_MasterDeviceManagement_SuppliesAndServicesFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @return Proposalgen_Form_MasterDeviceManagement_SuppliesAndService
     */
    public function getForm ()
    {
        return new Proposalgen_Form_MasterDeviceManagement_SuppliesAndService();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_suppliesAndServicesFormTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_suppliesAndServicesFormTest.xml");
    }

}

