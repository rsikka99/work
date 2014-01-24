<?php

/**
 * Class Proposalgen_Model_UserTonerOverride
 */
class Proposalgen_Model_UserTonerOverride extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $tonerId;

    /**
     * @var int
     */
    public $overrideTonerPrice;

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

        if (isset($params->tonerId) && !is_null($params->tonerId))
        {
            $this->tonerId = $params->tonerId;
        }

        if (isset($params->overrideTonerPrice) && !is_null($params->overrideTonerPrice))
        {
            $this->overrideTonerPrice = $params->overrideTonerPrice;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "userId"             => $this->userId,
            "tonerId"            => $this->tonerId,
            "overrideTonerPrice" => $this->overrideTonerPrice,
        );
    }
}