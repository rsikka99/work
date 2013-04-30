<?php
/**
 * Class Proposalgen_Model_DeviceToner
 */
class Proposalgen_Model_DeviceToner extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $tonerId;

    /**
     * @var int
     */
    public $masterDeviceId;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->tonerId) && !is_null($params->tonerId))
        {
            $this->tonerId = $params->tonerId;
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "tonerId"        => $this->tonerId,
            "masterDeviceId" => $this->masterDeviceId,
        );
    }
}