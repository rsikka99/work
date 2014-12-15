<?php

use MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement\HardwareConfigurationsForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationOptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceOptionModel;

class Proposalgen_Form_MasterDeviceManagement_HardwareConfigurationsFormTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * @var HardwareConfigurationsForm
     */
    protected $_form;

    /**
     * @return HardwareConfigurationsForm
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

        $this->_form = new HardwareConfigurationsForm($deviceConfigurationId, $masterDeviceId);
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

            $deviceOption                   = new DeviceOptionModel();
            $deviceOption->masterDeviceId   = 1;
            $deviceOption->dealerId         = 1;
            $deviceOption->optionId         = 1;
            $deviceOption->includedQuantity = $quantity;

            $device                 = new DeviceModel();
            $device->masterDeviceId = 1;
            $device->dealerId       = 1;
            $device->setOptions(array($deviceOption));

            $deviceConfigurationOption                        = new DeviceConfigurationOptionModel();
            $deviceConfigurationOption->quantity              = $quantity;
            $deviceConfigurationOption->optionId              = 1;
            $deviceConfigurationOption->deviceConfigurationId = 1;

            $option           = new OptionModel();
            $option->dealerId = 1;
            $option->id       = 1;

            $deviceConfigurationOption->setOption($option);
            OptionMapper::getInstance()->saveItemToCache($option);
            DeviceConfigurationOptionMapper::getInstance()->saveItemToCache($deviceConfigurationOption);
            DeviceOptionMapper::getInstance()->saveItemToCache($deviceOption);
            DeviceMapper::getInstance()->saveItemToCache($device);
        }
    }
}

