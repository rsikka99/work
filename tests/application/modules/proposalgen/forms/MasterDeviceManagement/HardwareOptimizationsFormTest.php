<?php

use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HardwareOptimizationForm;

class Proposalgen_Form_MasterDeviceManagement_HardwareOptimizationsFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase

{
    /**
     * @return HardwareOptimizationForm
     */
    public function getForm ()
    {
        return new HardwareOptimizationForm();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_hardwareOptimizationsFormTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_hardwareOptimizationsFormTest.xml");
    }
}

