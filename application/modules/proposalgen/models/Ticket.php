<?php

/**
 * Class Proposalgen_Model_Ticket
 * 
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_Ticket extends Tangent_Model_Abstract
{
    protected $TicketId;
    protected $UserId;
    protected $CategoryId;
    protected $StatusId;
    protected $Title;
    protected $Description;
    protected $DateCreated;
    protected $DateUpdated;
    
    // Extra fields
    protected $User;
    protected $Category;
    protected $Status;

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
     * @return the $CategoryId
     */
    public function getCategoryId ()
    {
        if (! isset($this->CategoryId))
        {
            
            $this->CategoryId = null;
        }
        return $this->CategoryId;
    }

    /**
     *
     * @param field_type $CategoryId            
     */
    public function setCategoryId ($CategoryId)
    {
        $this->CategoryId = $CategoryId;
        return $this;
    }

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
     * @return the $Title
     */
    public function getTitle ()
    {
        if (! isset($this->Title))
        {
            
            $this->Title = null;
        }
        return $this->Title;
    }

    /**
     *
     * @param field_type $Title            
     */
    public function setTitle ($Title)
    {
        $this->Title = $Title;
        return $this;
    }

    /**
     *
     * @return the $Description
     */
    public function getDescription ()
    {
        if (! isset($this->Description))
        {
            
            $this->Description = null;
        }
        return $this->Description;
    }

    /**
     *
     * @param field_type $Description            
     */
    public function setDescription ($Description)
    {
        $this->Description = $Description;
        return $this;
    }

    /**
     *
     * @return the $DateCreated
     */
    public function getDateCreated ()
    {
        if (! isset($this->DateCreated))
        {
            
            $this->DateCreated = null;
        }
        return $this->DateCreated;
    }

    /**
     *
     * @param field_type $DateCreated            
     */
    public function setDateCreated ($DateCreated)
    {
        $this->DateCreated = $DateCreated;
        return $this;
    }

    /**
     *
     * @return the $DateUpdated
     */
    public function getDateUpdated ()
    {
        if (! isset($this->DateUpdated))
        {
            
            $this->DateUpdated = null;
        }
        return $this->DateUpdated;
    }

    /**
     *
     * @param field_type $DateUpdated            
     */
    public function setDateUpdated ($DateUpdated)
    {
        $this->DateUpdated = $DateUpdated;
        return $this;
    }

    /**
     *
     * @return the $User
     */
    public function getUser ()
    {
        if (! isset($this->User))
        {
            $id = $this->getUserId();
            if (isset($id))
            {
                $this->User = Proposalgen_Model_Mapper_User::getInstance()->find($id);
            }
        }
        return $this->User;
    }

    /**
     *
     * @param field_type $User            
     */
    public function setUser ($User)
    {
        $this->User = $User;
        return $this;
    }

    /**
     *
     * @return the $Category
     */
    public function getCategory ()
    {
        if (! isset($this->Category))
        {
            $id = $this->getCategoryId();
            if (isset($id))
            {
                $this->Category = Proposalgen_Model_Mapper_TicketCategory::getInstance()->find($id);
            }
        }
        return $this->Category;
    }

    /**
     *
     * @param field_type $Category            
     */
    public function setCategory ($Category)
    {
        $this->Category = $Category;
        return $this;
    }

    /**
     *
     * @return the $Status
     */
    public function getStatus ()
    {
        if (! isset($this->Status))
        {
            $id = $this->getStatusId();
            if (isset($id))
            {
                $this->Status = Proposalgen_Model_Mapper_TicketStatus::getInstance()->find($id);
            }
        }
        return $this->Status;
    }

    /**
     *
     * @param field_type $Status            
     */
    public function setStatus ($Status)
    {
        $this->Status = $Status;
        return $this;
    }
}