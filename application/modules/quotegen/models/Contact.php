<?php

/**
 * Quotegen_Model_Contact
 *
 * @author Tyson Riehl
 *        
 */
class Quotegen_Model_Contact extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The clientId assigned by the database
     *
     * @var int
     */
    protected $_clientId;
    
    /**
     * The first name of the contact
     *
     * @var string
     */
    protected $_firstName;
    
    /**
     * The first name of the contact
     *
     * @var string
     */
    protected $_lastName;
    
    /**
     * The country code of the phone number
     *
     * @var string
     */
    protected $_countryCode;
    
    /**
     * The area code of the phone number
     *
     * @var string
     */
    protected $_areaCode;
    
    /**
     * The exchange code of the phone number
     *
     * @var string
     */
    protected $_exchangeCode;
    
    /**
     * The number of the phone number
     *
     * @var string
     */
    protected $_number;
    
    /**
     * The extension of the phone number
     *
     * @var string
     */
    protected $_extension;
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::populate()
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }
        if (isset($params->id) && ! is_null($params->id))
            $this->setId($params->id);
        if (isset($params->clientId) && ! is_null($params->clientId))
            $this->setClientId($params->clientId);
        if (isset($params->firstName) && ! is_null($params->firstName))
            $this->setFirstName($params->firstName);
        if (isset($params->lastName) && ! is_null($params->lastName))
            $this->setLastName($params->lastName);
        if (isset($params->countryCode) && ! is_null($params->countryCode))
            $this->setCountryCode($params->countryCode);
        if (isset($params->areaCode) && ! is_null($params->areaCode))
            $this->setAreaCode($params->areaCode);
        if (isset($params->exchangeCode) && ! is_null($params->exchangeCode))
            $this->setExchangeCode($params->exchangeCode);
        if (isset($params->number) && ! is_null($params->number))
            $this->setNumber($params->number);
        if (isset($params->extension))
            $this->setExtension($params->extension);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'clientId' => $this->getClientId(), 
                'firstName' => $this->getFirstName(), 
                'lastName' => $this->getLastName(), 
                'countryCode' => $this->getCountryCode(), 
                'areaCode' => $this->getAreaCode(), 
                'exchangeCode' => $this->getExchangeCode(), 
                'number' => $this->getNumber(), 
                'extension' => $this->getExtension() 
        );
    }

    /**
     * Gets the id of the object
     *
     * @return number The id of the object
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     * Sets the id of the object
     *
     * @param number $_id
     *            the new id
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
    }

    /**
     * Getter for $_clientId
     *
     * @return number
     */
    public function getClientId ()
    {
        return $this->_clientId;
    }

    /**
     * Setter for $_clientId
     *
     * @param number $_clientId
     *            The new value
     */
    public function setClientId ($_clientId)
    {
        $this->_clientId = $_clientId;
    }

    /**
     * Getter for $_firstName
     *
     * @return string
     */
    public function getFirstName ()
    {
        return $this->_firstName;
    }

    /**
     * Getter for $_lastName
     *
     * @return string
     */
    public function getLastName ()
    {
        return $this->_lastName;
    }

    /**
     * Getter for $_countryCode
     *
     * @return string
     */
    public function getCountryCode ()
    {
        return $this->_countryCode;
    }

    /**
     * Getter for $_areaCode
     *
     * @return string
     */
    public function getAreaCode ()
    {
        return $this->_areaCode;
    }

    /**
     * Getter for $_exchangeCode
     *
     * @return string
     */
    public function getExchangeCode ()
    {
        return $this->_exchangeCode;
    }

    /**
     * Getter for $_number
     *
     * @return string
     */
    public function getNumber ()
    {
        return $this->_number;
    }

    /**
     * Getter for $_extension
     *
     * @return string
     */
    public function getExtension ()
    {
        return $this->_extension;
    }

    /**
     * Setter for $_firstName
     *
     * @param string $_firstName
     *            The new value
     */
    public function setFirstName ($_firstName)
    {
        $this->_firstName = $_firstName;
    }

    /**
     * Setter for $_lastName
     *
     * @param string $_lastName
     *            The new value
     */
    public function setLastName ($_lastName)
    {
        $this->_lastName = $_lastName;
    }

    /**
     * Setter for $_countryCode
     *
     * @param string $_countryCode
     *            The new value
     */
    public function setCountryCode ($_countryCode)
    {
        $this->_countryCode = $_countryCode;
    }

    /**
     * Setter for $_areaCode
     *
     * @param string $_areaCode
     *            The new value
     */
    public function setAreaCode ($_areaCode)
    {
        $this->_areaCode = $_areaCode;
    }

    /**
     * Setter for $_exchangeCode
     *
     * @param string $_exchangeCode
     *            The new value
     */
    public function setExchangeCode ($_exchangeCode)
    {
        $this->_exchangeCode = $_exchangeCode;
    }

    /**
     * Setter for $_number
     *
     * @param string $_number
     *            The new value
     */
    public function setNumber ($_number)
    {
        $this->_number = $_number;
    }

    /**
     * Setter for $_extension
     *
     * @param string $_extension
     *            The new value
     */
    public function setExtension ($_extension)
    {
        $this->_extension = $_extension;
    }
}
