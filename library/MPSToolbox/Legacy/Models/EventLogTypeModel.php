<?php

namespace MPSToolbox\Legacy\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class EventLogTypeModel
 *
 * @package MPSToolbox\Legacy\Models
 */
class EventLogTypeModel extends My_Model_Abstract
{
    const LOGIN                   = "login";
    const LOGIN_FAIL              = "login_fail";
    const LOGOUT                  = "logout";
    const CHANGE_PASSWORD         = "change_password";
    const FORGOT_PASSWORD_SEND    = "forgot_password_send";
    const FORGOT_PASSWORD_CHANGED = "forgot_password_changed";

    /**
     * @var String
     */
    public $id;

    /**
     * @var String
     */
    public $name;

    /**
     * @var int
     */
    public $description;

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

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

        if (isset($params->description) && !is_null($params->description))
        {
            $this->description = $params->description;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "id"          => $this->id,
            "name"        => $this->name,
            "description" => $this->description,
        ];
    }
}