<?php
class Proposalgen_Model_TicketComment extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $commentId;

    /**
     * @var int
     */
    public $ticketId;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var string
     */
    public $commentText;

    /**
     * @var string
     */
    public $commentDate;

    /**
     * @var Application_Model_User
     */
    protected $_user;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->commentId) && !is_null($params->commentId))
        {
            $this->commentId = $params->commentId;
        }

        if (isset($params->ticketId) && !is_null($params->ticketId))
        {
            $this->ticketId = $params->ticketId;
        }

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->commentText) && !is_null($params->commentText))
        {
            $this->commentText = $params->commentText;
        }

        if (isset($params->commentDate) && !is_null($params->commentDate))
        {
            $this->commentDate = $params->commentDate;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "commentId"   => $this->commentId,
            "ticketId"    => $this->ticketId,
            "userId"      => $this->userId,
            "commentText" => $this->commentText,
            "commentDate" => $this->commentDate,
        );
    }


    /**
     * Gets the user
     *
     * @return Application_Model_User
     */
    public function getUser ()
    {
        if (!isset($this->_user))
        {
            $id = $this->userId;
            if (isset($id))
            {

                $this->_user = Application_Model_Mapper_User::getInstance()->find($id);
            }
        }

        return $this->_user;
    }

    /**
     * Sets the user
     *
     * @param $User
     *
     * @return Proposalgen_Model_TicketComment
     */
    public function setUser ($User)
    {
        $this->_user = $User;

        return $this;
    }
}