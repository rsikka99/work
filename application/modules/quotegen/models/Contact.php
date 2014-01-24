<?php

/**
 * Class Quotegen_Model_Contact
 */
class Quotegen_Model_Contact extends My_Model_Abstract
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
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var int
     */
    public $countryCode;

    /**
     * @var int
     */
    public $areaCode;

    /**
     * @var int
     */
    public $exchangeCode;

    /**
     * @var int
     */
    public $number;

    /**
     * @var int
     */
    public $extension;

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

        if (isset($params->countryCode) && !is_null($params->countryCode))
        {
            $this->countryCode = $params->countryCode;
        }

        if (isset($params->areaCode) && !is_null($params->areaCode))
        {
            $this->areaCode = $params->areaCode;
        }

        if (isset($params->exchangeCode) && !is_null($params->exchangeCode))
        {
            $this->exchangeCode = $params->exchangeCode;
        }

        if (isset($params->number) && !is_null($params->number))
        {
            $this->number = $params->number;
        }

        if (isset($params->extension) && !is_null($params->extension))
        {
            $this->extension = $params->extension;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"           => $this->id,
            "clientId"     => $this->clientId,
            "firstName"    => $this->firstName,
            "lastName"     => $this->lastName,
            "countryCode"  => $this->countryCode,
            "areaCode"     => $this->areaCode,
            "exchangeCode" => $this->exchangeCode,
            "number"       => $this->number,
            "extension"    => $this->extension,
        );
    }

    /**
     * Checks to see if the contact has no new data
     *
     * @return boolean
     */
    public function isEmpty ()
    {
        if (strcmp($this->firstName, ""))
        {
            return false;
        }
        if (strcmp($this->lastName, ""))
        {
            return false;
        }
        if (!($this->areaCode instanceof Zend_Db_Expr))
        {
            return false;
        }
        if (!($this->exchangeCode instanceof Zend_Db_Expr))
        {
            return false;
        }
        if (!($this->number instanceof Zend_Db_Expr))
        {
            return false;
        }

        return true;
    }
}
