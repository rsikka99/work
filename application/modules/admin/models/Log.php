<?php

class Admin_Model_Log extends My_Model_Abstract
{
    
    /**
     * The log id
     *
     * @var number
     */
    protected $_id;
    
    /**
     * The log type id
     *
     * @var number
     */
    protected $_logTypeId;
    
    /**
     * The log priority
     *
     * @var number
     */
    protected $_priority;
    
    /**
     * The log message
     *
     * @var string
     */
    protected $_message;
    
    /**
     * The log timestamp
     *
     * @var string
     */
    protected $_timestamp;
    
    /**
     * The user id that created the log entry
     *
     * @var number
     */
    protected $_userId;
    
    /**
     * The log type of this log
     *
     * @var Admin_Model_Log_Type
     */
    protected $_logType;
    
    /**
     * The user associated with this log
     *
     * @var Application_Model_User
     */
    protected $_user;
    
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
        
        if (isset($params->logTypeId) && ! is_null($params->logTypeId))
            $this->setLogTypeId($params->logTypeId);
        
        if (isset($params->priority) && ! is_null($params->priority))
            $this->setPriority($params->priority);
        
        if (isset($params->message) && ! is_null($params->message))
            $this->setMessage($params->message);
        
        if (isset($params->timestamp) && ! is_null($params->timestamp))
            $this->setTimestamp($params->timestamp);
        
        if (isset($params->userId) && ! is_null($params->userId))
            $this->setUserId($params->userId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                "id" => $this->getId(), 
                "logTypeId" => $this->getLogTypeId(), 
                "priority" => $this->getPriority(), 
                "message" => $this->getMessage(), 
                "timestamp" => $this->getTimestamp(), 
                "userId" => $this->getUserId() 
        );
    }

    /**
     * Getter for $_id
     *
     * @return number
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Setter for $_id
     *
     * @param number $_id
     *            The new value
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
        return $this;
    }

    /**
     * Getter for $_logTypeId
     *
     * @return number
     */
    public function getLogTypeId ()
    {
        return $this->_logTypeId;
    }

    /**
     * Setter for $_logTypeId
     *
     * @param number $_logTypeId
     *            The new value
     */
    public function setLogTypeId ($_logTypeId)
    {
        $this->_logTypeId = $_logTypeId;
        return $this;
    }

    /**
     * Getter for $_priority
     *
     * @return number
     */
    public function getPriority ()
    {
        return $this->_priority;
    }

    /**
     * Setter for $_priority
     *
     * @param number $_priority
     *            The new value
     */
    public function setPriority ($_priority)
    {
        $this->_priority = $_priority;
        return $this;
    }

    /**
     * Getter for $_message
     *
     * @return string
     */
    public function getMessage ()
    {
        return $this->_message;
    }

    /**
     * Setter for $_message
     *
     * @param string $_message
     *            The new value
     */
    public function setMessage ($_message)
    {
        $this->_message = $_message;
        return $this;
    }

    /**
     * Getter for $_timestamp
     *
     * @return string
     */
    public function getTimestamp ()
    {
        return $this->_timestamp;
    }

    /**
     * Setter for $_timestamp
     *
     * @param string $_timestamp
     *            The new value
     */
    public function setTimestamp ($_timestamp)
    {
        $this->_timestamp = $_timestamp;
        return $this;
    }

    /**
     * Getter for $_userId
     *
     * @return number
     */
    public function getUserId ()
    {
        return $this->_userId;
    }

    /**
     * Setter for $_userId
     *
     * @param number $_userId
     *            The new value
     */
    public function setUserId ($_userId)
    {
        $this->_userId = $_userId;
        return $this;
    }

    /**
     * **************************************************************************
     * End of database field getter/setters
     * **************************************************************************
     */
    
    /**
     * Getter for $_logType
     *
     * @return Admin_Model_Log_Type
     */
    public function getLogType ()
    {
        if (! isset($this->_logType))
        {
            $this->_logType = Admin_Model_Mapper_Log_Type::getInstance()->find($this->getLogTypeId());
        }
        return $this->_logType;
    }

    /**
     * Setter for $_logType
     *
     * @param Admin_Model_Log_Type $_logType
     *            The new value
     */
    public function setLogType ($_logType)
    {
        $this->_logType = $_logType;
        return $this;
    }

    /**
     * Getter for $_user
     *
     * @return Application_Model_User
     */
    public function getUser ()
    {
        if (! isset($this->_user))
        {
            $this->_user = Application_Model_Mapper_User::getInstance()->find($this->getUserId());
        }
        return $this->_user;
    }

    /**
     * Setter for $_user
     *
     * @param Application_Model_User $_user
     *            The new value
     */
    public function setUser ($_user)
    {
        $this->_user = $_user;
        return $this;
    }
}
