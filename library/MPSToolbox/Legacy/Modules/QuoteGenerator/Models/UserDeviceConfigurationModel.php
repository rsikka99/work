<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class UserDeviceConfigurationModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class UserDeviceConfigurationModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $deviceConfigurationId;

    /**
     * @var int
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

        if (isset($params->deviceConfigurationId) && !is_null($params->deviceConfigurationId))
        {
            $this->deviceConfigurationId = $params->deviceConfigurationId;
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
            "deviceConfigurationId" => $this->deviceConfigurationId,
            "userId"                => $this->userId,
        ];
    }
}