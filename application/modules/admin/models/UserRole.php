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
    public $roleId = 0;

    /**
     * The user id
     *
     * @var int
     */
    public $userId;

    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->roleId) && !is_null($params->roleId))
        {
            $this->roleId = $params->roleId;
        }
        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }
    }

    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array(
            'roleId' => $this->roleId,
            'userId' => $this->userId
        );
    }
}
