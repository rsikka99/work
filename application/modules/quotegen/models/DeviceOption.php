<?php

/**
 * Quotegen_Model_DeviceOption
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_DeviceOption extends My_Model_Abstract
{
    
    /**
     * The master device id (quote device id)
     *
     * @var int
     */
    protected $_masterDeviceId;
    
    /**
     * The option id
     *
     * @var string
     */
    protected $_optionId;

    /**
     * The quanity of the item that is included
     *
     * @var int
     */
    protected $_includedQuantity;
    
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
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->masterDeviceId) && ! is_null($params->masterDeviceId))
            $this->setMasterDeviceId($params->masterDeviceId);
        if (isset($params->optionId) && ! is_null($params->optionId))
            $this->setOptionId($params->optionId);
        if (isset($params->includedQuantity) && ! is_null($params->includedQuantity))
            $this->setIncludedQuantity($params->includedQuantity);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'masterDeviceId' => $this->getMasterDeviceId(), 
                'optionId' => $this->getOptionId(), 
                'includedQuantity' => $this->getIncludedQuantity() 
        );
    }

    /**
     * Gets the master device id of the object (Which is also the primary key for this object)
     *
     * @return number
     */
    public function getMasterDeviceId ()
    {
        return $this->_masterDeviceId;
    }

    /**
     * Sets a new master device id for the object
     *
     * @param number $_masterDeviceId
     *            The new master device id to set
     */
    public function setMasterDeviceId ($_masterDeviceId)
    {
        $this->_masterDeviceId = $_masterDeviceId;
        return $this;
    }

    /**
     * Gets the optionId of the device
     *
     * @return string The optionId of the device
     */
    public function getOptionId ()
    {
        return $this->_optionId;
    }

    /**
     * Sets a new optionId for the device
     *
     * @param string $_optionId
     *            The new optionId to set
     */
    public function setOptionId ($_optionId)
    {
        $this->_optionId = $_optionId;
        return $this;
    }

    /**
     * Gets the included quantity of the option
     *
     * @return the $_includedQuantity the new quantity
     */
    public function getIncludedQuantity ()
    {
        return $this->_includedQuantity;
    }

    /**
     * Sets the included quantity
     *
     * @param number $_includedQuantity
     *            the new included quantity
     */
    public function setIncludedQuantity ($_includedQuantity)
    {
        $this->_includedQuantity = $_includedQuantity;
        return $this;
    }

    /**
     * Gets the option
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
     * Sets the option
     *
     * @param Quotegen_Model_Option $_option
     *            The new option
     */
    public function setOption ($_option)
    {
        $this->_option = $_option;
        return $this;
    }

    /**
     * Gets the device configuration option
     *
     * @return Quotegen_Model_DeviceConfigurationOption
     */
    public function getDeviceConfigurationOption ($deviceConfigurationId)
    {
        if (! isset($this->_deviceConfigurationOption))
        {
            $where = "deviceConfigurationId = {$deviceConfigurationId} AND optionId = {$this->getOptionId()}";
            $this->_deviceConfigurationOption = Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->fetch($where);
            
            if (! isset( $this->_deviceConfigurationOption))
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
     */
    public function setDeviceConfigurationOption ($_deviceConfigurationOption)
    {
        $this->_deviceConfigurationOption = $_deviceConfigurationOption;
        return $this;
    }
}
