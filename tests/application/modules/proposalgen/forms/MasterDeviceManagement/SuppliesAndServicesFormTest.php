<?php

use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\SuppliesAndServiceForm;

class Proposalgen_Form_MasterDeviceManagement_SuppliesAndServicesFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    public $fixtures = ['toner_configs'];

    /**
     * @return SuppliesAndServiceForm
     */
    public function getForm ()
    {
        return new SuppliesAndServiceForm();
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

