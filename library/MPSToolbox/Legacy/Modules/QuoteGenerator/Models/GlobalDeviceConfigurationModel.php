<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class GlobalDeviceConfigurationModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class GlobalDeviceConfigurationModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $deviceConfigurationId = 0;

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

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "deviceConfigurationId" => $this->deviceConfigurationId,
        ];
    }
}
