<?php

/**
 * Application_Model_LeasingSchemaRates is a model that represents a user row in the database.
 *
 * @author John Sadler
 *        
 */
class Quotegen_Model_LeasingSchemaRates extends My_Model_Abstract {
	
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
			$this->setLeaseingSchemaRangeId( $params->leasingSchemaRangeId );
		if (isset ( $params->rate ) && ! is_null ( $params->rate ))
			$this->setRate ( $params->rate );
	}
	
	/*
	 * (non-PHPdoc) @see My_Model_Abstract::toArray()
	 */
	public function toArray() {
		return array (
				'leasingSchemaTermId' => $this->getId(),	
				'leasingSchemaRangeId' => $this->getLeaseingSchemasId(),	
				'rate' => $this->getMonths()
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

}
