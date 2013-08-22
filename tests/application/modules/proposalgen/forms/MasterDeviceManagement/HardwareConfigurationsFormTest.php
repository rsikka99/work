<?php

class Proposalgen_Form_MasterDeviceManagement_HardwareConfigurationsFormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Proposalgen_Form_MasterDeviceManagement_HardwareConfigurations
     */
    protected $_form;

    /**
     * Builds the form to be used for testing
     *
     * @param int $deviceConfigurationId
     * @param int $masterDeviceId
     *
     */
    public function buildForm ($deviceConfigurationId = 1, $masterDeviceId = 1)
    {
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

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function goodData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/goodData_hardwareConfigurationsFormTest.xml");
        $data = array();

        foreach ($xml->hardwareConfiguration as $row)
        {
            $row    = json_decode(json_encode($row), 1);
            $data[] = $row;
        }

        return $data;
    }

    /**
     * @dataProvider goodData
     *               Tests whether the form accepts valid data
     */
    public function testFormAcceptsValidData ($data)
    {
        $this->setupDeviceOptions($data['hardwareConfigurationsoption1']);
        $this->buildForm(1, 1);

        $this->assertTrue($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }

    /**
     * This function loads an XML file of good data into arrays to be tested in the form
     */
    public function badData ()
    {
        $xml  = simplexml_load_file(__DIR__ . "/_files/badData_hardwareConfigurationsFormTest.xml");
        $data = array();

        foreach ($xml->hardwareConfiguration as $row)
        {
            $row    = json_decode(json_encode($row), 1);
            $data[] = $row;
        }

        return $data;
    }

    /**
     * @dataProvider badData
     *               Tests if the form errors on invalid data
     */
    public function testFormRejectsBadData ($data)
    {
        $this->setupDeviceOptions($data['hardwareConfigurationsoption1']);
        $this->buildForm(1, 1);

        $this->assertFalse($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }
}

