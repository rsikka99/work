<?php

class Quotegen_QuoteSettingsController extends Zend_Controller_Action {
	
	public function init() {
		/* Initialize action controller here */
	}
	
	public function indexAction() {
		// Display all of the quote settings
		$this->view->quotesettings = Quotegen_Model_Mapper_QuoteSettings::getInstance ()->fetchAll ();
	}

}

