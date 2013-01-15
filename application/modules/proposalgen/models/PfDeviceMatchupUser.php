<?php
class Proposalgen_Model_PfDeviceMatchupUser extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $devicesPfId;

    /**
     * @var int
     */
    public $masterDeviceId;

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

        if (isset($params->devicesPfId) && !is_null($params->devicesPfId))
        {
            $this->devicesPfId = $params->devicesPfId;
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
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
        return array(
            "devicesPfId"    => $this->devicesPfId,
            "masterDeviceId" => $this->masterDeviceId,
            "userId"         => $this->userId,
        );
    }
}