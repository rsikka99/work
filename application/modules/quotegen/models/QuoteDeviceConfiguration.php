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
     * The device id
     *
     * @var int
     */
    protected $_masterDeviceId;
    
    /**
     * The device
     *
     * @var Quotegen_Model_Device
     */
    protected $_device;
    
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
        if (isset($params->masterDeviceId) && ! is_null($params->masterDeviceId))
            $this->setMasterDeviceId($params->masterDeviceId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'quoteDeviceId' => $this->getQuoteDeviceId(), 
                'masterDeviceId' => $this->getMasterDeviceId() 
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
     * Gets the device id
     *
     * @return number
     */
    public function getMasterDeviceId ()
    {
        return $this->_masterDeviceId;
    }

    /**
     * Sets the new device id
     *
     * @param int $_deviceId
     *            The new device id.
     */
    public function setMasterDeviceId ($_deviceId)
    {
        $this->_masterDeviceId = $_deviceId;
        return $this;
    }

    /**
     * Gets the associated device
     *
     * @return Quotegen_Model_Device The device.
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
     * Sets the associated device
     *
     * @param Quotegen_Model_Device $_device
     *            The device.
     */
    public function setDevice ($_device)
    {
        $this->_device = $_device;
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
