<?php
class Proposalgen_Model_UserDeviceOverride extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $masterDeviceId;

    /**
     * @var float
     */
    public $overrideDevicePrice;

    /**
     * @var bool
     */
    public $isLeased;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->masterDeviceId) && !is_null($params->masterDeviceId))
        {
            $this->masterDeviceId = $params->masterDeviceId;
        }

        if (isset($params->overrideDevicePrice) && !is_null($params->overrideDevicePrice))
        {
            $this->overrideDevicePrice = $params->overrideDevicePrice;
        }

        if (isset($params->isLeased) && !is_null($params->isLeased))
        {
            $this->isLeased = $params->isLeased;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "userId"              => $this->userId,
            "masterDeviceId"      => $this->masterDeviceId,
            "overrideDevicePrice" => $this->overrideDevicePrice,
            "isLeased"            => $this->isLeased,
        );
    }
}