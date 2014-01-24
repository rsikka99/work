<?php

/**
 * Class Proposalgen_Model_DeviceToner
 */
class Proposalgen_Model_DeviceToner extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $toner_id;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $isSystemDevice;

    /**
     * @var int
     */
    public $master_device_id;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->toner_id) && !is_null($params->toner_id))
        {
            $this->toner_id = $params->toner_id;
        }

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->isSystemDevice) && !is_null($params->isSystemDevice))
        {
            $this->isSystemDevice = $params->isSystemDevice;
        }

        if (isset($params->master_device_id) && !is_null($params->master_device_id))
        {
            $this->master_device_id = $params->master_device_id;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "toner_id"         => $this->toner_id,
            "userId"           => $this->userId,
            "master_device_id" => $this->master_device_id,
            "isSystemDevice"   => $this->isSystemDevice,
        );
    }
}