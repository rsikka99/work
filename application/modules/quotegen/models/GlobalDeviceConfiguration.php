<?php

/**
 * Quotegen_Model_GlobalDeviceConfiguration
 *
 * @author Shawn Wilder
 *        
 */
class Quotegen_Model_GlobalDeviceConfiguration extends My_Model_Abstract
{
    
    /**
     * The device configuration id
     *
     * @var int
     */
    protected $_deviceConfigurationId = 0;
    
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
            $this->setId($params->deviceConfigurationId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'deviceConfigurationId' => $this->getDeviceConfigurationId() 
        );
    }

    /**
     * Gets the id of the object
     *
     * @return number The id of the object
     */
    public function getDeviceConfigurationId ()
    {
        return $this->_deviceConfigurationId;
    }

    /**
     * Sets the id of the object
     *
     * @param number $_id
     *            the new id
     */
    public function setDeviceConfigurationId ($_deviceConfigurationId)
    {
        $this->_deviceConfigurationId = $_deviceConfigurationId;
        return $this;
    }
}
