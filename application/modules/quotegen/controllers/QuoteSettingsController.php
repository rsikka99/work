<?php

class Quotegen_QuoteSettingsController extends Zend_Controller_Action {
	
	public function init() {
		/* Initialize action controller here */
	}
	
	public function indexAction() {
		// Get the users quote settings
		$quoteSettingsId = 1;
		
		$mapper = new Quotegen_Model_Mapper_QuoteSettings();
		$quoteSetting = $mapper->find ( $quoteSettingsId );
		
		// If the quote setting record doesn't exist, send them back
		if (! $quoteSetting) {
			$this->_helper->flashMessenger ( array (
					'danger' => 'There was an error selecting quote settings.' 
			) );
			$this->_redirect ( '/quotegen' );
		}
		
		// Create a new form with the mode and roles set
		$form = new Quotegen_Form_QuoteSettings();
		
		// Prepare the data for the form
		$request = $this->getRequest ();
		$form->populate ( $quoteSetting->toArray () );
		
		// Make sure we are posting data
		if ($request->isPost ()) {
			// Get the post data
			$values = $request->getPost ();
			
			// If we cancelled we don't need to validate anything
			if (! isset ( $values ['cancel'] )) {
				try {
					// Validate the form
					if ($form->isValid ( $values )) {
						$mapper = new Quotegen_Model_Mapper_QuoteSettings ();
						$quoteSetting = new Quotegen_Model_QuoteSettings ();
						$quoteSetting->populate ( $values );
						$quoteSetting->setId ( $quoteSettingsId );
						
						// Save to the database with cascade insert turned on
						$clientId = $mapper->save ( $quoteSetting, $quoteSettingsId );
						
						$this->_helper->flashMessenger ( array (
								'success' => "Settings have been updated sucessfully." 
						) );
					} else {
						throw new InvalidArgumentException ( "Please correct the errors below" );
					}
				} catch ( InvalidArgumentException $e ) {
					$this->_helper->flashMessenger ( array (
							'danger' => $e->getMessage () 
					) );
				}
			} else {
				// User has cancelled. We could do a redirect here if we wanted.
				$this->_redirect ( '/quotegen' );
			}
		}
		$this->view->form = $form;
	
	}

}

