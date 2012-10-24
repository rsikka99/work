<?php

/**
 * Quotegen_Model_Region
 *
 * @author Tyson Riehl
 *        
 */
class Quotegen_Model_Region extends My_Model_Abstract
{
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    
    /**
     * The id of the country
     *
     * @var string
     */
    protected $_countryId;
    
    /**
     * The region of the country
     *
     * @var string
     */
    protected $_region;
    
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
        if (isset($params->name) && ! is_null($params->name))
            $this->setName($params->name);
        if (isset($params->region) && ! is_null($params->region))
            $this->setRegion($params->region);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'countryId' => $this->getCountryId(),
                'region' => $this->getRegion()
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
     * Getter for $_name
     *
     * @return string
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Setter for $_name
     *
     * @param string $_name
     *            The new value
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
    }
    
	/**
     * Getter for region
     *
     * @return string
     */
    public function getRegion ()
    {
        return $this->_region;
    }

	/**
     * Setter for region
     *
     * @param string region The new value
     */
    public function setRegion ($_region)
    {
        $this->_region = $_region;
    }

    
    
}
