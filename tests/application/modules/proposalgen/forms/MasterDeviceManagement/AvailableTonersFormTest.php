<?php

use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\AvailableTonersForm;

class Proposalgen_Form_MasterDeviceManagement_AvailableTonersFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    public $fixtures = ['manufacturers','toner_colors'];

    /**
     * @return AvailableTonersForm
     */
    public function getForm ()
    {
        return new AvailableTonersForm();
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

