<?php
/**
 * Class Hardwareoptimization_Model_Device_Swap_Reason_Default
 */
class Hardwareoptimization_Model_Device_Swap_Reason_Default extends My_Model_Abstract
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
    public $deviceSwapId;


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
        if (isset($params->deviceSwapId) && !is_null($params->deviceSwapId))
        {
            $this->deviceSwapId = $params->deviceSwapId;
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
            "deviceSwapId"               => $this->deviceSwapId,
        );
    }
}