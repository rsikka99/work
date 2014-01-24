<?php

/**
 * Class Quotegen_Model_DeviceOption
 */
class Quotegen_Model_DeviceOption extends My_Model_Abstract
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
     * @var Quotegen_Model_Option
     */
    protected $_option;

    /**
     * The device configuration option
     *
     * @var Quotegen_Model_DeviceConfigurationOption
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
        return array(
            "masterDeviceId"   => $this->masterDeviceId,
            "dealerId"         => $this->dealerId,
            "optionId"         => $this->optionId,
            "includedQuantity" => $this->includedQuantity,
        );
    }

    /**
     * Gets the option
     *
     * @return Quotegen_Model_Option
     */
    public function getOption ()
    {
        if (!isset($this->_option))
        {
            $this->_option = Quotegen_Model_Mapper_Option::getInstance()->find($this->optionId);
        }

        return $this->_option;
    }

    /**
     * Sets the option
     *
     * @param Quotegen_Model_Option $_option
     *            The new option
     *
     * @return Quotegen_Model_DeviceOption
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
     * @return Quotegen_Model_DeviceConfigurationOption
     */
    public function getDeviceConfigurationOption ($deviceConfigurationId)
    {
        if (!isset($this->_deviceConfigurationOption))
        {
            $where                            = "deviceConfigurationId = {$deviceConfigurationId} AND optionId = {$this->optionId}";
            $this->_deviceConfigurationOption = Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->fetch($where);

            if (!isset($this->_deviceConfigurationOption))
            {
                $this->_deviceConfigurationOption = new Quotegen_Model_DeviceConfigurationOption();
            }
        }

        return $this->_deviceConfigurationOption;
    }

    /**
     * Sets the device configuration option
     *
     * @param Quotegen_Model_DeviceConfigurationOption $_deviceConfigurationOption
     *            The new device configuration option
     *
     * @return Quotegen_Model_DeviceOption
     */
    public function setDeviceConfigurationOption ($_deviceConfigurationOption)
    {
        $this->_deviceConfigurationOption = $_deviceConfigurationOption;

        return $this;
    }
}