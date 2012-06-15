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
}
