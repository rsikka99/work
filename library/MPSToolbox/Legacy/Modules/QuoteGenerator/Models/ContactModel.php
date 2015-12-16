<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use My_Model_Abstract;
use Zend_Db_Expr;

/**
 * Class ContactModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class ContactModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $clientId;

    /**
     * @var string
     */
    public $firstName = '';

    /**
     * @var string
     */
    public $lastName = '';

    /** @var  string */
    public $phoneNumber = '';

    /** @var  string */
    public $email = '';

    /** @var  string */
    public $website = '';

    /** @var  string */
    public $emailSupply = '';

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

        if (isset($params->clientId) && !is_null($params->clientId))
        {
            $this->clientId = $params->clientId;
        }

        if (isset($params->firstName) && !is_null($params->firstName))
        {
            $this->firstName = $params->firstName;
        }

        if (isset($params->lastName) && !is_null($params->lastName))
        {
            $this->lastName = $params->lastName;
        }

        if (isset($params->phoneNumber) && !is_null($params->phoneNumber))
        {
            $this->phoneNumber = $params->phoneNumber;
        }
        if (isset($params->email) && !is_null($params->email))
        {
            $this->email = $params->email;
        }
        if (isset($params->website) && !is_null($params->website))
        {
            $this->website = $params->website;
        }
        if (isset($params->emailSupply) && !is_null($params->emailSupply))
        {
            $this->emailSupply = $params->emailSupply;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "id"          => $this->id,
            "clientId"    => $this->clientId,
            "firstName"   => $this->firstName,
            "lastName"    => $this->lastName,
            "phoneNumber" => $this->phoneNumber,
            "email" => $this->email,
            "website" => $this->website,
            "emailSupply" => $this->emailSupply,
        ];
    }

    /**
     * Checks to see if the contact has no new data
     *
     * @return boolean
     */
    public function isEmpty ()
    {
        if (strcmp($this->firstName, "")!==0)
        {
            return false;
        }

        if (strcmp($this->lastName, "")!==0)
        {
            return false;
        }

        if (strcmp($this->email, "")!==0)
        {
            return false;
        }
        if (strcmp($this->website, "")!==0)
        {
            return false;
        }

        if (!($this->phoneNumber instanceof Zend_Db_Expr))
        {
            return false;
        }

        return true;
    }
}
