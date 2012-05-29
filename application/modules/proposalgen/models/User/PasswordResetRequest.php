<?php

/**
 * Class Proposalgen_Model_User_PasswordResetRequest
 */
class Proposalgen_Model_User_PasswordResetRequest extends Tangent_Model_Abstract
{
    protected $Id;
    protected $DateRequested;
    protected $ResetToken;
    protected $IpAddress;
    protected $ResetVerified;
    protected $UserId;
    protected $ResetUsed;

    /**
     * @return the $Id
     */
    public function getId ()
    {
        if (! isset($this->Id))
        {
            
            $this->Id = null;
        }
        return $this->Id;
    }

    /**
     * @param field_type $Id
     */
    public function setId ($Id)
    {
        $this->Id = $Id;
        return $this;
    }

    /**
     * @return the $DateRequested
     */
    public function getDateRequested ()
    {
        if (! isset($this->DateRequested))
        {
            
            $this->DateRequested = null;
        }
        return $this->DateRequested;
    }

    /**
     * @param field_type $DateRequested
     */
    public function setDateRequested ($DateRequested)
    {
        $this->DateRequested = $DateRequested;
        return $this;
    }

    /**
     * @return the $ResetToken
     */
    public function getResetToken ()
    {
        if (! isset($this->ResetToken))
        {
            
            $this->ResetToken = null;
        }
        return $this->ResetToken;
    }

    /**
     * @param field_type $ResetToken
     */
    public function setResetToken ($ResetToken)
    {
        $this->ResetToken = $ResetToken;
        return $this;
    }

    /**
     * @return the $IpAddress
     */
    public function getIpAddress ()
    {
        if (! isset($this->IpAddress))
        {
            
            $this->IpAddress = null;
        }
        return $this->IpAddress;
    }

    /**
     * @param field_type $IpAddress
     */
    public function setIpAddress ($IpAddress)
    {
        $this->IpAddress = $IpAddress;
        return $this;
    }

    /**
     * @return the $ResetVerified
     */
    public function getResetVerified ()
    {
        if (! isset($this->ResetVerified))
        {
            
            $this->ResetVerified = null;
        }
        return $this->ResetVerified;
    }

    /**
     * @param field_type $ResetVerified
     */
    public function setResetVerified ($ResetVerified)
    {
        $this->ResetVerified = $ResetVerified;
        return $this;
    }

    /**
     * @return the $UserId
     */
    public function getUserId ()
    {
        if (! isset($this->UserId))
        {
            
            $this->UserId = null;
        }
        return $this->UserId;
    }

    /**
     * @param field_type $UserId
     */
    public function setUserId ($UserId)
    {
        $this->UserId = $UserId;
        return $this;
    }

    /**
     * @return the $ResetUsed
     */
    public function getResetUsed ()
    {
        if (! isset($this->ResetUsed))
        {
            // TODO: Change the default value
            $this->ResetUsed = null;
        }
        return $this->ResetUsed;
    }

    /**
     * @param field_type $ResetUsed
     */
    public function setResetUsed ($ResetUsed)
    {
        $this->ResetUsed = $ResetUsed;
        return $this;
    }

}