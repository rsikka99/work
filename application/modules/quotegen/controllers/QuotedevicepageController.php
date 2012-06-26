<?php

class Quotegen_QuoteDevicePageController extends Zend_Controller_Action
{

    /**
     * Displays all pages for a device
     */
    public function indexAction ()
    {
        // Display all of the clients
        $mapper = Quotegen_Model_Mapper_QuoteDevicePage::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper));
        
        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Set how many items to show
        $paginator->setItemCountPerPage(15);
        
        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    /**
     * Deletes pages from a device
     */
    public function deleteAction ()
    {
        $quoteDeviceId = $this->_getParam('id', false);
        
        if (! $quoteDeviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a device to delete pages from first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $mapper = Quotegen_Model_Mapper_QuoteDevicePage::getInstance();
        $quoteDevicePage = $mapper->find($quoteDeviceId);
        
        if (! $quoteDevicePage)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the pages to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $message = "Are you sure you want to delete pages from this device?";
        $form = new Application_Form_Delete($message);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['cancel']))
            {
                // delete client from database
                if ($form->isValid($values))
                {
                    $mapper->delete($quoteDevicePage);
                    $this->_helper->flashMessenger(array (
                            'success' => "Pages for the device were deleted successfully." 
                    ));
                    $this->_helper->redirector('index');
                }
            }
            else // go back
            {
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Creates pages for a device
     */
    public function createAction ()
    {
        // TODO: createAction
        $request = $this->getRequest();
        $form = new Quotegen_Form_QuoteDevicePage();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (! isset($values ['cancel']))
            {
                
                try
                {
                    if ($form->isValid($values))
                    {
                        
                        // Save to the database
                        try
                        {
                            $mapper = Quotegen_Model_Mapper_QuoteDevicePage::getInstance();
                            $quoteDevicePage = new Quotegen_Model_QuoteDevicePage();
                            $quoteDevicePage->populate($values);
                            $quoteDeviceId = $mapper->insert($quoteDevicePage);
                            
                            $this->_helper->flashMessenger(array (
                                    'success' => "Pages for device were added successfully." 
                            ));
                            
                            // Reset the form after everything is saved successfully
                            $form->reset();
                        }
                        catch ( Zend_Db_Statement_Mysqli_Exception $e )
                        {
                            // Check to see what error code was thrown
                            switch ($e->getCode())
                            {
                                // Duplicate column
                                case 1062 :
                                    $this->_helper->flashMessenger(array (
                                            'danger' => 'Pages for device already exist.' 
                                    ));
                                    break;
                                default :
                                    $this->_helper->flashMessenger(array (
                                            'danger' => 'Error saving to database. Please try again.' 
                                    ));
                                    break;
                            }
                            
                            $form->populate($request->getPost());
                        }
                        catch ( Exception $e )
                        {
                            $this->_helper->flashMessenger(array (
                                    'danger' => 'There was an error processing this request. Please try again.' 
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

    /**
     * Edits pages for a device
     */
    public function editAction ()
    {
        $quoteDeviceId = $this->_getParam('id', false);
        
        // If they haven't provided an id, send them back to the view all client page
        if (! $quoteDeviceId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a device to edit pages for first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Get the pages for a device
        $mapper = Quotegen_Model_Mapper_QuoteDevicePage::getInstance();
        $quoteDevicePage = $mapper->find($quoteDeviceId);
        
        // If the client doesn't exist, send them back t the view all clients page
        if (! $quoteDevicePage)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the pages for the device to edit.' 
            ));
            $this->_helper->redirector('index');
        }
        
        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_Client();
        
        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($quoteDevicePage->toArray());
        
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
                        $mapper = Quotegen_Model_Mapper_QuoteDevicePage::getInstance();
                        $quoteDevicePage = new Quotegen_Model_QuoteDevicePage();
                        $quoteDevicePage->populate($values);
                        $quoteDevicePage->setId($quoteDeviceId);
                        
                        // Save to the database with cascade insert turned on
                        $quoteDeviceId = $mapper->save($quoteDevicePage, $quoteDeviceId);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Pages for device were updated sucessfully." 
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
}

