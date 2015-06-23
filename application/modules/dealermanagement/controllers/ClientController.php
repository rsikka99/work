<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Mappers\UserMapper;
use MPSToolbox\Legacy\Modules\Admin\Services\ClientService;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ContactMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\AddressMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ContactModel;
use Tangent\Controller\Action;

/**
 * Class Dealermanagement_ClientController
 */
class Dealermanagement_ClientController extends Action
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
        $this->_pageTitle = ['Your Clients', 'Company'];
        // Display all of the clients
        $mapper    = ClientMapper::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper, UserMapper::getInstance()->getWhereDealerId(Zend_Auth::getInstance()->getIdentity()->dealerId)));
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
        $this->_pageTitle = ['Delete Client', 'Your Clients', 'Company'];
        $clientId         = $this->_getParam('id', false);
        $dealerId         = Zend_Auth::getInstance()->getIdentity()->dealerId;
        if (!$clientId)
        {
            $this->_flashMessenger->addMessage([
                'warning' => 'Please select a client to delete first.'
            ]);
            $this->redirectToRoute('company.clients');
        }
        $client = ClientMapper::getInstance()->find($clientId);
        if ($client && $client->dealerId != $dealerId)
        {
            $this->_flashMessenger->addMessage([
                'danger' => 'Insufficient Privilege: You cannot delete this client.'
            ]);
            $this->redirectToRoute('company.clients');
        }

        $clientMapper = ClientMapper::getInstance();
        $client       = $clientMapper->find($clientId);
        if (!$client)
        {
            $this->_flashMessenger->addMessage([
                'danger' => 'There was an error selecting the client to delete.'
            ]);
            $this->redirectToRoute('company.clients');
        }

        $message = "Are you sure you want to completely delete {$client->companyName} including all quotes, assessments and proposals? <br/>This is an irreversible operation";
        $form    = new DeleteConfirmationForm($message);

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
                        $clientService = new ClientService();
                        $clientService->delete($client->id);
                        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
                        if ($client->id == $this->_mpsSession->selectedClientId)
                        {
                            unset($this->_mpsSession->selectedClientId);
                        }
                    }
                    catch (Exception $e)
                    {
                        throw new Exception("Passing exception up the chain.", 0, $e);
                        $this->_flashMessenger->addMessage(['danger' => "Client {$client->companyName} cannot be deleted since there are  quote(s) attached."]);
                        $this->redirectToRoute('company.clients');
                    }

                    $this->_flashMessenger->addMessage(['success' => "Client  {$client->companyName} was deleted successfully."]);
                    $this->redirectToRoute('company.clients');
                }
            }
            else // User has selected cancel button, go back. 
            {
                $this->redirectToRoute('company.clients');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Create a client
     */
    public function createAction ()
    {
        $this->_pageTitle = ['Create Client', 'Your Clients', 'Company'];
        $clientService    = new ClientService();
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getParams();
            if (isset($values ['Cancel']))
            {
                $this->redirectToRoute('company.clients');
            }

            try
            {
                // Create Client
                $values['dealerId'] = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $clientId           = $clientService->create($values);
            }
            catch (Exception $e)
            {
                \Tangent\Logger\Logger::logException($e);
                $clientId = false;
            }

            if ($clientId)
            {
                $this->_flashMessenger->addMessage([
                    'success' => "Client successfully created."
                ]);
                // Redirect with client id so that the client is preselected
                $this->redirectToRoute('company.clients', [
                    'clientId' => $clientId
                ]);
            }
            else
            {
                $this->_flashMessenger->addMessage([
                    'danger' => "Please correct the errors below."
                ]);
            }
        }

        $this->view->form = $clientService->getForm();
    }

    public function editAction ()
    {
        $this->_pageTitle = ['Edit Client', 'Your Clients', 'Company'];
        // Get the passed client id
        $clientId = $this->_getParam('id', false);
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        // Get the client object from the database
        $client = ClientMapper::getInstance()->find($clientId);
        if ($client && $client->dealerId != $dealerId)
        {
            $this->_flashMessenger->addMessage([
                'danger' => 'Insufficient Privilege: You cannot edit this client.'
            ]);
            $this->redirectToRoute('company.clients');
        }
        // Start the client service
        $clientService = new ClientService();
        if ($client)
        {
            $clientService->populateForm($clientId);
        }
        else
        {
            $this->redirectToRoute('company.clients');
        }
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getParams();
            if (isset($values ['Cancel']))
            {
                $this->redirectToRoute('company.clients');
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
                $this->_flashMessenger->addMessage([
                    'success' => "Client {$client->companyName} successfully updated."
                ]);
                // Redirect with client id so that the client is preselected
                $this->redirectToRoute('company.clients', [
                    'clientId' => $clientId
                ]);
            }
            else
            {
                $this->_flashMessenger->addMessage([
                    'danger' => "Please correct the errors below."
                ]);
            }
        }
        $this->view->form = $clientService->getForm();
    }

    /**
     * The view action
     */
    public function viewAction ()
    {
        $this->_pageTitle = ['Viewing Client', 'Your Clients', 'Company'];

        $this->view->client = ClientMapper::getInstance()->find($this->_getParam('id', false));
        $dealerId           = Zend_Auth::getInstance()->getIdentity()->dealerId;
        if (!$this->view->client)
        {
            $this->redirectToRoute('company.clients');
        }
        if ($this->view->client && $this->view->client->dealerId != $dealerId)
        {
            $this->_flashMessenger->addMessage([
                'danger' => 'Insufficient Privilege: You cannot view this client.'
            ]);
            $this->redirectToRoute('company.clients');
        }
        $this->view->address = AddressMapper::getInstance()->find($this->_getParam('id', false));
        $contact             = ContactMapper::getInstance()->getContactByClientId($this->_getParam('id', false));
        if (!$contact)
        {
            $contact = new ContactModel();
        }
        $this->view->contact = $contact;
    }
}

