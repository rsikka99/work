<?php

class Proposalgen_Form_MasterDeviceManagement_HardwareConfigurationsFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @var Proposalgen_Form_MasterDeviceManagement_HardwareConfigurations
     */
    protected $_form;

    /**
     * @return Proposalgen_Form_MasterDeviceManagement_HardwareConfigurations
     */
    public function getForm ()
    {
        $this->buildForm();

        return $this->_form;
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_hardwareConfigurationsFormTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_hardwareConfigurationsFormTest.xml");
    }

    /**
     * Builds the form to be used for testing
     *
     * @param int $deviceConfigurationId
     * @param int $masterDeviceId
     *
     */
    public function buildForm ($deviceConfigurationId = 1, $masterDeviceId = 1)
    {
        $data = $this->getGoodData();
        $this->setupDeviceOptions($data[0]);

        $this->_form = new Proposalgen_Form_MasterDeviceManagement_HardwareConfigurations($deviceConfigurationId, $masterDeviceId);
    }

    public function setupDeviceOptions ($quantity = null)
    {
        if ($quantity != null)
        {
            $identity           = Zend_Auth::getInstance()->getStorage();
            $stdClass           = new stdClass();
            $stdClass->dealerId = 1;
            $stdClass->id       = 1;
            $identity->write($stdClass);

            $deviceOption                   = new Quotegen_Model_DeviceOption();
            $deviceOption->masterDeviceId   = 1;
            $deviceOption->dealerId         = 1;
            $deviceOption->optionId         = 1;
            $deviceOption->includedQuantity = $quantity;

            $device                 = new Quotegen_Model_Device();
            $device->masterDeviceId = 1;
            $device->dealerId       = 1;
            $device->setOptions(array($deviceOption));

            $deviceConfigurationOption                        = new Quotegen_Model_DeviceConfigurationOption();
            $deviceConfigurationOption->quantity              = $quantity;
            $deviceConfigurationOption->optionId              = 1;
            $deviceConfigurationOption->deviceConfigurationId = 1;

            $option           = new Quotegen_Model_Option();
            $option->dealerId = 1;
            $option->id       = 1;

            $deviceConfigurationOption->setOption($option);
            Quotegen_Model_Mapper_Option::getInstance()->saveItemToCache($option);
            Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->saveItemToCache($deviceConfigurationOption);
            Quotegen_Model_Mapper_DeviceOption::getInstance()->saveItemToCache($deviceOption);
            Quotegen_Model_Mapper_Device::getInstance()->saveItemToCache($device);
        }
    }
}

