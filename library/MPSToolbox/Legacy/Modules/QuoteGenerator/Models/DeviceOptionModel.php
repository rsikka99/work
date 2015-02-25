<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceConfigurationOptionMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\OptionMapper;
use My_Model_Abstract;

/**
 * Class DeviceOptionModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class DeviceOptionModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var int
     */
    public $optionId;

    /**
     * @var int
     */
    public $includedQuantity;

    /**
     * The option associated with this device option
     *
     * @var OptionModel
     */
    protected $_option;

    /**
     * The device configuration option
     *
     * @var DeviceConfigurationOptionModel
     */
    protected $_deviceConfigurationOption;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->optionId) && !is_null($params->optionId))
        {
            $this->optionId = $params->optionId;
        }

        if (isset($params->includedQuantity) && !is_null($params->includedQuantity))
        {
            $this->includedQuantity = $params->includedQuantity;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "masterDeviceId"   => $this->masterDeviceId,
            "dealerId"         => $this->dealerId,
            "optionId"         => $this->optionId,
            "includedQuantity" => $this->includedQuantity,
        ];
    }

    /**
     * Gets the option
     *
     * @return OptionModel
     */
    public function getOption ()
    {
        if (!isset($this->_option))
        {
            $this->_option = OptionMapper::getInstance()->find($this->optionId);
        }

        return $this->_option;
    }

    /**
     * Sets the option
     *
     * @param OptionModel $_option
     *            The new option
     *
     * @return DeviceOptionModel
     */
    public function setOption ($_option)
    {
        $this->_option = $_option;

        return $this;
    }

    /**
     * Gets the device configuration option
     *
     * @param $deviceConfigurationId
     *
     * @return DeviceConfigurationOptionModel
     */
    public function getDeviceConfigurationOption ($deviceConfigurationId)
    {
        if (!isset($this->_deviceConfigurationOption))
        {
            $where                            = "deviceConfigurationId = {$deviceConfigurationId} AND optionId = {$this->optionId}";
            $this->_deviceConfigurationOption = DeviceConfigurationOptionMapper::getInstance()->fetch($where);

            if (!isset($this->_deviceConfigurationOption))
            {
                $this->_deviceConfigurationOption = new DeviceConfigurationOptionModel();
            }
        }

        return $this->_deviceConfigurationOption;
    }

    /**
     * Sets the device configuration option
     *
     * @param DeviceConfigurationOptionModel $_deviceConfigurationOption
     *            The new device configuration option
     *
     * @return DeviceOptionModel
     */
    public function setDeviceConfigurationOption ($_deviceConfigurationOption)
    {
        $this->_deviceConfigurationOption = $_deviceConfigurationOption;

        return $this;
    }
}