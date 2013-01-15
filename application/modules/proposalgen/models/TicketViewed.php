<?php
class Proposalgen_Model_TicketViewed extends My_Model_Abstract
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
     * @var string
     */
    public $dateViewed;

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

        if (isset($params->dateViewed) && !is_null($params->dateViewed))
        {
            $this->dateViewed = $params->dateViewed;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "ticketId"   => $this->ticketId,
            "userId"     => $this->userId,
            "dateViewed" => $this->dateViewed,
        );
    }
}