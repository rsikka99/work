<?php

namespace MPSToolbox\Legacy\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class UserPasswordResetRequestModel
 *
 * @package MPSToolbox\Legacy\Models
 */
class UserPasswordResetRequestModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $dateRequested;

    /**
     * @var string
     */
    public $resetToken;

    /**
     * @var string
     */
    public $ipAddress;

    /**
     * @var bool
     */
    public $resetVerified;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var bool
     */
    public $resetUsed;

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            'id'            => $this->id,
            'dateRequested' => $this->dateRequested,
            'resetToken'    => $this->resetToken,
            'ipAddress'     => $this->ipAddress,
            'resetVerified' => $this->resetVerified,
            'userId'        => $this->userId,
            'resetUsed'     => $this->resetUsed
        ];
    }

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
        if (isset($params->dateRequested) && !is_null($params->dateRequested))
        {
            $this->dateRequested = $params->dateRequested;
        }
        if (isset($params->resetToken) && !is_null($params->resetToken))
        {
            $this->resetToken = $params->resetToken;
        }
        if (isset($params->ipAddress) && !is_null($params->ipAddress))
        {
            $this->ipAddress = $params->ipAddress;
        }
        if (isset($params->resetVerified) && !is_null($params->resetVerified))
        {
            $this->resetVerified = $params->resetVerified;
        }
        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }
        if (isset($params->resetUsed) && !is_null($params->resetUsed))
        {
            $this->resetUsed = $params->resetUsed;
        }
    }
}