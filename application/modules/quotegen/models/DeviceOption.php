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
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'masterDeviceId' => $this->getMasterDeviceId(), 
                'optionId' => $this->getOptionId() 
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
}
