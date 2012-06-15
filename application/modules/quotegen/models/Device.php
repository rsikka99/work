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
    protected $_sku;
    
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
        if (isset($params->sku) && ! is_null($params->sku))
            $this->setSku($params->sku);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'masterDeviceId' => $this->getMasterDeviceId(), 
                'sku' => $this->getSku() 
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
    public function getSku ()
    {
        return $this->_sku;
    }

    /**
     * Sets a new sku for the device
     *
     * @param string $_sku
     *            The new sku to set
     */
    public function setSku ($_sku)
    {
        $this->_sku = $_sku;
        return $this;
    }
}
