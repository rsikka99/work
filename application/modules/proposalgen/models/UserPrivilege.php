<?php

/**
 * Class Proposalgen_Model_UserPrivileges
 *
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_UserPrivilege extends Tangent_Model_Abstract
{
    // Database Fields
    protected $PrivId;
    protected $UserId;

    /**
     *
     * @return the $PrivId
     */
    public function getPrivId ()
    {
        if (! isset($this->PrivId))
        {
            
            $this->PrivId = null;
        }
        return $this->PrivId;
    }

    /**
     *
     * @param field_type $PrivId            
     */
    public function setPrivId ($PrivId)
    {
        $this->PrivId = $PrivId;
        return $this;
    }

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
}