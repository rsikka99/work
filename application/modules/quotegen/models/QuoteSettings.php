<?php

/**
 * Application_Model_QuoteSettings is a model that represents a quoteSetting row in the database.
 *
 * @author John Sadler
 *        
 */
class Quotegen_Model_QuoteSettings extends My_Model_Abstract {
	
	/**
	 * The id assigned by the database
	 *
	 * @var int
	 */
	protected $_id = 0;

	/*
	 * (non-PHPdoc) @see My_Model_Abstract::populate()
	 */
	public function populate($params) {
		if (is_array ( $params )) {
			$params = new ArrayObject ( $params, ArrayObject::ARRAY_AS_PROPS );
		}
	}
	
	/*
	 * (non-PHPdoc) @see My_Model_Abstract::toArray()
	 */
	public function toArray() {
		return array (
				'id' => $this->getId()		
		);
	}

}
