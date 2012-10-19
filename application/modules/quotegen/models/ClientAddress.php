<?php

/**
 * Quotegen_Model_Client
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_ClientAddress extends My_Model_Abstract
{
    
    /**
     * The clients id.
     *
     * @var int
     */
    protected $_clientId = 0;
    
    /**
     * The addresses id.
     *
     * @var string
     */
    protected $_addressId;
    
    /**
     * If this is the primary address(bool)
     *
     * @var string
     */
    protected $_primaryAddress;
    
    /**
     * The name of this address
     *
     * @var string
     */
    protected $_name;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->clientId) && ! is_null($params->clientId))
            $this->setClientId($params->clientId);
        if (isset($params->addressId) && ! is_null($params->addressId))
            $this->setAddressId($params->addressId);
        if (isset($params->primaryAddress) && ! is_null($params->primaryAddress))
            $this->setPrimaryAddress($params->primaryAddress);
        if (isset($params->name) && ! is_null($params->name))
            $this->setName($params->name);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                
                'clientId' => $this->getClientId(), 
                'addressId' => $this->getAddressId(), 
                'primaryAddress' => $this->getPrimaryAddress(), 
                'name' => $this->getName() 
        );
    }

    /**
     * Getter for $_clientId
     *
     * @return number
     */
    public function getClientId ()
    {
        return $this->_clientId;
    }

    /**
     * Getter for $_addressId
     *
     * @return string
     */
    public function getAddressId ()
    {
        return $this->_addressId;
    }

    /**
     * Getter for $_primaryAddress
     *
     * @return string
     */
    public function getPrimaryAddress ()
    {
        return $this->_primaryAddress;
    }

    /**
     * Getter for $_name
     *
     * @return string
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Setter for $_clientId
     *
     * @param number $_clientId
     *            The new value
     */
    public function setClientId ($_clientId)
    {
        $this->_clientId = $_clientId;
    }

    /**
     * Setter for $_addressId
     *
     * @param string $_addressId
     *            The new value
     */
    public function setAddressId ($_addressId)
    {
        $this->_addressId = $_addressId;
    }

    /**
     * Setter for $_primaryAddress
     *
     * @param string $_primaryAddress
     *            The new value
     */
    public function setPrimaryAddress ($_primaryAddress)
    {
        $this->_primaryAddress = $_primaryAddress;
    }

    /**
     * Setter for $_name
     *
     * @param string $_name
     *            The new value
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
    }
}
