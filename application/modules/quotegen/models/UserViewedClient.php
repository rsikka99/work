<?php

/**
 * Class Quotegen_Model_UserViewedClient
 */
class Quotegen_Model_UserViewedClient extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $clientId;

    /**
     * @var int
     */
    public $dateViewed;


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

        if (isset($params->clientId) && !is_null($params->clientId))
        {
            $this->clientId = $params->clientId;
        }

        if (isset($params->dateViewed) && !is_null($params->dateViewed))
        {
            $this->dateViewed = $params->dateViewed;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "userId"     => $this->userId,
            "clientId"   => $this->clientId,
            "dateViewed" => $this->dateViewed,
        );
    }
}