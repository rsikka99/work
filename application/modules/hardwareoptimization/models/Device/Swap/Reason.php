<?php
/**
 * Class Hardwareoptimization_Model_Device_Swap
 */
class Hardwareoptimization_Model_Device_Swap_Reason extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $dealerId;
    /**
     * @var string
     */
    public $reason;

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

        if (isset($params->dealerId) && !is_null($params->dealerId))
        {
            $this->dealerId = $params->dealerId;
        }

        if (isset($params->reason) && !is_null($params->reason))
        {
            $this->reason = $params->reason;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "masterDeviceId" => $this->id,
            "dealerId"       => $this->dealerId,
            "reason"         => $this->reason,
        );
    }
}