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
    public $userId;

    /**
     * @var int
     */
    public $isSystemDevice;

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

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->isSystemDevice) && !is_null($params->isSystemDevice))
        {
            $this->isSystemDevice = $params->isSystemDevice;
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
            "userId"         => $this->userId,
            "masterDeviceId" => $this->masterDeviceId,
            "isSystemDevice" => $this->isSystemDevice,
        );
    }
}