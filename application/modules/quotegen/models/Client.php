<?php

/**
 * Quotegen_Model_Client
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_Client extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * Represents the client name
     *
     * @var string
     */
    protected $_name;
    
    /**
     * The address of the client
     *
     * @var string
     */
    protected $_address;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->id) && ! is_null($params->id))
            $this->setId($params->id);
        if (isset($params->name) && ! is_null($params->name))
            $this->setName($params->name);
        if (isset($params->address) && ! is_null($params->address))
            $this->setAddress($params->address);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'name' => $this->getName(), 
                'address' => $this->getAddress() 
        );
    }

    /**
     * Gets the id of the object
     *
     * @return number The id of the object
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Sets the id of the object
     *
     * @param number $_id
     *            the new id
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
    }

    /**
     * Gets the name of the object
     *
     * @return the $_name
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
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
     * Gets the address of the object
     *
     * @return the $_address
     */
    public function getAddress ()
    {
        return $this->_address;
    }

    /**
     *
     * @param string $_address
     *            the new adress
     */
    public function setAddress ($_address)
    {
        $this->_address = $_address;
        return $this;
    }
}
