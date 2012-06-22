<?php

/**
 * Class Proposalgen_Model_MasterMatchupPf
 *
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_MasterMatchupPf extends Tangent_Model_Abstract
{
    protected $MasterDeviceId;
    protected $DevicesPfId;

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
     * @return the $DevicesPfId
     */
    public function getDevicesPfId ()
    {
        if (! isset($this->DevicesPfId))
        {
            
            $this->DevicesPfId = null;
        }
        return $this->DevicesPfId;
    }

    /**
     *
     * @param field_type $DevicesPfId            
     */
    public function setDevicesPfId ($DevicesPfId)
    {
        $this->DevicesPfId = $DevicesPfId;
        return $this;
    }
}