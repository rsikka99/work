<?php

/**
 * Class Proposalgen_Model_UserDeviceOverride
 *
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_UserDeviceOverride extends Tangent_Model_Abstract
{
    // Database Fields
    protected $UserId;
    protected $MasterDeviceId;
    protected $OverrideDevicePrice;
    protected $IsLeased;

    /**
     *
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
     *
     * @param field_type $UserId            
     */
    public function setUserId ($UserId)
    {
        $this->UserId = $UserId;
        return $this;
    }

    /**
     *
     * @return the $MasterDeviceId
     */
    public function getMasterDeviceId ()
    {
        if (! isset($this->MasterDeviceId))
        {
            
            $this->MasterDeviceId = null;
        }
        return $this->MasterDeviceId;
    }

    /**
     *
     * @param field_type $MasterDeviceId            
     */
    public function setMasterDeviceId ($MasterDeviceId)
    {
        $this->MasterDeviceId = $MasterDeviceId;
        return $this;
    }

    /**
     *
     * @return the $OverrideDevicePrice
     */
    public function getOverrideDevicePrice ()
    {
        if (! isset($this->OverrideDevicePrice))
        {
            
            $this->OverrideDevicePrice = null;
        }
        return $this->OverrideDevicePrice;
    }

    /**
     *
     * @param field_type $OverrideDevicePrice            
     */
    public function setOverrideDevicePrice ($OverrideDevicePrice)
    {
        $this->OverrideDevicePrice = $OverrideDevicePrice;
        return $this;
    }

    /**
     *
     * @return the $IsLeased
     */
    public function getIsLeased ()
    {
        if (! isset($this->IsLeased))
        {
            
            $this->IsLeased = null;
        }
        return $this->IsLeased;
    }

    /**
     *
     * @param field_type $IsLeased            
     */
    public function setIsLeased ($IsLeased)
    {
        $this->IsLeased = $IsLeased;
        return $this;
    }
}