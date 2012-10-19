<?php

/**
 * Quotegen_Model_Client
 *
 * @author Lee Robert
 *        
 */
class Quotegen_Model_Address extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The address line 1
     *
     * @var string
     */
    protected $_addressLine1;
    
    /**
     * The address line 2
     *
     * @var string
     */
    protected $_addressLine2;
    
    /**
     * The city of the address
     *
     * @var string
     */
    protected $_city;
    
    /**
     * The region (province or state)
     *
     * @var string
     */
    protected $_region;
    /**
     * The region (province or state)
     *
     * @var string
     */
    protected $_postCode;
    /**
     * The id of the country
     *
     * @var string
     */
    protected $_countryId;
    
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
        if (isset($params->addressLine1) && ! is_null($params->addressLine1))
            $this->setAddressLine1($params->addressLine1);
        if (isset($params->addressLine2) && ! is_null($params->addressLine2))
            $this->setAddressLine2($params->addressLine2);
        if (isset($params->city) && ! is_null($params->city))
            $this->setCity($params->city);
        if (isset($params->region) && ! is_null($params->region))
            $this->setRegion($params->region);
        if (isset($params->postCode) && ! is_null($params->postCode))
            $this->setPostCode($params->postCode);
        if (isset($params->countryId) && ! is_null($params->countryId))
            $this->setCountryId($params->countryId);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'addressLine1' => $this->getAddressLine1(),
                'addressLine2' => $this->getAddressLine2(),
                'city' => $this->getCity(),
                'region' => $this->getRegion(),
                'postCode' => $this->getPostCode(),
                'countryId' => $this->getCountryId()                
        )
        ;
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
     * Getter for $_addressLine1
     *
     * @return string
     */
    public function getAddressLine1 ()
    {
        return $this->_addressLine1;
    }

    /**
     * Getter for $_addressLine2
     *
     * @return string
     */
    public function getAddressLine2 ()
    {
        return $this->_addressLine2;
    }

    /**
     * Getter for $_city
     *
     * @return string
     */
    public function getCity ()
    {
        return $this->_city;
    }

    /**
     * Getter for $_region
     *
     * @return string
     */
    public function getRegion ()
    {
        return $this->_region;
    }

    /**
     * Getter for $_postCode
     *
     * @return string
     */
    public function getPostCode ()
    {
        return $this->_postCode;
    }

    /**
     * Getter for $_countryId
     *
     * @return string
     */
    public function getCountryId ()
    {
        return $this->_countryId;
    }

    /**
     * Setter for $_addressLine1
     *
     * @param string $_addressLine1
     *            The new value
     */
    public function setAddressLine1 ($_addressLine1)
    {
        $this->_addressLine1 = $_addressLine1;
    }

    /**
     * Setter for $_addressLine2
     *
     * @param string $_addressLine2
     *            The new value
     */
    public function setAddressLine2 ($_addressLine2)
    {
        $this->_addressLine2 = $_addressLine2;
    }

    /**
     * Setter for $_city
     *
     * @param string $_city
     *            The new value
     */
    public function setCity ($_city)
    {
        $this->_city = $_city;
    }

    /**
     * Setter for $_region
     *
     * @param string $_region
     *            The new value
     */
    public function setRegion ($_region)
    {
        $this->_region = $_region;
    }

    /**
     * Setter for $_postCode
     *
     * @param string $_postCode
     *            The new value
     */
    public function setPostCode ($_postCode)
    {
        $this->_postCode = $_postCode;
    }

    /**
     * Setter for $_countryId
     *
     * @param string $_countryId
     *            The new value
     */
    public function setCountryId ($_countryId)
    {
        $this->_countryId = $_countryId;
    }
}
