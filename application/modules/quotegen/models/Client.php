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
                'legalName' => $this->getLegalName() 
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
     * Setter for $_accountNumber
     *
     * @param string $_accountNumber
     *            The new value
     */
    public function setAccountNumber ($_accountNumber)
    {
        $this->_accountNumber = $_accountNumber;
    }

    /**
     * Setter for $_companyName
     *
     * @param string $_companyName
     *            The new value
     */
    public function setCompanyName ($_companyName)
    {
        $this->_companyName = $_companyName;
    }

    /**
     * Setter for $_legalName
     *
     * @param string $_legalName
     *            The new value
     */
    public function setLegalName ($_legalName)
    {
        $this->_legalName = $_legalName;
    }
    
    /**
     * Gets the address of this client
     * @return Address <Quotegen_Model_Address>
     */
    public function getAddress(){
        return Quotegen_Model_Mapper_Address::getInstance()->getAddressByClientId($this->getId());
    }
}
