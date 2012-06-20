<?php

/**
 * Class Proposalgen_Model_Manufacturer
 *
 * @author "Lee Robert"
 */
class Proposalgen_Model_Manufacturer extends My_Model_Abstract
{
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id;
    
    /**
     * The name of the manufacturer
     *
     * @var string
     */
    protected $_fullname;
    
    /**
     * The display name of the manufacturer (to show up on reports)
     *
     * @var string
     */
    protected $_displayname;
    
    /**
     * A flag to show whether or not a manufacturer is deleted
     *
     * @var boolean
     */
    protected $_isDeleted;
    
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
        
        if (isset($params->fullname) && ! is_null($params->fullname))
            $this->setFullName($params->fullname);
        
        if (isset($params->displayname) && ! is_null($params->displayname))
            $this->setDisplayname($params->displayname);
        
        if (isset($params->isDeleted) && ! is_null($params->isDeleted))
            $this->setIsDeleted($params->isDeleted);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'fullname' => $this->getFullname(), 
                'displayname' => $this->getDisplayname(), 
                'isDeleted' => (int)$this->getId() 
        );
    }

    /**
     * Gets the id of the manufacturer
     *
     * @return number
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Sets the id of the manufacturer
     *
     * @param number $_id
     *            The new id
     */
    public function setId ($_id)
    {
        $this->_id = (int)$_id;
        return $this;
    }

    /**
     * Gets the full name of the manufacturer
     *
     * @return string
     */
    public function getFullname ()
    {
        return $this->_fullname;
    }

    /**
     * Sets the full name of the manufacturer
     *
     * @param string $_fullname
     *            The new full name
     */
    public function setFullname ($_fullname)
    {
        $this->_fullname = (string)$_fullname;
        return $this;
    }

    /**
     * Gets the display name of the manufacturer
     *
     * @return string
     */
    public function getDisplayname ()
    {
        return $this->_displayname;
    }

    /**
     * Sets the display name of the manufacturer
     *
     * @param string $_displayname
     *            The new display name
     */
    public function setDisplayname ($_displayname)
    {
        $this->_displayname = (string)$_displayname;
        return $this;
    }

    /**
     * Gets the deleted flag
     *
     * @return boolean
     */
    public function getIsDeleted ()
    {
        return $this->_isDeleted;
    }

    /**
     * Sets the deleted flag
     *
     * @param boolean $_isDeleted            
     */
    public function setIsDeleted ($_isDeleted)
    {
        $this->_isDeleted = (bool)$_isDeleted;
        return $this;
    }
}