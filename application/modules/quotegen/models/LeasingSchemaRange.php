<?php

/**
 * Application_Model_LeasingSchemaRange is a model that represents a user row in the database.
 *
 * @author John Sadler
 *        
 */
class Quotegen_Model_LeasingSchemaRange extends My_Model_Abstract
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
     * The minimum value in the range
     *
     * @var double
     */
    protected $_startRange = 0;
    
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
        if (isset($params->startRange) && ! is_null($params->startRange))
            $this->setStartRange($params->startRange);
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Abstract::toArray()
     */
    public function toArray ()
    {
        return array (
                'id' => $this->getId(), 
                'leasingSchemaId' => $this->getLeasingSchemaId(), 
                'startRange' => $this->getStartRange() 
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
     * @return the $_leasingSchemaId
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
     * @return the $_startRange
     */
    public function getStartRange ()
    {
        return $this->_startRange;
    }

    /**
     *
     * @param number $_startRange            
     */
    public function setStartRange ($_startRange)
    {
        $this->_startRange = $_startRange;
        return $this;
    }
}
