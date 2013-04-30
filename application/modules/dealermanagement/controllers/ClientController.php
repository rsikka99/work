<?php

/**
 * Class Dealermanagement_ClientController
 */
class Dealermanagement_ClientController extends Tangent_Controller_Action
{
    protected $_mpsSession;

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
        $mapper    = Quotegen_Model_Mapper_Client::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper, Application_Model_Mapper_User::getInstance()->getWhereDealerId(Zend_Auth::getInstance()->getIdentity()->dealerId)));
        // Set the current page we're on
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set how many items to show
        $paginator->setItemCountPerPage(15);

        // Pass the view the paginator
        $this->view->paginator = $paginator;
    }

    /**
     * Deletes a client
     */
    public function deleteAction ()
    {
        $clientId = $this->_getParam('id', false);
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        if (!$clientId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'warning' => 'Please select a client to delete first.'
                                               ));
            $this->redirector('index');
        }
        $client = Quotegen_Model_Mapper_Client::getInstance()->find($clientId);
        if ($client && $client->dealerId != $dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot delete this client.'
                                               ));
            $this->redirector('index');
        }

        $clientMapper = Quotegen_Model_Mapper_Client::getInstance();
        $client       = $clientMapper->find($clientId);
        if (!$client)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'There was an error selecting the client to delete.'
                                               ));
            $this->redirector('index');
        }

        $message = "Are you sure you want to completely delete {$client->companyName} including all quotes, assessments and proposals? <br/>This is an irreversible operation";
        $form    = new Application_Form_Delete($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                if ($form->isValid($values))
                {
                    try
                    {
                        // Delete the client from the database
                        $clientService = new Admin_Service_Client();
                        $clientService->delete($client->id);
                        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
                        if ($client->id == $this->_mpsSession->selectedClientId)
                        {
                            unset($this->_mpsSession->selectedClientId);
                        }
                    }
                    catch (Exception $e)
                    {
                        $this->_flashMessenger->addMessage(array(
                                                                'danger' => "Client {$client->companyName} cannot be deleted since there are  quote(s) attached."
                                                           ));
                        $this->redirector('index');
                    }

                    $this->_flashMessenger->addMessage(array(
                                                            'success' => "Client  {$client->companyName} was deleted successfully."
                                                       ));
                    $this->redirector('index');
                }
            }
            else // User has selected cancel button, go back. 
            {
                $this->redirector('index');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Create a client
     */
    public function createAction ()
    {
        $clientService = new Admin_Service_Client();
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ['Cancel']))
            {
                $this->redirector('index');
            }

            try
            {
                // Create Client
                $values['dealerId'] = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $clientId           = $clientService->create($values);
            }
            catch (Exception $e)
            {
                $clientId = false;
            }

            if ($clientId)
            {
                $this->_flashMessenger->addMessage(array(
                                                        'success' => "Client successfully created."
                                                   ));
                // Redirect with client id so that the client is preselected
                $this->redirector('index', null, null, array(
                                                            'clientId' => $clientId
                                                       ));
            }
            else
            {
                $this->_flashMessenger->addMessage(array(
                                                        'danger' => "Please correct the errors below."
                                                   ));
            }
        }

        $this->view->form = $clientService->getForm();
    }

    public function editAction ()
    {
        // Get the passed client id
        $clientId = $this->_getParam('id', false);
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        // Get the client object from the database
        $client = Quotegen_Model_Mapper_Client::getInstance()->find($clientId);
        if ($client && $client->dealerId != $dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot edit this client.'
                                               ));
            $this->redirector('index');
        }
        // Start the client service
        $clientService = new Admin_Service_Client();
        if ($client)
        {
            $clientService->populateForm($clientId);
        }
        else
        {
            $this->redirector('index');
        }
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ['Cancel']))
            {
                $this->redirector('index');
            }

            try
            {
                // Update Client
                $clientId = $clientService->update($values, $clientId);
            }
            catch (Exception $e)
            {
                $clientId = false;
            }

            if ($clientId)
            {
                $this->_flashMessenger->addMessage(array(
                                                        'success' => "Client {$client->companyName} successfully updated."
                                                   ));
                // Redirect with client id so that the client is preselected
                $this->redirector('index', null, null, array(
                                                            'clientId' => $clientId
                                                       ));
            }
            else
            {
                $this->_flashMessenger->addMessage(array(
                                                        'danger' => "Please correct the errors below."
                                                   ));
            }
        }
        $this->view->form = $clientService->getForm();
    }

    /**
     * The view action
     */
    public function viewAction ()
    {
        $this->view->client = Quotegen_Model_Mapper_Client::getInstance()->find($this->_getParam('id', false));
        $dealerId           = Zend_Auth::getInstance()->getIdentity()->dealerId;
        if (!$this->view->client)
        {
            $this->redirector('index');
        }
        if ($this->view->client && $this->view->client->dealerId != $dealerId)
        {
            $this->_flashMessenger->addMessage(array(
                                                    'danger' => 'Insufficient Privilege: You cannot view this client.'
                                               ));
            $this->redirector('index');
        }
        $this->view->address = Quotegen_Model_Mapper_Address::getInstance()->find($this->_getParam('id', false));
        $contact             = Quotegen_Model_Mapper_Contact::getInstance()->getContactByClientId($this->_getParam('id', false));
        if (!$contact)
        {
            $contact = new Quotegen_Model_Contact();
        }
        $this->view->contact = $contact;
    }
}

