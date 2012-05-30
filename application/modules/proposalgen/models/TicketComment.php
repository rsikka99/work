<?php

/**
 * Class Proposalgen_Model_TicketComment
 * 
 * @author "Kevin Jervis"
 */
class Proposalgen_Model_TicketComment extends Tangent_Model_Abstract
{
    protected $CommentId;
    protected $TicketId;
    protected $UserId;
    protected $CommentText;
    protected $CommentDate;
    protected $User;

    /**
     *
     * @return the $CommentId
     */
    public function getCommentId ()
    {
        if (! isset($this->CommentId))
        {
            
            $this->CommentId = null;
        }
        return $this->CommentId;
    }

    /**
     *
     * @param field_type $CommentId            
     */
    public function setCommentId ($CommentId)
    {
        $this->CommentId = $CommentId;
        return $this;
    }

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
     * @return the $CommentDate
     */
    public function getCommentDate ()
    {
        if (! isset($this->CommentDate))
        {
            
            $this->CommentDate = null;
        }
        return $this->CommentDate;
    }

    /**
     *
     * @param field_type $CommentDate            
     */
    public function setCommentDate ($CommentDate)
    {
        $this->CommentDate = $CommentDate;
        return $this;
    }

    /**
     *
     * @return the $CommentText
     */
    public function getCommentText ()
    {
        if (! isset($this->CommentText))
        {
            
            $this->CommentText = null;
        }
        return $this->CommentText;
    }

    /**
     *
     * @param field_type $CommentText            
     */
    public function setCommentText ($CommentText)
    {
        $this->CommentText = $CommentText;
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
}