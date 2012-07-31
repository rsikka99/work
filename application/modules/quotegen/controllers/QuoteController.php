<?php

class Quotegen_QuoteController extends Quotegen_Library_Controller_Quote
{

    public function indexAction ()
    {
        // Display all of the quotes
        $mapper = Quotegen_Model_Mapper_Quote::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper));
        
        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set how many items to show
        $paginator->setItemCountPerPage(15);
        
        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    public function deleteAction ()
    {
        $quoteId = $this->_getParam('id', false);
        
        if (! $quoteId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a quote to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $quoteMapper = new Quotegen_Model_Mapper_Quote();
        $quote = $quoteMapper->find($quoteId);
        
        if (! $quote->getId())
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the quote to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $client = Quotegen_Model_Mapper_Client::getInstance()->find($quote->getClientId());
        $message = "Are you sure you want to delete company {$client->getName()} ?";
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (! isset($values ['cancel']))
            {
                if ($form->isValid($values))
                {
                    $quoteDevice = Quotegen_Model_Mapper_QuoteDevice::getInstance()->find($quote);
				                    
                    // Delete entry from QuoteDeviceOption
                    $quoteDeviceOption = new Quotegen_Model_QuoteDeviceOption();
                    Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->deleteAllOptionsForQuoteDevice($quoteDevice->getId());
                    
                    // Delete entry from QuoteDevice
                    Quotegen_Model_Mapper_QuoteDevice::getInstance()->delete($quoteDevice);
                                        
                    $quoteMapper->delete($quote);
                    
                    $this->_helper->flashMessenger(array (
                            'success' => "Quote  {$this->view->escape ($quote->getName())} was deleted successfully." 
                    ));
                    $this->_helper->redirector('index');
                }
            }
            else
            {
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function createAction ()
    {
        $request = $this->getRequest();
        $form = new Quotegen_Form_Quote();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                try
                {
                    if ($form->isValid($values))
                    {
                        try
                        {
                            $userId = Zend_Auth::getInstance()->getIdentity()->id;
                            $userQuoteSetting = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting($userId);
                            
                            $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
                            $quoteSetting->applyOverride($userQuoteSetting);
                            
                            $currentDate = date('Y-m-d H:i:s');
                            $quote = new Quotegen_Model_Quote($values);
                            
                            $quote->setUserId($userId);
                            $quote->setDateCreated($currentDate);
                            $quote->setDateModified($currentDate);
                            $quote->setQuoteDate($currentDate);
                            
                            $quote->setPageCoverageMonochrome($quoteSetting->getPageCoverageMonochrome());
                            $quote->setPageCoverageColor($quoteSetting->getPageCoverageColor());
                            $quote->setPricingConfigId($quoteSetting->getPricingConfigId());
                            
                            $quoteId = Quotegen_Model_Mapper_Quote::getInstance()->insert($quote);
                            
                            // Redirect to the build controller
                            $this->_helper->redirector('index', 'quote_devices', null, array (
                                    'quoteId' => $quoteId 
                            ));
                        }
                        catch ( Exception $e )
                        {
                            $this->_helper->flashMessenger(array (
                                    'danger' => 'There was an error processing this request.  Please try again.' 
                            ));
                            $form->populate($request->getPost());
                        }
                    }
                    else
                    {
                        throw new Zend_Validate_Exception("Form Validation Failed");
                    }
                }
                catch ( Zend_Validate_Exception $e )
                {
                    $form->buildBootstrapErrorDecorators();
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function editAction ()
    {
        $quoteId = $this->_getParam('id', false);
        
        // If they haven't provided an id, send them back to the view all quote
        // page
        if (! $quoteId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a quote to edit first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the quote
        $mapper = new Quotegen_Model_Mapper_Quote();
        $quote = $mapper->find($quoteId);
        // If the quote doesn't exist, send them back t the view all quotes page
        if (! $quote)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the quote to edit.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_Quote();
        
        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($quote->toArray());
        
        // Make sure we are posting data
        if ($request->isPost())
        {
            // Get the post data
            $values = $request->getPost();
            
            // If we cancelled we don't need to validate anything
            if (! isset($values ['cancel']))
            {
                try
                {
                    // Validate the form
                    if ($form->isValid($values))
                    {
                        $mapper = new Quotegen_Model_Mapper_Quote();
                        $quote = new Quotegen_Model_Quote();
                        $quote->populate($values);
                        $quote->setId($quoteId);
                        
                        // Save to the database with cascade insert turned on
                        $quoteId = $mapper->save($quote, $quoteId);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Quote was updated sucessfully." 
                        ));
                    }
                    else
                    {
                        throw new InvalidArgumentException("Please correct the errors below");
                    }
                }
                catch ( InvalidArgumentException $e )
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => $e->getMessage() 
                    ));
                }
            }
            else
            {
                // User has cancelled. We could do a redirect here if we wanted.
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function viewAction ()
    {
        $this->view->quote = Quotegen_Model_Mapper_Quote::getInstance()->find($this->_getParam('id', false));
    }
}

