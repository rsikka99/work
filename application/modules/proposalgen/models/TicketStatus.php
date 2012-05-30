<?php

/**
 * Class Proposalgen_Model_TicketStatus
 * 
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_TicketStatus extends Tangent_Model_Abstract
{
    const STATUS_NEW = 1;
    const STATUS_OPEN = 2;
    const STATUS_CLOSED = 3;
    const STATUS_REJECTED = 4;
    protected $StatusId;
    protected $StatusName;

    /**
     *
     * @return the $StatusId
     */
    public function getStatusId ()
    {
        if (! isset($this->StatusId))
        {
            
            $this->StatusId = null;
        }
        return $this->StatusId;
    }

    /**
     *
     * @param field_type $StatusId            
     */
    public function setStatusId ($StatusId)
    {
        $this->StatusId = $StatusId;
        return $this;
    }

    /**
     *
     * @return the $StatusName
     */
    public function getStatusName ()
    {
        if (! isset($this->StatusName))
        {
            
            $this->StatusName = null;
        }
        return $this->StatusName;
    }

    /**
     *
     * @param field_type $StatusName            
     */
    public function setStatusName ($StatusName)
    {
        $this->StatusName = $StatusName;
        return $this;
    }
}