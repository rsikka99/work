<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class DeviceInstanceDeviceSwapReasonModel
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Models
 */
class DeviceInstanceDeviceSwapReasonModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $hardwareOptimizationId;
    /**
     * @var int
     */
    public $deviceInstanceId;
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

        if (isset($params->hardwareOptimizationId) && !is_null($params->hardwareOptimizationId))
        {
            $this->hardwareOptimizationId = $params->hardwareOptimizationId;
        }
        if (isset($params->deviceInstanceId) && !is_null($params->deviceInstanceId))
        {
            $this->deviceInstanceId = $params->deviceInstanceId;
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
        return [
            "hardwareOptimizationId" => $this->hardwareOptimizationId,
            "deviceInstanceId"       => $this->deviceInstanceId,
            "deviceSwapReasonId"     => $this->deviceSwapReasonId,
        ];
    }

}