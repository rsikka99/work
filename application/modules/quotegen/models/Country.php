<?php

/**
 * Quotegen_Model_Client
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_Country extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The name of the country
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
        if (isset($params->id) && ! is_null($params->id))
            $this->setId($params->id);
        if (isset($params->name) && ! is_null($params->name))
            $this->setName($params->name);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'name' => $this->getName()
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
     * Getter for $_name
     *
     * @return string
     */
    public function getName ()
    {
        return $this->_name;
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
