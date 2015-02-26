<?php

namespace MPSToolbox\Legacy\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class UserSessionModel
 *
 * @package MPSToolbox\Legacy\Models
 */
class UserSessionModel extends My_Model_Abstract
{
    /**
     * Id of the session
     *
     * @var int
     */
    public $sessionId;

    /**
     * User id from the represent and id of user
     *
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
        if (isset($params->sessionId) && !is_null($params->sessionId))
        {
            $this->sessionId = $params->sessionId;
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
        return [
            'sessionId' => $this->sessionId,
            'userId'    => $this->userId,
        ];
    }
}