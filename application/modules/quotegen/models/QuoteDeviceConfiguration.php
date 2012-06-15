<?php

/**
 * Quotegen_Model_QuoteDeviceConfiguration
 *
 * @author Shawn Wilder
 *        
 */
class Quotegen_Model_QuoteDeviceConfiguration extends My_Model_Abstract
{
    /**
     * The quote device id
     * 
     * @var int
     */
    protected $_quoteDeviceId;
    
    /**
     * The device configuration id
     *  
     * @var int
     */
    protected $_deviceConfigurationId;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->quoteDeviceId) && ! is_null($params->quoteDeviceId))
            $this->setId($params->quoteDeviceId);
        if (isset($params->deviceConfigurationId) && ! is_null($params->deviceConfigurationId))
            $this->setId($params->deviceConfigurationId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'quoteDeviceId' => $this->getQuoteDeviceId(), 
                'deviceConfigurationId' => $this->getDeviceConfigurationId()
        );
    }
    
	/**
	 * Gets the quoteDeviceId
	 * 
     * @return the $_quoteDeviceId
     */
    public function getQuoteDeviceId ()
    {
        return $this->_quoteDeviceId;
    }

	/**
	 * Sets the new quote device Id
	 * 
     * @param int $_quoteDeviceId the new quote device id
     */
    public function setQuoteDeviceId ($_quoteDeviceId)
    {
        $this->_quoteDeviceId = $_quoteDeviceId;
        return $this;
    }

	/**
	 * Gets the device configuration id
	 * 
     * @return the $_deviceConfigurationId
     */
    public function getDeviceConfigurationId ()
    {
        return $this->_deviceConfigurationId;
    }

	/**
	 * Sets the new device configuration id
	 * 
     * @param int $_deviceConfigurationId the new device configuration id
     */
    public function setDeviceConfigurationId ($_deviceConfigurationId)
    {
        $this->_deviceConfigurationId = $_deviceConfigurationId;
        return $this;
    }    
}
