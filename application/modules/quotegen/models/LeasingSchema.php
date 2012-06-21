<?php

/**
 * Application_Model_LeasingSchema is a model that represents a user row in the database.
 *
 * @author John Sadler
 *        
 */
class Quotegen_Model_LeasingSchema extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    /**
     * The name of the schema
     *
     * @var string
     */
    protected $_name;
    /**
     * All terms
     *
     * @var array
     */
    protected $_terms;
    /**
     * All ranges
     *
     * @var array
     */
    protected $_ranges;
    /**
     * 2 dimensional array rates.
     * First key is term id.
     * Second key is range id.
     *
     * @var array
     */
    protected $_rates;
    
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
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'name' => $this->getName() 
        );
    }
    
    /**
     * Validates the leasing schema
     */
    public function isValid ()
    {
        
    }

    /**
     *
     * @return the $_id
     */
    public function getId ()
    {
        return $this->_id;
    }

    /**
     *
     * @param number $_id            
     */
    public function setId ($_id)
    {
        $this->_id = $_id;
        return $this;
    }

    /**
     *
     * @return the $_name
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     *
     * @param string $_name            
     */
    public function setName ($_name)
    {
        $this->_name = $_name;
        return $this;
    }

    /**
     * Gets all terms for leasing schema
     *
     * @return the $_terms
     */
    public function getTerms ()
    {
        if (! isset($this->_terms))
        {
            $this->_terms = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->fetchAllForLeasingSchema($this->getId());
        }
        return $this->_terms;
    }

    /**
     * Sets all terms for leasing schema
     *
     * @param multitype: $_terms            
     */
    public function setTerms ($_terms)
    {
        $this->_terms = $_terms;
        return $this;
    }

    /**
     * Gets all ranges for leasing schema
     *
     * @return the $_ranges
     */
    public function getRanges ()
    {
        if (! isset($this->_ranges))
        {
            $this->_ranges = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance()->fetchAllForLeasingSchema($this->getId());
        }
        return $this->_ranges;
    }

    /**
     * Sets all ranges for leasing schema
     *
     * @param multitype: $_ranges            
     */
    public function setRanges ($_ranges)
    {
        $this->_ranges = $_ranges;
        return $this;
    }

    /**
     * Gets all rates for leasing schema
     *
     * @return the $_rates
     *         2 dimensional array rates.
     *         First key is term id.
     *         Second key is range id.
     */
    public function getRates ()
    {
        if (! isset($this->_rates))
        {
            $this->_rates = Quotegen_Model_Mapper_LeasingSchemaRate::getInstance()->fetchAllForLeasingSchema($this->getId());
        }
        return $this->_rates;
    }

    /**
     * Sets all rates for leasing schema
     *
     * @param multitype: $_rates
     *            2 dimensional array rates.
     *            First key is term id.
     *            Second key is range id.
     */
    public function setRates ($_rates)
    {
        $this->_rates = $_rates;
        return $this;
    }

}
