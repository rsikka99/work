<?php

/**
 * Application_Model_Page
 *
 * @author Lee Robert
 *        
 */
class Application_Model_Page extends My_Model_Abstract
{
    
    /**
     * The name of the page
     *
     * @var string
     */
    protected $_name;
    
    /**
     * The module of the page
     *
     * @var string
     */
    protected $_module;
    
    /**
     * The controller of the page
     *
     * @var string
     */
    protected $_controller;
    
    /**
     * The action of the page
     *
     * @var string
     */
    protected $_action;
    
    /**
     * Whether or not we can view the page
     *
     * @var boolean
     */
    protected $_canView = false;
    
    /**
     * Whether or not the page is active
     *
     * @var boolean
     */
    protected $_active = false;
    
    /**
     * The next page in line
     *
     * @var Application_Model_Page
     */
    protected $_nextPage;
    
    /**
     * The previous page in line
     *
     * @var Application_Model_Page
     */
    protected $_previousPage;
    
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
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId() 
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
        return $this;
    }
}
