<?php

/**
 * Quotegen_Model_DeviceConfigurationOption
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_DeviceConfigurationOption extends My_Model_Abstract
{
    
    /**
     * The device configuration id
     *
     * @var int
     */
    protected $_deviceConfigurationId = 0;
    
    /**
     * The option id
     *
     * @var int
     */
    protected $_optionId = 0;
    
    /**
     * The quantity in the configuration.
     * Defaults to 0
     *
     * @var int
     */
    protected $_quantity = 0;
    
    /**
     * The included quantity in the configuration.
     * Defaults to 0
     *
     * @var int
     */
    protected $_includedQuantity = 0;

    /**
     * The option associated with this configuration
     *
     * @var Quotegen_Model_Option
     */
    protected $_option;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->deviceConfigurationId) && ! is_null($params->deviceConfigurationId))
            $this->setDeviceConfigurationId($params->deviceConfigurationId);
        if (isset($params->optionId) && ! is_null($params->optionId))
            $this->setOptionId($params->optionId);
        if (isset($params->quantity) && ! is_null($params->quantity))
            $this->setQuantity($params->quantity);
        if (isset($params->includedQuantity) && ! is_null($params->includedQuantity))
            $this->setIncludedQuantity($params->includedQuantity);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'deviceConfigurationId' => $this->getDeviceConfigurationId(), 
                'optionId' => $this->getOptionId(), 
                'quantity' => $this->getQuantity(),
                'includedQuantity' => $this->getIncludedQuantity()
        );
    }

    /**
     * Gets the device configuration id
     *
     * @return number The device configuration id
     */
    public function getDeviceConfigurationId ()
    {
        return $this->_deviceConfigurationId;
    }

    /**
     * Sets a new device configuration id
     *
     * @param number $_deviceConfigurationId
     *            The new id
     */
    public function setDeviceConfigurationId ($_deviceConfigurationId)
    {
        $this->_deviceConfigurationId = $_deviceConfigurationId;
        return $this;
    }

    /**
     * Gets the option id
     *
     * @return number The option id
     */
    public function getOptionId ()
    {
        return $this->_optionId;
    }

    /**
     * Sets a new option id
     *
     * @param number $_optionId
     *            The id
     */
    public function setOptionId ($_optionId)
    {
        $this->_optionId = $_optionId;
        return $this;
    }

    /**
     * Gets the quantity
     *
     * @return number The quantity
     */
    public function getQuantity ()
    {
        return $this->_quantity;
    }

    /**
     * Sets a new quantity
     *
     * @param number $_quantity
     *            The new quantity
     */
    public function setQuantity ($_quantity)
    {
        $this->_quantity = $_quantity;
        return $this;
    }

    /**
     * Gets the quantity of this item that is included in the price of the device/device configuration
     * 
     * @return number The quantity
     */
    public function getIncludedQuantity ()
    {
        return $this->_includedQuantity;
    }

    /**
     * Sets the quantity of this item that is included in the price of the device/device configuration
     * 
     * @param number $_includedQuantity
     *            The new quantity
     */
    public function setIncludedQuantity ($_includedQuantity)
    {
        $this->_includedQuantity = $_includedQuantity;
        return $this;
    }

    /**
     * Gets the option associated with the device configuration option
     *
     * @return Quotegen_Model_Option
     */
    public function getOption ()
    {
        if (! isset($this->_option))
        {
        	$this->_option = Quotegen_Model_Mapper_Option::getInstance()->find($this->getOptionId());
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
