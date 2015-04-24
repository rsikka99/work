<?php

namespace MPSToolbox\Legacy\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class UserActivityModel
 *
 * @package MPSToolbox\Legacy\Models
 */
class UserActivityModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var string
     */
    public $lastSeen;

    /**
     * @var string
     */
    public $url;

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
        if (isset($params->lastSeen) && !is_null($params->lastSeen))
        {
            $this->lastSeen = $params->lastSeen;
        }
        if (isset($params->url) && !is_null($params->url))
        {
            $this->url = $params->url;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            'userId'   => $this->userId,
            'lastSeen' => $this->lastSeen,
            'url'      => $this->url,
        ];
    }
}