<?php

/**
 * Class Proposalgen_Model_TicketViewed
 *
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_TicketViewed extends Tangent_Model_Abstract
{
    protected $TicketId;
    protected $UserId;
    protected $DateViewed;

    /**
     *
     * @return the $TicketId
     */
    public function getTicketId ()
    {
        if (! isset($this->TicketId))
        {
            
            $this->TicketId = null;
        }
        return $this->TicketId;
    }

    /**
     *
     * @param field_type $TicketId            
     */
    public function setTicketId ($TicketId)
    {
        $this->TicketId = $TicketId;
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

    /**
     *
     * @return the $DateViewed
     */
    public function getDateViewed ()
    {
        if (! isset($this->DateViewed))
        {
            
            $this->DateViewed = null;
        }
        return $this->DateViewed;
    }

    /**
     *
     * @param field_type $DateViewed            
     */
    public function setDateViewed ($DateViewed)
    {
        $this->DateViewed = $DateViewed;
        return $this;
    }
}