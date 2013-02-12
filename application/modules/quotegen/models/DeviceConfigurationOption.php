<?php
class Quotegen_Model_DeviceConfigurationOption extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $deviceConfigurationId = 0;

    /**
     * @var int
     */
    public $optionId = 0;

    /**
     * @var int
     */
    public $quantity = 0;

    /**
     * @var int
     */
    public $includedQuantity = 0;

    /**
     * The option associated with this configuration
     *
     * @var Quotegen_Model_Option
     */
    protected $_option;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->deviceConfigurationId) && !is_null($params->deviceConfigurationId))
        {
            $this->deviceConfigurationId = $params->deviceConfigurationId;
        }

        if (isset($params->optionId) && !is_null($params->optionId))
        {
            $this->optionId = $params->optionId;
        }

        if (isset($params->quantity) && !is_null($params->quantity))
        {
            $this->quantity = $params->quantity;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "deviceConfigurationId" => $this->deviceConfigurationId,
            "optionId"              => $this->optionId,
            "quantity"              => $this->quantity
        );
    }

    /**
     * Gets the option associated with the device configuration option
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
     * Sets the option associated with the device configuration option
     *
     * @param Quotegen_Model_Option $_option
     */
    public function setOption ($_option)
    {
        $this->_option = $_option;

        return $this;
    }
}