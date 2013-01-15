<?php
class Proposalgen_Model_TicketStatus extends Tangent_Model_Abstract
{
    const STATUS_NEW      = 1;
    const STATUS_OPEN     = 2;
    const STATUS_CLOSED   = 3;
    const STATUS_REJECTED = 4;

    /**
     * @var int
     */
    public $statusId;

    /**
     * @var string
     */
    public $statusName;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->statusId) && !is_null($params->statusId))
        {
            $this->statusId = $params->statusId;
        }

        if (isset($params->statusName) && !is_null($params->statusName))
        {
            $this->statusName = $params->statusName;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "statusId"   => $this->statusId,
            "statusName" => $this->statusName,
        );
    }
}