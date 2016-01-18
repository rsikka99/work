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
        $this->_pageTitle = ['Manage Clients', 'Company'];
        // Display all of the clients
        $mapper    = ClientMapper::getInstance();
        $clients = $mapper->fetchClientListForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);

        // Pass the view the paginator
        $this->view->clients = $clients;
    }

    /**
     * Deletes a client
     */
    public function deleteAction ()
    {
        $this->_pageTitle = ['Delete Client', 'Manage Clients', 'Company'];
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

    public function ordersAction() {
        $this->_pageTitle = ['Supply Orders', 'Manage Clients', 'Company'];
    }

    /**
     * Create a client
     */
    public function createAction ()
    {
        $this->_pageTitle = ['Create Client', 'Manage Clients', 'Company'];
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
                if (isset($_GET['select'])) {
                    $url = $this->getFrontController()->getRouter()->assemble([],'app.dashboard.select-client');
                    $this->redirect($url.'?selectClient='.$clientId);
                    return;
                }
                // Redirect with client id so that the client is preselected
                $this->redirectToRoute('company.clients', [
                    'clientId' => $clientId
                ]);
                return;
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
        $this->_pageTitle = ['Edit Client', 'Manage Clients', 'Company'];
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
        $this->_pageTitle = ['Viewing Client', 'Manage Clients', 'Company'];

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

    private function recursiveGroupTable($groups, $root=null, $root_found=false) {
        if (!$root) $root_found=true;
        if (!$root_found) {
            foreach ($groups as $group) {
                if ($group['id']==$root) {
                    $root_found=true;
                    $groups = [$group];
                } else {
                    $result = $this->recursiveGroupTable($group['children'], $root, false);
                    if ($result) return $result;
                }
            }
            if (!$root_found) return false;
        }
        $result = [];
        foreach ($groups as $group) {
            if (!$group['deviceCountTotal']) continue;
            $icon = 'glyphicon glyphicon glyphicon-folder-open';
            switch ($group['groupType']) {
                case 'Dealer' : $icon = 'glyphicon glyphicon glyphicon-list-alt'; break;
                case 'Customer' : $icon = 'glyphicon glyphicon glyphicon-user'; break;
                case 'Office/Location' : $icon = 'glyphicon glyphicon glyphicon-home'; break;
            }
            $state=[['expanded'=>true]];

            $addr = [];
            if (!empty($group['additionalDetails']['street 1'])) $addr []= $group['additionalDetails']['street 1'];
            if (!empty($group['additionalDetails']['city'])) $addr []= $group['additionalDetails']['city'];
            if (!empty($group['additionalDetails']['state/Prov'])) $addr []= $group['additionalDetails']['state/Prov'];
            if (!empty($group['additionalDetails']['ziP/Postal Code'])) $addr []= $group['additionalDetails']['ziP/Postal Code'];
            if (!empty($group['additionalDetails']['country'])) $addr []= $group['additionalDetails']['country'];

            $text = $group['name'];
            $text.= ' ('.$group['deviceCountTotal'].')';
            if (!empty($addr)) $text.="<span class='pull-right'>".implode(', ',$addr).'</span>';
            $line=['text'=>$text, 'icon'=>$icon,'state'=>$state,'selectable'=>false, 'href'=>$group['id']];
            if (!empty($group['children'])) {
                $line['nodes'] = $this->recursiveGroupTable($group['children'], null, true);
            }
            $result []= $line;
        }
        return $result;
    }

    private function getPrintFleet() {
        $service = new \MPSToolbox\Services\RmsUpdateService();
        $rmsUri = $service->getRmsUri();
        if (!$rmsUri) {
            echo 'Error: RMS Uri not specified by root administrator';
            return false;
        }

        $printFleet = new \MPSToolbox\Api\PrintFleet($rmsUri);
        if (!$printFleet->auth()) {
            echo 'Error: Cannot connect with Print Fleet';
            return false;
        }
        return $printFleet;
    }

    public function importAction() {
        $settings = \MPSToolbox\Settings\Entities\DealerSettingsEntity::getDealerSettings();
        $root = $settings->shopSettings->rmsGroup;

        $ajax = $this->getRequest()->getParam('ajax');
        if ($ajax) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $printFleet = $this->getPrintFleet();
            if ($printFleet) {
                $groups = $printFleet->groups();
                $tree = $this->recursiveGroupTable($groups, $root);
                if ($tree) {
                    echo '<div id="tree"></div><script> showTree(' . json_encode($tree) . '); </script>';
                } else {
                    echo '<div id="tree">No RMS groups found</div>';
                }
            }
            return;
        }
        if ($this->getRequest()->getMethod()=='POST') {
            $ids = $this->getParam('ids');
            if (!empty($ids)) {
                $printFleet = $this->getPrintFleet();
                if ($printFleet) {
                    $service = new ClientService();
                    $result = $service->importFromPrintFleet($printFleet, explode(' ',$ids));
                    $this->redirect('/dealermanagement/client/imported?'.http_build_query(['result'=>json_encode($result)]));
                }
                return;
            }
        }

        $this->_pageTitle = ['Import Clients from RMS', 'Manage Clients', 'Company'];
    }

    public function importedAction() {
        $this->_pageTitle = ['Import Clients from RMS', 'Manage Clients', 'Company'];
        $this->view->result = json_decode($this->getParam('result'), true);
    }

}

