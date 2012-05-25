<?php

/**
 * Admin_Model_UserRole is a model that represents a user role in the database.
 *
 * @author Lee Robert
 *        
 */
class Admin_Model_UserRole extends My_Model_Abstract
{
    
    /**
     * The role id
     *
     * @var int
     */
    protected $_roleId = 0;
    
    /**
     * The user id
     *
     * @var string
     */
    protected $_userId;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->roleId) && ! is_null($params->roleId))
            $this->setRoleId($params->roleId);
        if (isset($params->userId) && ! is_null($params->userId))
            $this->setUserId($params->userId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'roleId' => $this->getRoleId(), 
                'userId' => $this->getUserId() 
        );
    }

    /**
     *
     * @return the $_roleId
     */
    public function getRoleId ()
    {
        return $this->_roleId;
    }

    /**
     *
     * @param number $_roleId            
     */
    public function setRoleId ($_roleId)
    {
        $this->_roleId = $_roleId;
    }

    /**
     *
     * @return the $_userId
     */
    public function getUserId ()
    {
        return $this->_userId;
    }

    /**
     *
     * @param string $_userId            
     */
    public function setUserId ($_userId)
    {
        $this->_userId = $_userId;
    }
}
