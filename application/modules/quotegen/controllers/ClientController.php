<?php

class Quotegen_ClientController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // Display all of the clients
        $mapper = Quotegen_Model_Mapper_Client::getInstance();
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
        // TODO: deleteAction
        $clientId = $this->_getParam('id', false);
        
        if (! $clientId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a user to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $mapper = new Quotegen_Model_Mapper_Client();
        $client = $mapper->find($clientId);
        
        if (! $clientId)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the user to delete.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $message = "Are you sure you want to delete {$client->getName()}?";
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
                    $mapper->delete($client);
                    $this->_helper->flashMessenger(array (
                            'success' => "Client  {$this->view->escape ( $client->getName() )} was deleted successfully." 
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

    public function createAction ()
    {
        // TODO: createAction
        $request = $this->getRequest();
        $form = new Quotegen_Form_Client();
        
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
                            $mapper = new Quotegen_Model_Mapper_Client();
                            $client = new Quotegen_Model_Client();
                            $values ['userId'] = Zend_Auth::getInstance()->getIdentity()->id;
                            $client->populate($values);
                            $clientId = $mapper->insert($client);
                            
                            $this->_helper->flashMessenger(array (
                                    'success' => "Client " . $this->view->escape($values ["name"]) . " was added successfully." 
                            ));
                            
                            // Reset the form after everything is saved
        // successfully
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
                                            'danger' => 'Client already exists.' 
                                    ));
                                    break;
                                default :
                                    $this->_helper->flashMessenger(array (
                                            'danger' => 'Error saving to database.  Please try again.' 
                                    ));
                                    break;
                            }
                            
                            $form->populate($request->getPost());
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
        $clientId = $this->_getParam('id', false);
        
        // If they haven't provided an id, send them back to the view all client
        // page
        if (! $clientId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a client to edit first.' 
            ));
            $this->_redirect('/quotegen/client');
        }
        
        // Get the client
        $mapper = new Quotegen_Model_Mapper_Client();
        $client = $mapper->find($clientId);
        // If the user doesn't exist, send them back t the view all users page
        if (! $client)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the client to edit.' 
            ));
            $this->_redirect('/quotegen/client');
        }
        
        // Create a new form with the mode and roles set
        $form = new Quotegen_Form_Client();
        
        // Prepare the data for the form
        $request = $this->getRequest();
        $form->populate($client->toArray());
        
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
                        $mapper = new Quotegen_Model_Mapper_Client();
                        $client = new Quotegen_Model_Client();
                        $client->populate($values);
                        $client->setId($clientId);
                        
                        // Save to the database with cascade insert turned on
                        $clientId = $mapper->save($client, $clientId);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Client '" . $this->view->escape($values ["name"]) . "' was updated sucessfully." 
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
        // TODO: viewAction
    }
}

