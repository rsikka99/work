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
    
    /**
     * The device configuration
     *
     * @var Quotegen_Model_DeviceConfiguration
     */
    protected $_deviceConfiguration;
    
    /**
     * The quote device
     *
     * @var Quotegen_Model_QuoteDevice
     */
    protected $_quoteDevice;
    
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
            $this->setQuoteDeviceId($params->quoteDeviceId);
        if (isset($params->deviceConfigurationId) && ! is_null($params->deviceConfigurationId))
            $this->setDeviceConfigurationId($params->deviceConfigurationId);
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
     * @param int $_quoteDeviceId
     *            the new quote device id
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
     * @param int $_deviceConfigurationId
     *            the new device configuration id
     */
    public function setDeviceConfigurationId ($_deviceConfigurationId)
    {
        $this->_deviceConfigurationId = $_deviceConfigurationId;
        return $this;
    }

    /**
     * Gets the associated device configuration
     *
     * @return Quotegen_Model_DeviceConfiguration The device configuration
     */
    public function getDeviceConfiguration ()
    {
        if (! isset($this->_deviceConfiguration))
        {
            $this->_deviceConfiguration = Quotegen_Model_Mapper_DeviceConfiguration::getInstance()->find($this->getDeviceConfigurationId());
        }
        return $this->_deviceConfiguration;
    }

    /**
     * Sets the associated device configuration
     *
     * @param Quotegen_Model_DeviceConfiguration $_deviceConfiguration
     *            The device configuration.
     */
    public function setDeviceConfiguration ($_deviceConfiguration)
    {
        $this->_deviceConfiguration = $_deviceConfiguration;
        return $this;
    }

    /**
     * Gets the associated quote device
     *
     * @return Quotegen_Model_QuoteDevice The quote device.
     */
    public function getQuoteDevice ()
    {
        if (! isset($this->_quoteDevice))
        {
            $this->_quoteDevice = Quotegen_Model_Mapper_QuoteDevice::getInstance()->find($this->getQuoteDeviceId());
        }
        return $this->_quoteDevice;
    }

    /**
     * Sets the associated quote device
     *
     * @param Quotegen_Model_QuoteDevice $_quoteDevice
     *            The quote device.
     */
    public function setQuoteDevice ($_quoteDevice)
    {
        $this->_quoteDevice = $_quoteDevice;
        return $this;
    }
}
