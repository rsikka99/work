<?php

/**
 * Quotegen_Model_UserDeviceConfiguration
 *
 * @author Shawn Wilder
 *        
 */
class Quotegen_Model_UserDeviceConfiguration extends My_Model_Abstract
{
    /**
     * The device configuration id
     *
     * @var int
     */
    protected $_deviceConfigurationId;
    
    /**
     * The user id
     *
     * @var int
     */
    protected $_userId;
    

    
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
        if (isset($params->userId) && ! is_null($params->userId))
            $this->setUserId($params->userId);

    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'userId' => $this->getUserId(), 
        );
    }

    /**
     * Gets the device configuration id of the object
     *
     * @return the $_deviceConfigurationId
     */
    public function getDeviceConfigurationId ()
    {
        return $this->_deviceConfigurationId;
    }

    /**
     * Sets a new device configuration id for the object
     *
     * @param number $_deviceConfigurationId
     *            the new device configuration to be set
     */
    public function setDeviceConfigurationId ($_deviceConfigurationId)
    {
        $this->_deviceConfigurationId = $_deviceConfigurationId;
        return $this;
    }

    /**
     * Get the userId of the object
     *
     * @return the $_userId
     */
    public function getUserId ()
    {
        return $this->_userId;
    }

    /**
     * Sets the new userId of the object
     *
     * @param number $_userId
     *            The new objects userId
     */
    public function setUserId ($_userId)
    {
        $this->_userId = $_userId;
        return $this;
    }


}
