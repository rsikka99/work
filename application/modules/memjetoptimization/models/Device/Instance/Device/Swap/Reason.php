<?php

class Memjetoptimization_Model_Device_Instance_Device_Swap_Reason extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $memjetOptimizationId;
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

        if (isset($params->memjetOptimizationId) && !is_null($params->memjetOptimizationId))
        {
            $this->memjetOptimizationId = $params->memjetOptimizationId;
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
        return array(
            "memjetOptimizationId" => $this->memjetOptimizationId,
            "deviceInstanceId"       => $this->deviceInstanceId,
            "deviceSwapReasonId"     => $this->deviceSwapReasonId,
        );
    }

}