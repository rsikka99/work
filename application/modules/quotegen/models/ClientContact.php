<?php

/**
 * Quotegen_Model_Client
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_ClientContact extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_clientId = 0;
    
    /**
     * The account number of the client
     *
     * @var string
     */
    protected $_contactId;
    
    
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
        if (isset($params->contactId) && ! is_null($params->contactId))
            $this->setContactId($params->contactId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'clientId' => $this->getClientId(), 
                'contactId' => $this->getContactId(), 
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
     * Getter for $_contactId
     *
     * @return string
     */
    public function getContactId ()
    {
        return $this->_contactId;
    }

	/**
     * Setter for $_clientId
     *
     * @param number $_clientId The new value
     */
    public function setClientId ($_clientId)
    {
        $this->_clientId = $_clientId;
    }

	/**
     * Setter for $_contactId
     *
     * @param string $_contactId The new value
     */
    public function setContactId ($_contactId)
    {
        $this->_contactId = $_contactId;
    }


    
}
