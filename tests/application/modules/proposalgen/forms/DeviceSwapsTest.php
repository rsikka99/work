<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\DeviceSwapsForm;

/**
 * Class Proposalgen_Form_DeviceSwapTest
 */
class Proposalgen_Form_DeviceSwapTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @return DeviceSwapsForm
     */
    public function getForm ()
    {
        return new DeviceSwapsForm();
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_deviceSwapsSettingTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_deviceSwapsSettingTest.xml");
    }

}