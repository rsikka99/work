<?php

/**
 * Quotegen_Model_Client
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_Client extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The account number of the client
     *
     * @var string
     */
    protected $_accountNumber;
    
    /**
     * The company name
     *
     * @var string
     */
    protected $_companyName;
    
    /**
     * The legal name of the client
     *
     * @var string
     */
    protected $_legalName;
    
    /**
     * The first name of the contact
     *
     * @var string
     */
    protected $_contactFirstName;
    
    /**
     * The first name of the contact
     *
     * @var string
     */
    protected $_contactLastName;
    /**
     * The first name of the contact
     *
     * @var string
     */
    protected $_contactPhone;
    
    /**
     * The address1 of the client
     *
     * @var string
     */
    protected $_companyAddress1;
    
    /**
     * The address2 of the client
     *
     * @var string
     */
    protected $_companyAddress2;
    
    /**
     * The address2 of the client
     *
     * @var string
     */
    protected $_companyCity;
    
    /**
     * The country of the company
     *
     * @var string
     */
    protected $_companyCountry;
    
    /**
     * The address2 of the client
     *
     * @var string
     */
    protected $_companyStateOrProv;
    
    /**
     * The address2 of the client
     *
     * @var string
     */
    protected $_companyZipOrPostalCode;
    
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
        if (isset($params->accountNumber) && ! is_null($params->accountNumber))
            $this->setAccountNumber($params->accountNumber);
        if (isset($params->companyName) && ! is_null($params->companyName))
            $this->setCompanyName($params->companyName);
        if (isset($params->legalName) && ! is_null($params->legalName))
            $this->setLegalName($params->legalName);
        if (isset($params->contactFirstName) && ! is_null($params->contactFirstName))
            $this->setContactFirstName($params->contactFirstName);
        if (isset($params->contactLastName) && ! is_null($params->contactLastName))
            $this->setContactLastName($params->contactLastName);
        if (isset($params->contactPhone) && ! is_null($params->contactPhone))
            $this->setContactPhone($params->contactPhone);
        if (isset($params->companyAddress1) && ! is_null($params->companyAddress1))
            $this->setCompanyAddress1($params->companyAddress1);
        if (isset($params->companyAddress2) && ! is_null($params->companyAddress2))
            $this->setCompanyAddress2($params->companyAddress2);
        if (isset($params->companyCity) && ! is_null($params->companyCity))
            $this->setCompanyCity($params->companyCity);
        if (isset($params->companyCountry) && ! is_null($params->companyCountry))
            $this->setCompanyCountry($params->companyCountry);
        if (isset($params->companyStateOrProv) && ! is_null($params->companyStateOrProv))
            $this->setCompanyStateOrProv($params->companyStateOrProv);
        if (isset($params->companyZipOrPostalCode) && ! is_null($params->companyZipOrPostalCode))
            $this->setCompanyZipOrPostalCode($params->companyZipOrPostalCode);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'accountNumber' => $this->getAccountNumber(), 
                'companyName' => $this->getCompanyName(), 
                'legalName' => $this->getLegalName(), 
                'contactFirstName' => $this->getContactFirstName(), 
                'contactLastName' => $this->getContactLastName(), 
                'contactPhone' => $this->getContactPhone(), 
                'companyAddress1' => $this->getCompanyAddress1(), 
                'companyAddress2' => $this->getCompanyAddress2(), 
                'companyCity' => $this->getCompanyCity(), 
                'companyCountry' => $this->getCompanyCountry(), 
                'companyStateOrProv' => $this->getCompanyStateOrProv(), 
                'companyZipOrPostalCode' => $this->getCompanyZipOrPostalCode() 
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
     * Gets the account number
     *
     * @return int
     */
    public function getAccountNumber ()
    {
        return $this->_accountNumber;
    }

    /**
     * Getter for $_companyName
     *
     * @return string
     */
    public function getCompanyName ()
    {
        return $this->_companyName;
    }

    /**
     * Gets the legal name
     *
     * @return string
     */
    public function getLegalName ()
    {
        return $this->_legalName;
    }

    /**
     * Gets the contacts first name
     *
     * @return string
     */
    public function getContactFirstName ()
    {
        return $this->_contactFirstName;
    }

    /**
     * Gets the contacts last name
     *
     * @return string
     */
    public function getContactLastName ()
    {
        return $this->_contactLastName;
    }

    /**
     * Gets the contacts phone number
     *
     * @return string
     */
    public function getContactPhone ()
    {
        return $this->_contactPhone;
    }

    /**
     * Gets the company address 1
     *
     * @return string
     */
    public function getCompanyAddress1 ()
    {
        return $this->_companyAddress1;
    }

    /**
     * Gets the company address 2
     *
     * @return string
     */
    public function getCompanyAddress2 ()
    {
        return $this->_companyAddress2;
    }

    /**
     * Gets the company City
     *
     * @return string
     */
    public function getCompanyCity ()
    {
        return $this->_companyCity;
    }

    /**
     * Gets the company State or province
     *
     * @return string
     */
    public function getCompanyStateOrProv ()
    {
        return $this->_companyStateOrProv;
    }

    /**
     * Gets the company Zip or Postal Code
     *
     * @return string
     */
    public function getCompanyZipOrPostalCode ()
    {
        return $this->_companyZipOrPostalCode;
    }

    /**
     * Sets the account number of the client
     *
     * @param string $_accountNumber
     *            The new value
     */
    public function setAccountNumber ($_accountNumber)
    {
        $this->_accountNumber = $_accountNumber;
    }

    /**
     * Sets the company name
     *
     * @param string $_companyName
     *            The new value
     */
    public function setCompanyName ($_companyName)
    {
        $this->_companyName = $_companyName;
    }

    /**
     * Sets the legal name of the client
     *
     * @param string $_legalName
     *            The new value
     */
    public function setLegalName ($_legalName)
    {
        $this->_legalName = $_legalName;
    }

    /**
     * Sets the contacts first name
     *
     * @param string $_contactFirstName
     *            The new value
     */
    public function setContactFirstName ($_contactFirstName)
    {
        $this->_contactFirstName = $_contactFirstName;
    }

    /**
     * Sets the contacts last name
     *
     * @param string $_contactLastName
     *            The new value
     */
    public function setContactLastName ($_contactLastName)
    {
        $this->_contactLastName = $_contactLastName;
    }

    /**
     * Sets the contacts phone number
     *
     * @param string $_contactPhone
     *            The new value
     */
    public function setContactPhone ($_contactPhone)
    {
        $this->_contactPhone = $_contactPhone;
    }

    /**
     * Sets the company address 1
     *
     * @param string $_companyAddress1
     *            The new value
     */
    public function setCompanyAddress1 ($_companyAddress1)
    {
        $this->_companyAddress1 = $_companyAddress1;
    }

    /**
     * Sets the company address 2
     *
     * @param string $_companyAddress2
     *            The new value
     */
    public function setCompanyAddress2 ($_companyAddress2)
    {
        $this->_companyAddress2 = $_companyAddress2;
    }

    /**
     * Sets the company city
     *
     * @param string $_companyCity
     *            The new value
     */
    public function setCompanyCity ($_companyCity)
    {
        $this->_companyCity = $_companyCity;
    }

    /**
     * Sets the company state or province
     *
     * @param string $_companyStateOrProv
     *            The new value
     */
    public function setCompanyStateOrProv ($_companyStateOrProv)
    {
        $this->_companyStateOrProv = $_companyStateOrProv;
    }

    /**
     * Sets the company zip or postal code
     *
     * @param string $_companyZipOrPostalCode
     *            The new value
     */
    public function setCompanyZipOrPostalCode ($_companyZipOrPostalCode)
    {
        $this->_companyZipOrPostalCode = $_companyZipOrPostalCode;
    }

    /**
     * Getter for $_companyCountry
     *
     * @return string
     */
    public function getCompanyCountry ()
    {
        return $this->_companyCountry;
    }

    /**
     * Setter for $_companyCountry
     *
     * @param string $_companyCountry
     *            The new value
     */
    public function setCompanyCountry ($_companyCountry)
    {
        $this->_companyCountry = $_companyCountry;
    }
}
