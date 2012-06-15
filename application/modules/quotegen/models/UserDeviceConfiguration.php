<?php

/**
 * Quotegen_Model_UserDeviceConfiguation
 *
 * @author Shawn Wilder
 *        
 */
class Quotegen_Model_UserDeviceConfiguation extends My_Model_Abstract
{
    /**
     * The device configuation id
     *
     * @var int
     */
    protected $_deviceConfiguationId;
    
    /**
     * The user id
     *
     * @var int
     */
    protected $_userId;
    
    /**
     * The name of the user configuration
     *
     * @var string
     */
    protected $_name;
    
    /**
     * The description of the configuration
     *
     * @var string
     */
    protected $_description;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->deviceConfiguationId) && ! is_null($params->deviceConfiguationId))
            $this->setId($params->deviceConfiguationId);
        if (isset($params->userId) && ! is_null($params->userId))
            $this->setId($params->userId);
        if (isset($params->name) && ! is_null($params->name))
            $this->setId($params->name);
        if (isset($params->description) && ! is_null($params->description))
            $this->setId($params->description);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'userId' => $this->getUserId(), 
                'name' => $this->getName(), 
                'description' => $this->getDescription() 
        );
    }

    /**
     * Gets the device configuation id of the object
     *
     * @return the $_deviceConfiguationId
     */
    public function getDeviceConfiguationId ()
    {
        return $this->_deviceConfiguationId;
    }

    /**
     * Sets a new device configuation id for the object
     *
     * @param number $_deviceConfiguationId
     *            the new device configuration to be set
     */
    public function setDeviceConfiguationId ($_deviceConfiguationId)
    {
        $this->_deviceConfiguationId = $_deviceConfiguationId;
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

    /**
     * Get the name of the device configuration
     *
     * @return the $_name
     *         The name of the device configuration
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Sets the new name of the object
     *
     * @param string $_name
     *            the new name
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
        return $this;
    }

    /**
     * Get the description of the device configuration
     *
     * @return the $_description
     */
    public function getDescription ()
    {
        return $this->_description;
    }

    /**
     * Gets the description of the object
     *
     * @param string $_description
     *            the new description
     */
    public function setDescription ($_description)
    {
        $this->_description = $_description;
        return $this;
    }
}
