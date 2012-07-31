<?php

/**
 * Application_Model_LeasingSchemaTerm is a model that represents a user row in the database.
 *
 * @author John Sadler
 *        
 */
class Quotegen_Model_LeasingSchemaTerm extends My_Model_Abstract
{
    
    /**
     * The id assigned by the database
     *
     * @var int
     */
    protected $_id = 0;
    /**
     * The related leasing schema id
     *
     * @var int
     */
    protected $_leasingSchemaId = 0;
    /**
     * The term value in months
     *
     * @var int
     */
    protected $_months = 0;
    
    /**
     * Leasing Schema
     *
     * @var Quotegen_Model_LeasingSchema
     */
    protected $_leasingSchema;
    
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
        if (isset($params->leasingSchemaId) && ! is_null($params->leasingSchemaId))
            $this->setLeasingSchemaId($params->leasingSchemaId);
        if (isset($params->months) && ! is_null($params->months))
            $this->setMonths($params->months);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'leasingSchemaId' => $this->getLeasingSchemaId(), 
                'months' => $this->getMonths() 
        );
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
     * @return the $_leaseingSchemaId
     */
    public function getLeasingSchemaId ()
    {
        return $this->_leasingSchemaId;
    }

    /**
     *
     * @param number $_leasingSchemaId            
     */
    public function setLeasingSchemaId ($_leasingSchemaId)
    {
        $this->_leasingSchemaId = $_leasingSchemaId;
        return $this;
    }

    /**
     *
     * @return the $_months
     */
    public function getMonths ()
    {
        return $this->_months;
    }

    /**
     *
     * @param number $_months            
     */
    public function setMonths ($_months)
    {
        $this->_months = $_months;
        return $this;
    }

    /**
     * Gets the leasing schema
     * 
     * @return Quotegen_Model_LeasingSchema
     */
    public function getLeasingSchema ()
    {
        if (! isset($this->_leasingSchema))
        {
            $this->_leasingSchema = Quotegen_Model_Mapper_LeasingSchema::getInstance()->find($this->getLeasingSchemaId());
        }
        return $this->_leasingSchema;
    }

    /**
     * Sets the leasing schema
     * 
     * @param Quotegen_Model_LeasingSchema $_leasingSchema            
     */
    public function setLeasingSchema ($_leasingSchema)
    {
        $this->_leasingSchema = $_leasingSchema;
        return $this;
    }
}
