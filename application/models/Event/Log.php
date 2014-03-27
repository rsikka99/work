<?php

/**
 * Class Application_Model_Event_Log
 */
class Application_Model_Event_Log extends My_Model_Abstract
{
    /**
     * The id of the Event Log
     *
     * @var string
     */
    public $id;

    /**
     * The eventLogTypeId of the event
     *
     * @var string
     */
    public $eventLogTypeId;

    /**
     * The time the event took place
     *
     * @var String
     */
    public $timestamp;

    /**
     * The message
     *
     * @var String
     */
    public $message;

    /**
     * The ipAddress
     *
     * @var String
     */
    public $ipAddress;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }

        if (isset($params->eventLogTypeId) && !is_null($params->eventLogTypeId))
        {
            $this->eventLogTypeId = $params->eventLogTypeId;
        }

        if (isset($params->timestamp) && !is_null($params->timestamp))
        {
            $this->timestamp = $params->timestamp;
        }

        if (isset($params->message) && !is_null($params->message))
        {
            $this->message = $params->message;
        }

        if (isset($params->ipAddress) && !is_null($params->ipAddress))
        {
            $this->ipAddress = $params->ipAddress;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            'id'             => $this->id,
            'eventLogTypeId' => $this->eventLogTypeId,
            'timestamp'      => $this->timestamp,
            'message'        => $this->message,
            'ipAddress'      => $this->ipAddress,
        );
    }

    public function getAttachedUserId ()
    {
        $userId       = false;
        $userEventLog = Application_Model_Mapper_User_Event_Log::getInstance()->find($this->id);

        if ($userEventLog instanceof Application_Model_User_Event_Log)
        {
            $userId = $userEventLog->userId;
        }

        return $userId;
    }
}