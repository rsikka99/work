<?php

namespace MPSToolbox\Legacy\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class UserEventLogModel
 *
 * @package MPSToolbox\Legacy\Models
 */
class UserEventLogModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $eventLogId;

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

        if (isset($params->eventLogId) && !is_null($params->eventLogId))
        {
            $this->eventLogId = $params->eventLogId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "userId"     => $this->userId,
            "eventLogId" => $this->eventLogId,
        );
    }
}