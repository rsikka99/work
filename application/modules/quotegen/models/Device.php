<?php

/**
 * Quotegen_Model_Device is a model that represents a user row in the database.
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_Device extends My_Model_Abstract
{
    
    /**
     * The master device id that the object is linked to
     *
     * @var int
     */
    protected $_masterDeviceId;
    
    /**
     * The sku of the object.
     *
     * @var string
     */
    protected $_oemSku;
    
    /**
     * the dealer sku for the object
     *
     * @var string
     */
    protected $_dealerSku;
    
    /**
     * The description of the standard features
     *
     * @var description
     */
    protected $_description;
    
    /**
     * The master device object
     *
     * @var Proposalgen_Model_MasterDevice
     */
    protected $_masterDevice;
    
    /**
     * The options added to the device
     *
     * @var multitype: Quotegen_Model_Option
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
        if (isset($params->masterDeviceId) && ! is_null($params->masterDeviceId))
            $this->setMasterDeviceId($params->masterDeviceId);
        if (isset($params->oemSku) && ! is_null($params->oemSku))
            $this->setOemSku($params->oemSku);
        if (isset($params->dealerSku) && ! is_null($params->dealerSku))
            $this->setDealerSku($params->dealerSku);
        if (isset($params->description) && ! is_null($params->description))
            $this->setDescription($params->description);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'masterDeviceId' => $this->getMasterDeviceId(), 
                'oemSku' => $this->getOemSku(), 
                'dealerSku' => $this->getDealerSku(), 
                'description' => $this->getDescription() 
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
     * Gets the sku of the device
     *
     * @return string The sku of the device
     */
    public function getOemSku ()
    {
        return $this->_oemSku;
    }

    /**
     * Sets a new sku for the device
     *
     * @param string $_sku
     *            The new sku to set
     */
    public function setOemSku ($_sku)
    {
        $this->_oemSku = $_sku;
        return $this;
    }

    /**
     * Gets the current sku of the item
     *
     * @return string
     */
    public function getDealerSku ()
    {
        return $this->_dealerSku;
    }

    /**
     * Sets a new sku
     *
     * @param string $_dealerSku
     *            The new value
     */
    public function setDealerSku ($_dealerSku)
    {
        $this->_dealerSku = $_dealerSku;
        return $this;
    }

    /**
     * Gets the description of the standard features
     *
     * @return string The description
     */
    public function getDescription ()
    {
        return $this->_description;
    }

    /**
     * Sets a new description for the standard features
     *
     * @param description $_description
     *            The new description
     */
    public function setDescription ($_description)
    {
        $this->_description = $_description;
        return $this;
    }

    /**
     * Gets the master device object associated with this device
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function getMasterDevice ()
    {
        if (! isset($this->_masterDevice))
        {
            $this->_masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($this->getMasterDeviceId());
        }
        return $this->_masterDevice;
    }

    /**
     * Sets the master device object associated with this device
     *
     * @param Proposalgen_Model_MasterDevice $_masterDevice
     *            The new master device
     */
    public function setMasterDevice ($_masterDevice)
    {
        $this->_masterDevice = $_masterDevice;
        return $this;
    }

    /**
     * Get the array of options for the device
     *
     * @return multitype:Quotegen_Model_DeviceOption The array of options
     */
    public function getDeviceOptions ()
    {
        if (! isset($this->_options))
        {
            $this->_options = Quotegen_Model_Mapper_Option::getInstance()->fetchAllDeviceOptionsForDevice($this->getMasterDeviceId());
        }
        return $this->_options;
    }

    /**
     * Set a new array of options for the device
     *
     * @param multitype: $_options            
     */
    public function setOptions ($_options)
    {
        $this->_options = $_options;
        return $this;
    }
}
