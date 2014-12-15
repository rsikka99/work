<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class DeviceSwapReasonDefaultModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 */
class DeviceSwapReasonDefaultModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $deviceSwapReasonCategoryId;

    /**
     * @var int
     */
    public $dealerId;

    /**
     * @var int
     */
    public $deviceSwapReasonId;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->deviceSwapReasonCategoryId) && !is_null($params->deviceSwapReasonCategoryId))
        {
            $this->deviceSwapReasonCategoryId = $params->deviceSwapReasonCategoryId;
        }
        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }
        if (isset($params->deviceSwapReasonId) && !is_null($params->deviceSwapReasonId))
        {
            $this->deviceSwapReasonId = $params->deviceSwapReasonId;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "deviceSwapReasonCategoryId" => $this->deviceSwapReasonCategoryId,
            "dealerId"                   => $this->dealerId,
            "deviceSwapReasonId"         => $this->deviceSwapReasonId,
        );
    }
}