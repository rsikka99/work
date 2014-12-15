<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Modules\Admin\Services\ClientService;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use Tangent\Controller\Action;

/**
 * Class Admin_ClientController
 */
class Admin_ClientController extends Action
{
    /**
     * @var Zend_Session_Namespace
     */
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
        $this->_pageTitle = array('System', 'Clients', 'Client Management');
        // Display all of the clients
        $mapper    = ClientMapper::getInstance();
        $paginator = new Zend_Paginator(new My_Paginator_MapperAdapter($mapper));

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
        $this->_pageTitle = array('System', 'Clients', 'Delete Client');
        $clientId         = $this->_getParam('id', false);

        if (!$clientId)
        {
            $this->_flashMessenger->addMessage(array(
                'warning' => 'Please select a client to delete first.'
            ));
            $this->redirectToRoute('admin.clients');
        }

        $clientMapper = ClientMapper::getInstance();
        $client       = $clientMapper->find($clientId);
        if (!$client)
        {
            $this->_flashMessenger->addMessage(array(
                'danger' => 'There was an error selecting the client to delete.'
            ));
            $this->redirectToRoute('admin.clients');
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
                        $this->_flashMessenger->addMessage(array(
                            'danger' => "Client {$client->companyName} cannot be deleted since there are  quote(s) attached."
                        ));
                        $this->redirectToRoute('admin.clients');
                    }

                    $this->_flashMessenger->addMessage(array(
                        'success' => "Client  {$client->companyName} was deleted successfully."
                    ));
                    $this->redirectToRoute('admin.clients');
                }
            }
            else // User has selected cancel button, go back. 
            {
                $this->redirectToRoute('admin.clients');
            }
        }
        $this->view->form = $form;
    }

    /**
     * Create a client
     */
    public function createAction ()
    {
        $this->_pageTitle = array('System', 'Clients', 'Create Client');
        $clientService    = new ClientService();
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ['Cancel']))
            {
                $this->redirectToRoute('admin.clients');
            }

            try
            {
                // Create Client
                $clientId = $clientService->create($values);
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
                $this->redirectToRoute('admin.clients', array(
                    'clientId' => $clientId));
            }
            else
            {
                $this->_flashMessenger->addMessage(array(
                    'danger' => "Please correct the errors below."
                ));
            }
        }

        $this->view->form = $clientService->getForm(false);
    }

    public function editAction ()
    {
        $this->_pageTitle = array('System', 'Clients', 'Edit Client');
        // Get the passed client id
        $clientId = $this->_getParam('id', false);
        // Get the client object from the database
        $client = ClientMapper::getInstance()->find($clientId);
        // Start the client service
        $clientService = new ClientService();
        if ($client)
        {
            //This sets the form to not be a dealer management form
            $clientService->getForm(false);
            $clientService->populateForm($clientId);
        }
        else
        {
            $this->redirectToRoute('admin.clients');
        }
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ['Cancel']))
            {
                $this->redirectToRoute('admin.clients');
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
                $this->redirectToRoute('admin.clients', array(
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
        $this->view->form = $clientService->getForm(false);
    }
}

