<?php
class Application_Model_User_Session extends My_Model_Abstract
{
    /**
     * Id of the session
     * @var int
     */
    public $id;

    /**
     * User id from the represent and id of user
     * @var string
     */
    public $userId;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }
        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id" => $this->id,
            "userId" => $this->userId,
        );
    }
}