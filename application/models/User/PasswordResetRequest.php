<?php

/**
 * Class Application_Model_User_PasswordResetRequest
 */
class Application_Model_User_PasswordResetRequest extends My_Model_Abstract
{
    public $id;
    public $dateRequested;
    public $resetToken;
    public $ipAddress;
    public $resetVerified;
    public $userId;
    public $resetUsed;

    /*
 * (non-PHPdoc) @see My_Model_Abstract::toArray()
 */
    public function toArray ()
    {
        return array(
            'id'                       => $this->id,
            'dateRequested'                 => $this->dateRequested,
            'resetToken'                 => $this->resetToken,
            'ipAddress'                => $this->ipAddress,
            'resetVerified'                 => $this->resetVerified,
            'userId'                    => $this->userId,
            'resetUsed'              => $this->resetUsed
        );
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