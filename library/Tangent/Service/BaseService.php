<?php

namespace Tangent\Service;

abstract class BaseService
{
    protected $_errorMessages = array();

    /**
     * Whether or not the service has errors
     *
     * @return bool
     */
    public function hasErrors ()
    {
        return (count($this->_errorMessages) > 0);
    }

    /**
     * Adds an error message
     *
     * @param mixed  $messageId The id of the message
     * @param string $message   The message to add
     */
    public function addError ($messageId, $message)
    {
        $this->_errorMessages[$messageId] = $message;
    }

    /**
     * Gets all error messages
     *
     * @return array
     */
    public function getErrors ()
    {
        return $this->_errorMessages;
    }
}

