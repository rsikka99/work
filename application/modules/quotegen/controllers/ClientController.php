<?php

class Quotegen_ClientController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    /**
     * Displays all clients
     */
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
    
    // Deletes a single client
    public function deleteAction ()
    {
        $clientId = $this->_getParam('id', false);
        
        if (! $clientId)
        {
            $this->_helper->flashMessenger(array (
                    'warning' => 'Please select a client to delete first.' 
            ));
            $this->_helper->redirector('index');
        }
        
        $clientMapper = Quotegen_Model_Mapper_Client::getInstance();
        $client = $clientMapper->find($clientId);
        
        if (! $client)
        {
            $this->_helper->flashMessenger(array (
                    'danger' => 'There was an error selecting the client to delete.' 
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
                if ($form->isValid($values))
                {
                    try
                    {
                        // Delete the client from the database
                        $clientMapper->delete($client);
                    }
                    catch ( Exception $e )
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => "Client {$client->getName} cannot be deleted since there are  quote(s) attached." 
                        ));
                        $this->_helper->redirector('index');
                    }
                    
                    $this->_helper->flashMessenger(array (
                            'success' => "Client  {$client->getName()} was deleted successfully." 
                    ));
                    $this->_helper->redirector('index');
                }
            }
            else // User has selected cancel button, go back. 
            {
                $this->_helper->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    public function createAction ()
    {
        $clientService = new Admin_Service_Client();
        
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ['cancel']))
            {
                $this->_helper->redirector('index');
            }
            
            try
            {
                // Create Client
                $clientId = $clientService->create($values);
            }
            catch ( Exception $e )
            {
                $clientId = false;
            }
            
            if ($clientId)
            {
                // Redirect with client id so that the client is preselected
                $this->_helper->redirector('index', null, null, array (
                        'clientId' => $clientId 
                ));
            }
        }
        
        $this->view->form = $clientService->getForm();
    }

    public function editAction ()
    {
        // Get the passed client id
        $clientId = $this->_getParam('id', false);
        // Get the client object from the database
        $client = Quotegen_Model_Mapper_Client::getInstance()->find($clientId);
        // Start the client service
        $clientService = new Admin_Service_Client();
        
        // Populate the client form
        if ($client)
            $clientService->getForm()->populate($client->toArray());
        
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ['cancel']))
            {
                $this->_helper->redirector('index');
            }
            try
            {
                // Create Client
                $clientId = $clientService->update($values,$clientId);
            }
            catch ( Exception $e )
            {
                $clientId = false;
            }
            
            if ($clientId)
            {
                // Redirect with client id so that the client is preselected
                $this->_helper->redirector('index', null, null, array (
                        'clientId' => $clientId 
                ));
            }
        }
        $this->view->form = $clientService->getForm();
    }

    public function viewAction ()
    {
        $this->view->client = Quotegen_Model_Mapper_Client::getInstance()->find($this->_getParam('id', false));
    }
}

