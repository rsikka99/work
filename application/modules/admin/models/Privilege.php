<?php

/**
 * Admin_Model_Privilege is a model that represents a privilege row in the database.
 *
 * @author Lee Robert
 *        
 */
class Admin_Model_Privilege extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The role this privilege is assigned to
     *
     * @var int
     */
    protected $_roleId;
    
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
                'roleId' => array (), 
                'module' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'controller' => array (
                        'StringTrim', 
                        'StripTags' 
                ), 
                'action' => array (
                        'StringTrim', 
                        'StripTags' 
                ) 
        );
        
        // Set the validators
        $this->_validators = array (
                'id' => array (
                        'Int' 
                ), 
                'roleId' => array (
                        'Int', 
                        Zend_Filter_Input::ALLOW_EMPTY => true 
                ), 
                'module' => array (
                        new Zend_Validate_StringLength(array (
                                'min' => 1, 
                                'max' => 255 
                        )), 
                        Zend_Filter_Input::ALLOW_EMPTY => true 
                ), 
                'controller' => array (
                        new Zend_Validate_StringLength(array (
                                'min' => 1, 
                                'max' => 255 
                        )), 
                        Zend_Filter_Input::ALLOW_EMPTY => true 
                ), 
                'action' => array (
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
        if (isset($params->roleId) && ! is_null($params->roleId))
            $this->setRoleId($params->roleId);
        if (isset($params->module) && ! is_null($params->module))
            $this->setModule($params->module);
        if (isset($params->controller) && ! is_null($params->controller))
            $this->setController($params->controller);
        if (isset($params->action) && ! is_null($params->action))
            $this->setAction($params->action);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'roleId' => $this->getRoleId(), 
                'module' => $this->getModule(), 
                'controller' => $this->getController(), 
                'action' => $this->getAction() 
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
     * @return int the $_roleId
     */
    public function getRoleId ()
    {
        return $this->_roleId;
    }

    /**
     *
     * @param int $_roleId            
     */
    public function setRoleId ($_roleId)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'roleId' => $_roleId 
        ));
        
        if (! $input->isValid('roleId'))
        {
            throw new InvalidArgumentException('Invalid role id provided');
        }
        
        $this->_roleId = $input->roleId;
        return $this;
    }

    /**
     *
     * @return string the $_module
     */
    public function getModule ()
    {
        return $this->_module;
    }

    /**
     *
     * @param string $_module            
     */
    public function setModule ($_module)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'module' => $_module 
        ));
        
        if (! $input->isValid('module'))
        {
            throw new InvalidArgumentException('Invalid module provided');
        }
        
        $this->_module = $input->module;
        return $this;
    }

    /**
     *
     * @return string the $_controller
     */
    public function getController ()
    {
        return $this->_controller;
    }

    /**
     *
     * @param string $_controller            
     */
    public function setController ($_controller)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'controller' => $_controller 
        ));
        
        if (! $input->isValid('controller'))
        {
            throw new InvalidArgumentException('Invalid controller provided');
        }
        
        $this->_controller = $input->controller;
        return $this;
    }

    /**
     *
     * @return string the $_action
     */
    public function getAction ()
    {
        return $this->_action;
    }

    /**
     *
     * @param string $_action            
     */
    public function setAction ($_action)
    {
        $input = new Zend_Filter_Input($this->_filters, $this->_validators, array (
                'action' => $_action 
        ));
        
        if (! $input->isValid('action'))
        {
            throw new InvalidArgumentException('Invalid action provided');
        }
        
        $this->_action = $input->action;
        return $this;
    }
}
