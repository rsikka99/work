<?php

/**
 * Class Proposalgen_Model_DeviceToner
 *
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_DeviceToner extends Tangent_Model_Abstract
{
    protected $TonerId;
    protected $MasterDeviceId;

    /**
     *
     * @return the $TonerId
     */
    public function getTonerId ()
    {
        if (! isset($this->TonerId))
        {
            
            $this->TonerId = null;
        }
        return $this->TonerId;
    }

    /**
     *
     * @param field_type $TonerId            
     */
    public function setTonerId ($TonerId)
    {
        $this->TonerId = $TonerId;
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
}