<?php

/**
 * Application_Model_LeasingSchema is a model that represents a user row in the database.
 *
 * @author John Sadler
 *        
 */
class Quotegen_Model_LeasingSchema extends My_Model_Abstract {

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

	
	/*
	 * (non-PHPdoc) @see My_Model_Abstract::populate()
	 */
	public function populate($params) {
		if (is_array ( $params )) {
			$params = new ArrayObject ( $params, ArrayObject::ARRAY_AS_PROPS );
		}
		if (isset ( $params->id ) && ! is_null ( $params->id ))
			$this->setId ( $params->id );
		if (isset ( $params->name ) && ! is_null ( $params->name ))
			$this->setName ( $params->name );
	}
	
	/*
	 * (non-PHPdoc) @see My_Model_Abstract::toArray()
	 */
	public function toArray() {
		return array (
				'id' => $this->getId(),
				'name' => $this->getName()		
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
	 * @return the $_name
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * @param string $_name
	 */
	public function setName($_name) {
		$this->_name = $_name;
		return $this;
	}
	
}
