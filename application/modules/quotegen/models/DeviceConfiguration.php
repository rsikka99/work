<?php

/**
 * Quotegen_Model_DeviceConfiguration
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_DeviceConfiguration extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The device id (quotegen device, but uses masterDeviceId as the id)
     *
     * @var int
     */
    protected $_masterDeviceId = 0;
    
    /**
     * The quote device associated with this configuration
     *
     * @var Quotegen_Model_Device
     */
    protected $_device;
    
    /**
     * The options added to the configuraiton
     *
     * @var multitype: Quotegen_Model_DeviceOption
     */
    protected $_options;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->id) && ! is_null($params->id))
            $this->setId($params->id);
        if (isset($params->masterDeviceId) && ! is_null($params->masterDeviceId))
            $this->setMasterDeviceId($params->masterDeviceId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'masterDeviceId' => $this->getMasterDeviceId() 
        );
    }

    /**
     * Gets the id of the object
     *
     * @return number The id of the object
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Sets the id of the object
     *
     * @param number $_id
     *            the new id
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
    }

    /**
     * Gets the master device id
     *
     * @return number
     */
    public function getMasterDeviceId ()
    {
        return $this->_masterDeviceId;
    }

    /**
     * Sets a new master device id
     *
     * @param number $_masterDeviceId
     *            The new id
     */
    public function setMasterDeviceId ($_masterDeviceId)
    {
        $this->_masterDeviceId = $_masterDeviceId;
        return $this;
    }

    /**
     * Gets the quote device associated with this configuration
     *
     * @return Quotegen_Model_Device
     */
    public function getDevice ()
    {
        if (! isset($this->_device))
        {
            $this->_device = Quotegen_Model_Mapper_Device::getInstance()->find($this->getMasterDeviceId());
        }
        return $this->_device;
    }

    /**
     * Sets the quote device associated with this configuration
     *
     * @param Quotegen_Model_Device $_device            
     */
    public function setDevice ($_device)
    {
        $this->_device = $_device;
        return $this;
    }

    /**
     * Get the array of options for the device
     *
     * @return multitype:Quotegen_Model_DeviceOption The array of options
     */
    public function getOptions ()
    {
        if (! isset($this->_options))
        {
            $this->_options = Quotegen_Model_Mapper_Option::getInstance()->fetchAllOptionsForDeviceConfiguration($this->getId());
        }
        return $this->_options;
    }

    /**
     * Set a new array of options for the device
     *
     * @param multitype:Quotegen_Model_DeviceOption $_options            
     */
    public function setOptions ($_options)
    {
        $this->_options = $_options;
        return $this;
    }
}
