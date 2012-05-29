<?php

/**
 * Class Proposalgen_Model_PfDeviceMatchupUsers
 *
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_PfDeviceMatchupUser extends Tangent_Model_Abstract
{
    protected $DevicesPfId;
    protected $MasterDeviceId;
    protected $UserId;

    /**
     *
     * @return the $DevicesPfId
     */
    public function getDevicesPfId ()
    {
        return $this->DevicesPfId;
    }

    /**
     *
     * @param field_type $DevicesPfId            
     */
    public function setDevicesPfId ($DevicesPfId)
    {
        $this->DevicesPfId = $DevicesPfId;
    }

    /**
     *
     * @return the $MasterDeviceId
     */
    public function getMasterDeviceId ()
    {
        return $this->MasterDeviceId;
    }

    /**
     *
     * @param field_type $MasterDeviceId            
     */
    public function setMasterDeviceId ($MasterDeviceId)
    {
        $this->MasterDeviceId = $MasterDeviceId;
    }

    /**
     *
     * @return the $UserId
     */
    public function getUserId ()
    {
        return $this->UserId;
    }

    /**
     *
     * @param field_type $UserId            
     */
    public function setUserId ($UserId)
    {
        $this->UserId = $UserId;
    }
}