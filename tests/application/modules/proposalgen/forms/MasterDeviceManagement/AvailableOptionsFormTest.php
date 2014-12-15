<?php

use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\AvailableOptionsForm;

class Proposalgen_Form_MasterDeviceManagement_AvailableOptionsFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase

{
    /**
     * @return AvailableOptionsForm
     */
    public function getForm ()
    {
        return new AvailableOptionsForm();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_availableOptionsFormTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_availableOptionsFormTest.xml");
    }

}

