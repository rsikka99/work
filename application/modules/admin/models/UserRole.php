<?php
/**
 * Class Admin_Model_UserRole
 */
class Admin_Model_UserRole extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $roleId;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->roleId) && !is_null($params->roleId))
        {
            $this->roleId = $params->roleId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "userId" => $this->userId,
            "roleId" => $this->roleId,
        );
    }
}
