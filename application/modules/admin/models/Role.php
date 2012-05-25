<?php

/**
 * Admin_Model_Role is a model that represents a role in the database.
 *
 * @author Lee Robert
 *        
 */
class Admin_Model_Role extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The module to provide access to
     *
     * @var string
     */
    protected $_module;
    
    /**
     * The controller to provide access to
     *
     * @var string
     */
    protected $_controller;
    
    /**
     * The action to provide access to
     *
     * @var string
     */
    protected $_action;

    public function __construct ($options = null)
    {
        // Set the filters
        $alnum = new Zend_Filter_Alnum(true);
        $this->_filters = array (
                'id' => array (), 
                'name' => array (
                        'StringTrim', 
                        'StripTags', 
                        $alnum 
                ) 
        );
        
        // Set the validators
        $this->_validators = array (
                'id' => array (
                        'Int' 
                ), 
                'name' => array (
                        new Zend_Validate_StringLength(array (
                                'min' => 1, 
                                'max' => 255 
                        )), 
                        Zend_Filter_Input::ALLOW_EMPTY => true 
                ) 
        );
        parent::__construct($options);
    }
    
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
     *
     * @return int the $_id
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     *
     * @param int $_id            
     */
    public function setId ($_id)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'id' => $_id 
        ));
        
        if (! $input->isValid('id'))
        {
            throw new InvalidArgumentException('Invalid id provided');
        }
        
        $this->_id = $input->id;
        return $this;
    }

    /**
     *
     * @return string the $_name
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     *
     * @param string $_name            
     */
    public function setName ($_name)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'name' => $_name 
        ));
        
        if (! $input->isValid('name'))
        {
            throw new InvalidArgumentException('Invalid name provided');
        }
        
        $this->_name = $input->name;
        return $this;
    }
}
