<?php

/**
 * Quotegen_Model_Category
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_Category extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    /**
     * The name of the category
     *
     * @var string
     */
    protected $_name;
    
    /**
     * The description of the category
     *
     * @var description
     */
    protected $_description;
    
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
        
        if (isset($params->description) && ! is_null($params->description))
            $this->setDescription($params->description);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'name' => $this->getName(), 
                'description' => $this->getDescription() 
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
     * Gets the name of category
     *
     * @return string The name of the category
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Sets a new name for the category
     *
     * @param string $_name
     *            The new name
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
        return $this;
    }

    /**
     * Gets the description of the category
     *
     * @return string The description
     */
    public function getDescription ()
    {
        return $this->_description;
    }

    /**
     * Sets a new description for the category
     *
     * @param description $_description
     *            The new description
     */
    public function setDescription ($_description)
    {
        $this->_description = $_description;
        return $this;
    }
}
