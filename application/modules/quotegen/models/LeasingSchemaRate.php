<?php

/**
 * Application_Model_LeasingSchemaRate is a model that represents a user row in the database.
 *
 * @author John Sadler
 *        
 */
class Quotegen_Model_LeasingSchemaRate extends My_Model_Abstract {
	
	/**
	 * The related leasing schema term
	 *
	 * @var int
	 */
	protected $_leasingSchemaTermId = 0;
	/**
	 * The related leasing schema range
	 *
	 * @var int
	 */
	protected $_leasingSchemaRangeId = 0;
	/**
	 * The rate for the term and range
	 *
	 * @var double
	 */
	protected $_rate = 0;
	/**
	 * The term for the leasing schema rate
	 *
	 * @var Quotegen_Model_LeasingSchemaTerm
	 */
	protected $_term;
	/**
	 * The range for the leasing schema rate
	 *
	 * @var Quotegen_Model_LeasingSchemaRange
	 */
	protected $_range;
	
	
	/*
	 * (non-PHPdoc) @see My_Model_Abstract::populate()
	 */
	public function populate($params) {
		if (is_array ( $params )) {
			$params = new ArrayObject ( $params, ArrayObject::ARRAY_AS_PROPS );
		}
		if (isset ( $params->leasingSchemaTermId ) && ! is_null ( $params->leasingSchemaTermId ))
			$this->setLeasingSchemaTermId( $params->leasingSchemaTermId );
		if (isset ( $params->leasingSchemaRangeId ) && ! is_null ( $params->leasingSchemaRangeId ))
			$this->setLeasingSchemaRangeId( $params->leasingSchemaRangeId );
		if (isset ( $params->rate ) && ! is_null ( $params->rate ))
			$this->setRate ( $params->rate );
	}
	
	/*
	 * (non-PHPdoc) @see My_Model_Abstract::toArray()
	 */
	public function toArray() {
		return array (
				'leasingSchemaTermId' => $this->getLeasingSchemaTermId(),	
				'leasingSchemaRangeId' => $this->getLeasingSchemaRangeId(),	
				'rate' => $this->getRate()
		);
	}
	
	/**
	 * @return the $_leasingSchemaTermId
	 */
	public function getLeasingSchemaTermId() {
		return $this->_leasingSchemaTermId;
	}

	/**
	 * @param number $_leasingSchemaTermId
	 */
	public function setLeasingSchemaTermId($_leasingSchemaTermId) {
		$this->_leasingSchemaTermId = $_leasingSchemaTermId;
		return $this;
	}

	/**
	 * @return the $_leasingSchemaRangeId
	 */
	public function getLeasingSchemaRangeId() {
		return $this->_leasingSchemaRangeId;
	}

	/**
	 * @param number $_leasingSchemaRangeId
	 */
	public function setLeasingSchemaRangeId($_leasingSchemaRangeId) {
		$this->_leasingSchemaRangeId = $_leasingSchemaRangeId;
		return $this;
	}

	/**
	 * @return the $_rate
	 */
	public function getRate() {
		return $this->_rate;
	}

	/**
	 * @param number $_rate
	 */
	public function setRate($_rate) {
		$this->_rate = $_rate;
		return $this;
	}
	/**
	 * Gets the term for the leasing schema rate
	 * 
     * @return the $_term
     */
    public function getTerm ()
    {
        if (! isset($this->_term))
        {
            $this->_term = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->find($this->getLeasingSchemaTermId());
        }
        return $this->_term;
    }

	/**
	 * Sets the term for the leasing schema rate
	 * 
     * @param Quotegen_Model_LeasingSchemaTerm $_term
     */
    public function setTerm ($_term)
    {
        $this->_term = $_term;
        return $this;
    }

	/**
	 * Gets the range for the leasing schema rate
	 * 
     * @return the $_range
     */
    public function getRange ()
    {
        if (! isset($this->_range))
        {
            $this->_range = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance()->find($this->getLeasingSchemaRangeId());
        }
        return $this->_range;
    }

	/**
	 * Sets the range for the leasing schema rate
	 * 
     * @param Quotegen_Model_LeasingSchemaRange $_range
     */
    public function setRange ($_range)
    {
        $this->_range = $_range;
        return $this;
    }


}
