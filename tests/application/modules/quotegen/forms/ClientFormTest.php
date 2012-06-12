<?php

class Quotegen_Form_ClientFormTest extends PHPUnit_Framework_TestCase {
	
	protected $_form;
	
	public function setUp() {
		$this->_form = new Quotegen_Form_Client ();
		parent::setUp ();
	}
	
	public function tearDown() {
		parent::tearDown ();
		$this->_form = null;
	}
	
	public function testCanRunPHPUNIT() {
		$this->assertTrue ( true, "This should never fail unless unit testing is broken" );
	}
	
	/**
	 * This function returns an array of good data to put into the form
	 */
	public function goodCreateData() {
		return array (
				array (
						'lrobert',
						'tmtwdev',
						'6135551234' 
				)
		);
	}
	
	/**
	 * @dataProvider goodCreateData
	 */
	public function testFormAcceptsValidData($name, $address, $phoneNumber) {
		$data = array (
				'name' => $name,
				'address' => $address,
				'phoneNumber' => $phoneNumber 
		);
		$this->assertTrue ( $this->_form->isValid ( $data ), "Client form did not accept good data." );
	}
	
	/**
	 * Provides bad data for tests to use
	 */
	public function badCreateData() {
		return array (
				array (
						'lee',
						'tmtwdev',
						 '',
				) 
		);
	}
	
	/**
	 * @dataProvider badCreateData
	 */
	public function testFormRejectsBadData($name, $address, $phoneNumber) {
		$data = array (
				'name' => $name,
				'address' => $address,
				'phoneNumber' => $phoneNumber 
		);
		$this->assertFalse ( $this->_form->isValid ( $data ), "User form accepted bad data!" );
	}

}

