<?php

/**
 * Application_Model_LeasingSchemaTerms is a model that represents a user row in the database.
 *
 * @author John Sadler
 *        
 */
class Quotegen_Model_LeasingSchemaTerms extends My_Model_Abstract {
	
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
	protected $_leaseingSchemaId = 0;
	/**
	 * The term value in months
	 *
	 * @var int
	 */
	protected $_months = 0;

	
	/*
	 * (non-PHPdoc) @see My_Model_Abstract::populate()
	 */
	public function populate($params) {
		if (is_array ( $params )) {
			$params = new ArrayObject ( $params, ArrayObject::ARRAY_AS_PROPS );
		}
		if (isset ( $params->id ) && ! is_null ( $params->id ))
			$this->setId ( $params->id );
		if (isset ( $params->leaseingSchemaId ) && ! is_null ( $params->leaseingSchemaId ))
			$this->setLeaseingSchemaId( $params->leaseingSchemaId );
		if (isset ( $params->months ) && ! is_null ( $params->months ))
			$this->setMonths ( $params->months );
	}
	
	/*
	 * (non-PHPdoc) @see My_Model_Abstract::toArray()
	 */
	public function toArray() {
		return array (
				'id' => $this->getId(),	
				'leasingSchemaId' => $this->getLeaseingSchemaId(),	
				'months' => $this->getMonths()
		);
	}
	
	/**
	 * @return the $_id
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @param number $_id
	 */
	public function setId($_id) {
		$this->_id = $_id;
		return $this;
	}

	/**
	 * @return the $_leaseingSchemaId
	 */
	public function getLeaseingSchemaId() {
		return $this->_leaseingSchemaId;
	}

	/**
	 * @param number $_leaseingSchemaId
	 */
	public function setLeaseingSchemaId($_leaseingSchemaId) {
		$this->_leaseingSchemaId = $_leaseingSchemaId;
		return $this;
	}

	/**
	 * @return the $_months
	 */
	public function getMonths() {
		return $this->_months;
	}

	/**
	 * @param number $_months
	 */
	public function setMonths($_months) {
		$this->_months = $_months;
		return $this;
	}

	
}
