<?php
class Proposalgen_Model_Ticket extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $ticketId;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $categoryId;

    /**
     * @var int
     */
    public $statusId;

    /**
     * @var int
     */
    public $title;

    /**
     * @var int
     */
    public $description;

    /**
     * @var int
     */
    public $dateCreated;

    /**
     * @var int
     */
    public $dateUpdated;


    /**
     * @var Application_Model_User
     */
    protected $_user;

    /**
     * @var Proposalgen_Model_TicketCategory
     */
    protected $_ticketCategory;

    /**
     * @var Proposalgen_Model_TicketStatus
     */
    protected $_ticketStatus;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->ticketId) && !is_null($params->ticketId))
        {
            $this->ticketId = $params->ticketId;
        }

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->categoryId) && !is_null($params->categoryId))
        {
            $this->categoryId = $params->categoryId;
        }

        if (isset($params->statusId) && !is_null($params->statusId))
        {
            $this->statusId = $params->statusId;
        }

        if (isset($params->title) && !is_null($params->title))
        {
            $this->title = $params->title;
        }

        if (isset($params->description) && !is_null($params->description))
        {
            $this->description = $params->description;
        }

        if (isset($params->dateCreated) && !is_null($params->dateCreated))
        {
            $this->dateCreated = $params->dateCreated;
        }

        if (isset($params->dateUpdated) && !is_null($params->dateUpdated))
        {
            $this->dateUpdated = $params->dateUpdated;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "ticketId"    => $this->ticketId,
            "userId"      => $this->userId,
            "categoryId"  => $this->categoryId,
            "statusId"    => $this->statusId,
            "title"       => $this->title,
            "description" => $this->description,
            "dateCreated" => $this->dateCreated,
            "dateUpdated" => $this->dateUpdated,
        );
    }

    /**
     * Gets the associated user
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
     * @param $_user
     *
     * @return Proposalgen_Model_Ticket
     */
    public function setUser ($_user)
    {
        $this->_user = $_user;

        return $this;
    }

    /**
     * Gets the ticket category
     *
     * @return Proposalgen_Model_TicketCategory
     */
    public function getTicketCategory ()
    {
        if (!isset($this->_ticketCategory))
        {
            $id = $this->categoryId;
            if (isset($id))
            {
                $this->_ticketCategory = Proposalgen_Model_Mapper_TicketCategory::getInstance()->find($id);
            }
        }

        return $this->_ticketCategory;
    }

    /**
     * Set the ticket category
     *
     * @param $_ticketCategory
     *
     * @return Proposalgen_Model_Ticket
     */
    public function setTicketCategory ($_ticketCategory)
    {
        $this->_ticketCategory = $_ticketCategory;

        return $this;
    }

    /**
     * Gets the ticket status
     *
     * @return Proposalgen_Model_TicketStatus
     */
    public function getTicketStatus ()
    {
        if (!isset($this->_ticketStatus))
        {
            $id = $this->statusId;
            if (isset($id))
            {
                $this->_ticketStatus = Proposalgen_Model_Mapper_TicketStatus::getInstance()->find($id);
            }
        }

        return $this->_ticketStatus;
    }

    /**
     * Sets the ticket status
     *
     * @param $_ticketStatus
     *
     * @return Proposalgen_Model_Ticket
     */
    public function setTicketStatus ($_ticketStatus)
    {
        $this->_ticketStatus = $_ticketStatus;

        return $this;
    }
}