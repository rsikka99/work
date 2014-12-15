<?php
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\DeviceSwapsForm;

/**
 * Class Hardwareoptimization_Form_DeviceSwapsTest
 */
class Hardwareoptimization_Form_DeviceSwapsTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @var DeviceSwapsForm
     *
     * @return DeviceSwapsForm|Zend_Form
     */
    public function getForm ()
    {
        return new DeviceSwapsForm();
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