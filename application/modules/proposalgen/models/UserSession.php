<?php

/**
 * Class Proposalgen_Model_UserSession
 * 
 * @author "Lee Robert"
 */
class Proposalgen_Model_UserSession extends Tangent_Model_Abstract
{
    protected $UserId;
    protected $SessionId;

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
     * @return the $SessionId
     */
    public function getSessionId ()
    {
        if (! isset($this->SessionId))
        {
            
            $this->SessionId = null;
        }
        return $this->SessionId;
    }

    /**
     *
     * @param field_type $SessionId            
     */
    public function setSessionId ($SessionId)
    {
        $this->SessionId = $SessionId;
        return $this;
    }
}