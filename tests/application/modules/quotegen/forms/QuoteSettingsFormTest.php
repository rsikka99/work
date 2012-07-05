<?php

class Quotegen_Form_QuoteSettingsFormTest extends PHPUnit_Framework_TestCase {
	
	protected $_form;
	
	public function setUp() {
		$this->_form = new Quotegen_Form_QuoteSetting();
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
						6,
						24,
						15,
						10,
						1 
				)
		);
	}
	
	/**
	 * @dataProvider goodCreateData
	 */
	public function testFormAcceptsValidData($pageCoverageMonochrome, $pageCoverageColor, $deviceMargin, $pageMargin, $tonerPreference) {
		$data = array (
				'pageCoverageMonochrome' => $pageCoverageMonochrome,
				'pageCoverageColor' => $pageCoverageColor,
				'deviceMargin' => $deviceMargin, 
				'pageMargin' => $pageMargin, 
				'tonerPreference' => $tonerPreference
		);
		$this->assertTrue ( $this->_form->isValid ( $data ), "Quote Settings form did not accept good data." );
	}
	
	/**
	 * Provides bad data for tests to use
	 */
	public function badCreateData() {
		return array (
				array (
						'a',
						24,
						15,
						10,
						5 
				)
		);
	}
	
	/**
	 * @dataProvider badCreateData
	 */
	public function testFormRejectsBadData($pageCoverageMonochrome, $pageCoverageColor, $deviceMargin, $pageMargin, $tonerPreference) {
		$data = array (
				'pageCoverageMonochrome' => $pageCoverageMonochrome,
				'pageCoverageColor' => $pageCoverageColor,
				'deviceMargin' => $deviceMargin, 
				'pageMargin' => $pageMargin, 
				'tonerPreference' => $tonerPreference
		);
		$this->assertFalse ( $this->_form->isValid ( $data ), "Quote Settings form accepted bad data!" );
	}

}

