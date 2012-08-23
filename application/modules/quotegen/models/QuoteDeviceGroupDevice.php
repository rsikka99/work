<?php

/**
 * Quotegen_Model_QuoteDeviceGroupDevice
 *
 * @author Shawn Wilder
 *        
 */
class Quotegen_Model_QuoteDeviceGroupDevice extends My_Model_Abstract
{
    /**
     * Foreign key that is associated with `qgen_quote_devices`
     *
     * @var int
     */
    protected $_quoteDeviceId;
    
    /**
     * Foreign key that is associated with `qgen_quote_device_groups`
     *
     * @var int
     */
    protected $_quoteDeviceGroupId;
    
    /**
     * Quantity of devices in group
     *
     * @var int
     */
    protected $_quantity;
    
    /**
     * The quote device
     *
     * @var Quotegen_Model_QuoteDevice
     */
    protected $_quoteDevice;
    
    /**
     * The quote device group
     *
     * @var Quotegen_Model_QuoteDeviceGroup
     */
    protected $_quoteDeviceGroup;
    
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
        if (isset($params->quoteDeviceGroupId) && ! is_null($params->quoteDeviceGroupId))
            $this->setQuoteDeviceGroupId($params->quoteDeviceGroupId);
        if (isset($params->quantity) && ! is_null($params->quantity))
            $this->setQuantity($params->quantity);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'quoteDeviceId' => $this->getQuoteDeviceId(), 
                'quoteDeviceGroupId' => $this->getQuoteDeviceGroupId(), 
                'quantity' => $this->getQuantity() 
        );
    }

    /**
     * Gets the quoteDeviceId
     *
     * @return the quote device id of the objevt
     */
    public function getQuoteDeviceId ()
    {
        return $this->_quoteDeviceId;
    }

    /**
     * Sets the quoteDevice idof the object
     *
     * @param number $_quoteDeviceId
     *            the new quoteDeviceID
     */
    public function setQuoteDeviceId ($_quoteDeviceId)
    {
        $this->_quoteDeviceId = $_quoteDeviceId;
        return $this;
    }

    /**
     * Gets the quoteDeviceGroupId of the object
     *
     * @return the qouteDeviceGroupId of the object
     */
    public function getQuoteDeviceGroupId ()
    {
        return $this->_quoteDeviceGroupId;
    }

    /**
     * Sets a new qouteDeviceGroupId for the object
     *
     * @param int $_quoteDeviceGroupId
     *            the new quoteDeviceGroupId
     */
    public function setQuoteDeviceGroupId ($_quoteDeviceGroupId)
    {
        $this->_quoteDeviceGroupId = $_quoteDeviceGroupId;
        return $this;
    }

    /**
     * Gets the amount of devices attached to the group
     *
     * @return the $_quantity of the devices attached to the group
     */
    public function getQuantity ()
    {
        return $this->_quantity;
    }

    /**
     * Sets a new quantity for the qouteDeviceGroupId and qouteDeviceId
     *
     * @param int $_quantity
     *            the new quantity
     */
    public function setQuantity ($_quantity)
    {
        $this->_quantity = $_quantity;
        return $this;
    }

    /**
     * Gets the quote device
     * 
     * @return Quotegen_Model_QuoteDevice
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
     * Sets the quote device
     * 
     * @param Quotegen_Model_QuoteDevice $_quoteDevice            
     */
    public function setQuoteDevice ($_quoteDevice)
    {
        $this->_quoteDevice = $_quoteDevice;
        return $this;
    }

    /**
     * Gets the quote device group
     * 
     * @return Quotegen_Model_QuoteDeviceGroup
     */
    public function getQuoteDeviceGroup ()
    {
        if (! isset($this->_quoteDeviceGroup))
        {
            $this->_quoteDeviceGroup = Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->find($this->getQuoteDeviceGroupId());
        }
        return $this->_quoteDeviceGroup;
    }

    /**
     * Sets the quote device group
     * 
     * @param Quotegen_Model_QuoteDeviceGroup $_quoteDeviceGroup            
     */
    public function setQuoteDeviceGroup ($_quoteDeviceGroup)
    {
        $this->_quoteDeviceGroup = $_quoteDeviceGroup;
        return $this;
    }
}
