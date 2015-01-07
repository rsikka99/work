<?php
use MPSToolbox\Legacy\Entities\ClientEntity;
use MPSToolbox\Legacy\Entities\RmsUploadEntity;
use MPSToolbox\Legacy\Models\Acl\AppAclModel;
use MPSToolbox\Legacy\Models\Acl\QuoteGeneratorAclModel;
use MPSToolbox\Legacy\Modules\Admin\Forms\ClientForm;
use MPSToolbox\Legacy\Modules\Admin\Services\ClientService;
use MPSToolbox\Legacy\Modules\Assessment\Mappers\AssessmentMapper;
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel;
use MPSToolbox\Legacy\Modules\HealthCheck\Mappers\HealthCheckMapper;
use MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUploadService;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\UserViewedClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserViewedClientModel;
use MPSToolbox\Legacy\Repositories\ClientRepository;
use MPSToolbox\Legacy\Repositories\RmsUploadRepository;
use Tangent\Controller\Action;

/**
 * Class Default_IndexController
 */
class Default_IndexController extends Action
{
    public function init ()
    {
        if ($this->getSelectedClient() instanceof ClientEntity)
        {
            $this->view->selectedClientId = $this->getSelectedClient()->id;
        }
    }

    /**
     * Handles the main workflow once a user logs in or deselects/reselects a client/upload
     */
    public function indexAction ()
    {
        if (ClientRepository::countForDealer($this->getIdentity()->dealerId) < 1)
        {
            // Users first time, redirect to noclients
            $this->redirectToRoute('app.dashboard.no-clients');
        }

        $client = $this->getSelectedClient();

        if ($client instanceof ClientEntity)
        {
            if (RmsUploadRepository::countForClient($client->id) < 1)
            {
                $this->redirectToRoute('app.dashboard.no-uploads');
            }

            $rmsUpload = $this->getSelectedUpload();
            if (!$rmsUpload instanceof RmsUploadEntity)
            {
                $this->redirectToRoute('app.dashboard.select-upload');
            }

            $this->view->client    = $client;
            $this->view->rmsUpload = $rmsUpload;

            // If everything is good to go we can bring them to the dashboard
            $this->showDashboard();
        }
        else
        {
            $this->redirectToRoute('app.dashboard.select-client');
        }
    }

    /**
     * Helps a user create their first client
     */
    public function noClientsAction ()
    {
        $this->_pageTitle = array('Your first client');

        $clientService = new ClientService();

        if ($this->getRequest()->isPost())
        {
            $postData             = $this->getRequest()->getPost();
            $postData['dealerId'] = $this->getIdentity()->dealerId;
            $newClientId          = $clientService->create($postData);
            if ($newClientId !== false)
            {
                $this->getMpsSession()->selectedClientId = $newClientId;
                $this->_flashMessenger->addMessage(array('success' => 'Congratulations! You\'ve successfully created your first client.'));
                $this->redirectToRoute('app.dashboard.no-uploads');
            }
            else
            {
                $this->_flashMessenger->addMessage(array('danger' => 'We had trouble validating your data. Please review the errors on the form and try again.'));
            }
        }

        $this->view->form = $clientService->getForm();
    }

    /**
     * Helps a user create their first upload
     */
    public function noUploadsAction ()
    {
        $this->_pageTitle = array('Upload fleet data');

        $uploadService = new RmsUploadService($this->getIdentity()->id, $this->getIdentity()->dealerId, $this->getSelectedClient()->id);

        $form = $uploadService->getForm();

        $form->setAction($this->view->url([], 'rms-upload.upload-file'));

        $this->view->form = $form;
    }

    /**
     * Lets a user select an upload
     */
    public function selectUploadAction ()
    {
        $this->_pageTitle = array('Select Upload');
        $client           = $this->getSelectedClient();

        // We must have a client
        if (!$client instanceof ClientEntity)
        {
            $this->redirectToRoute('app.dashboard.select-client');
        }

        // Handle selecting
        if ($this->getRequest()->isPost())
        {
            $rmsUploadId = $this->getParam('rmsUploadId', false);
            $rmsUpload   = RmsUploadRepository::find($rmsUploadId);
            if ($rmsUpload instanceof RmsUploadEntity && $rmsUpload->clientId == $client->id)
            {
                $this->getMpsSession()->selectedRmsUploadId = $rmsUploadId;
                $this->redirectToRoute('app.dashboard');
            }
            else
            {
                $this->_flashMessenger->addMessage(['error' => 'Invalid upload selected']);
            }
        }

        $rmsUploads = $client->rmsUploads()->with('RmsProvider')->get();
        if (count($rmsUploads) > 0)
        {
            $this->view->availableRmsUploads = $rmsUploads;
        }
    }

    /**
     * Unsets the selected client
     */
    public function changeClientAction ()
    {
        $mpsSession = $this->getMpsSession();
        unset($mpsSession->selectedClientId);
        unset($mpsSession->selectedRmsUploadId);

        $this->redirectToRoute('app.dashboard');
    }

    /**
     * Unsets the selected RMS upload
     */
    public function changeUploadAction ()
    {
        $mpsSession = $this->getMpsSession();
        unset($mpsSession->selectedRmsUploadId);

        $this->redirectToRoute('app.dashboard');
    }

    /**
     * Handles the deletion of reports
     *
     * FIXME lrobert: Move this to the api instead.
     */
    public function deleteReportAction ()
    {
        $assessmentId   = $this->getParam('assessmentId', false);
        $optimizationId = $this->getParam('hardwareOptimizationId', false);
        $healthcheckId  = $this->getParam('healthcheckId', false);
        $quoteId        = $this->getParam('quoteId', false);

        if ($assessmentId)
        {
            $assessmentMapper = AssessmentMapper::getInstance();
            $assessment       = $assessmentMapper->find($assessmentId);
            if ($assessment instanceof AssessmentModel && $assessment->clientId == $this->getSelectedClient()->id)
            {
                $assessmentMapper->delete($assessment);
                $this->_flashMessenger->addMessage(['success' => 'Report Deleted']);
                $this->redirectToRoute('app.dashboard');
            }

            $this->_flashMessenger->addMessage(['danger' => 'Invalid Report ID']);
        }
        else if ($optimizationId)
        {
            $hardwareOptimizationMapper = HardwareoptimizationMapper::getInstance();
            $hardwareOptimization       = $hardwareOptimizationMapper->find($optimizationId);
            if ($hardwareOptimization instanceof HardwareOptimizationModel && $hardwareOptimization->clientId == $this->getSelectedClient()->id)
            {
                $hardwareOptimizationMapper->delete($hardwareOptimization);
                $this->_flashMessenger->addMessage(['success' => 'Report Deleted']);
                $this->redirectToRoute('app.dashboard');
            }

            $this->_flashMessenger->addMessage(['danger' => 'Invalid Report ID']);
        }
        else if ($healthcheckId)
        {
            $healthcheckMapper = HealthcheckMapper::getInstance();
            $healthcheck       = $healthcheckMapper->find($healthcheckId);
            if ($healthcheck instanceof HealthCheckModel && $healthcheck->clientId == $this->getSelectedClient()->id)
            {
                $healthcheckMapper->delete($healthcheck);
                $this->_flashMessenger->addMessage(['success' => 'Report Deleted']);
                $this->redirectToRoute('app.dashboard');
            }

            $this->_flashMessenger->addMessage(['danger' => 'Invalid Report ID']);
        }
        else if ($quoteId)
        {
            $quoteMapper = QuoteMapper::getInstance();
            $quote       = $quoteMapper->find($quoteId);
            if ($quote instanceof QuoteModel && $quote->clientId == $this->getSelectedClient()->id)
            {
                $quoteMapper->delete($quote);
                $this->_flashMessenger->addMessage(['success' => 'Report Deleted']);
                $this->redirectToRoute('app.dashboard');
            }

            $this->_flashMessenger->addMessage(['danger' => 'Invalid Report ID']);
        }
        $this->redirectToRoute('app.dashboard');
    }

    /**
     * Handles the deletion of rms uploads
     *
     * FIXME lrobert: Move this to the api instead.
     */
    public function deleteRmsUploadAction ()
    {
        $rmsUploadId = $this->getParam('rmsUploadId', false);

        if ($rmsUploadId)
        {
            $rmsUpload = RmsUploadRepository::find($rmsUploadId);

            if ($rmsUpload instanceof RmsUploadEntity && $rmsUpload->clientId == $this->getSelectedClient()->id)
            {
                $selectedRmsUpload = $this->getSelectedUpload();
                if ($selectedRmsUpload instanceof RmsUploadEntity && $selectedRmsUpload->id == $rmsUpload->id)
                {
                    unset($this->getMpsSession()->selectedRmsUploadId);
                }

                /* @var $capsule \Illuminate\Database\Capsule\Manager */
                $capsule      = Zend_Registry::get('Illuminate\Database\Capsule\Manager');
                $dbConnection = $capsule->getConnection();
                try
                {

                    $dbConnection->beginTransaction();

                    $rmsUpload->delete();

                    $dbConnection->commit();
                    $this->_flashMessenger->addMessage(['success' => 'Upload Deleted']);
                }
                catch (Exception $e)
                {
                    $dbConnection->rollBack();
                    \Tangent\Logger\Logger::logException($e);
                    $this->_flashMessenger->addMessage(['danger' => 'Error deleting upload data. We are investigating the issue. If the issue persists please contact support.']);
                }
            }
            else
            {
                $this->_flashMessenger->addMessage(['danger' => 'Invalid Upload ID']);
            }
        }

        $this->redirectToRoute('app.dashboard');
    }

    /**
     * Main landing page
     */
    public function showDashboard ()
    {
        $this->_pageTitle   = array('Dashboard');
        $this->view->userId = $this->getIdentity()->id;

        $availableReports               = array();
        $availableQuotes                = array();
        $availableHealthchecks          = array();
        $availableHardwareOptimizations = array();
        $rmsUploads                     = array();

        if ($this->getSelectedClient()->id > 0)
        {
            $availableReports               = AssessmentMapper::getInstance()->fetchAllAssessmentsForClient($this->getSelectedClient()->id, $this->getSelectedUpload()->id);
            $availableQuotes                = QuoteMapper::getInstance()->fetchAllForClient($this->getSelectedClient()->id);
            $availableHealthchecks          = HealthCheckMapper::getInstance()->fetchAllHealthchecksForClient($this->getSelectedClient()->id, $this->getSelectedUpload()->id);
            $availableHardwareOptimizations = HardwareOptimizationMapper::getInstance()->fetchAllForClient($this->getSelectedClient()->id, $this->getSelectedUpload()->id);


            $this->view->availableAssessments           = $availableReports;
            $this->view->availableQuotes                = $availableQuotes;
            $this->view->availableHealthchecks          = $availableHealthchecks;
            $this->view->availableHardwareOptimizations = $availableHardwareOptimizations;
        }

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (isset($postData['createClient']))
            {
                $this->redirectToRoute('company.clients.create');
            }
            else if (isset($postData['editClient']))
            {
                $this->redirectToRoute('company.clients.edit');
            }
            else if (isset($postData['selectClient']))
            {
                $newClientId = $postData['selectClient'];
                $client      = ClientMapper::getInstance()->find($newClientId);
                if ($client)
                {
                    $userViewedClient = UserViewedClientMapper::getInstance()->find(array($this->getIdentity()->id, $client->id));
                    if ($userViewedClient instanceof UserViewedClientModel)
                    {
                        $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                        UserViewedClientMapper::getInstance()->save($userViewedClient);
                    }
                    else
                    {
                        $userViewedClient             = new UserViewedClientModel();
                        $userViewedClient->clientId   = $client->id;
                        $userViewedClient->userId     = $this->getIdentity()->id;
                        $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                        UserViewedClientMapper::getInstance()->insert($userViewedClient);
                    }


                    $this->getMpsSession()->selectedClientId = $newClientId;

                    // Reload the page
                    $this->redirectToRoute('app.dashboard');
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'Invalid Client'));
                }


            }
            else if (isset($postData['selectAssessment']))
            {
                $assessmentId = $postData['selectAssessment'];

                $validAssessmentIds = array(0);
                foreach ($availableReports as $report)
                {
                    $validAssessmentIds[] = $report->id;
                }

                $inArray = new Zend_Validate_InArray($validAssessmentIds);

                if ($inArray->isValid($assessmentId))
                {
                    $this->getMpsSession()->assessmentId = $assessmentId;
                    $this->redirectToRoute('assessment');
                }
            }
            else if (isset($postData['selectQuote']))
            {
                $selectedQuoteId = $postData['selectQuote'];

                $validQuoteIds = array();
                foreach ($availableQuotes as $quote)
                {
                    $validQuoteIds[] = $quote->id;
                }

                $inArray = new Zend_Validate_InArray($validQuoteIds);

                if ($inArray->isValid($selectedQuoteId))
                {
                    $this->redirectToRoute('quotes', array('quoteId' => $selectedQuoteId));
                }
                else
                {
                    // Creating a new one
                }
            }
            else if (isset($postData['createLeasedQuote']))
            {
                $selectedQuoteId = $this->_createNewQuote(QuoteModel::QUOTE_TYPE_LEASED);
                $this->redirectToRoute('quotes', array('quoteId' => $selectedQuoteId));
            }
            else if (isset($postData['createPurchasedQuote']))
            {
                $selectedQuoteId = $this->_createNewQuote(QuoteModel::QUOTE_TYPE_PURCHASED);
                $this->redirectToRoute('quotes', array('quoteId' => $selectedQuoteId));
            }
            else if (isset($postData['selectHealthcheck']))
            {
                $healthcheckId = $postData['selectHealthcheck'];

                $validReportIds = array(0);
                foreach ($availableHealthchecks as $healthcheck)
                {
                    $validReportIds[] = $healthcheck->id;
                }

                $inArray = new Zend_Validate_InArray($validReportIds);

                if ($inArray->isValid($healthcheckId))
                {

                    $this->getMpsSession()->healthcheckId = $healthcheckId;
                    $this->redirectToRoute('healthcheck');
                }
                else
                {
                    $this->_flashMessenger->addMessage(array("warning" => "Please select a health check"));
                }
            }
            else if (isset($postData['selectHardwareOptimization']))
            {
                $hardwareOptimizationId = $postData['selectHardwareOptimization'];

                if ($hardwareOptimizationId > 0)
                {
                    $validReportIds = array(0);
                    foreach ($availableHardwareOptimizations as $report)
                    {
                        $validReportIds[] = $report->id;
                    }

                    $inArray = new Zend_Validate_InArray($validReportIds);

                    if ($inArray->isValid($hardwareOptimizationId))
                    {
                        $this->getMpsSession()->hardwareOptimizationId = $hardwareOptimizationId;
                        $this->redirectToRoute('hardwareoptimization');
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("warning" => "Please select a hardware optimization"));
                    }
                }
                else
                {
                    $this->getMpsSession()->hardwareOptimizationId = $hardwareOptimizationId;
                    $this->redirectToRoute('hardwareoptimization');
                }

            }
            else if (isset($postData['selectRmsUpload']))
            {
                $rmsUploadId = $postData['selectRmsUpload'];

                if ($rmsUploadId > 0)
                {
                    /**
                     * Make sure it's a valid upload for our current client
                     */
                    $isValid = false;
                    foreach ($rmsUploads as $rmsUpload)
                    {
                        if ((int)$rmsUploadId === (int)$rmsUpload->id)
                        {
                            $isValid = true;
                            break;
                        }
                    }

                    if (!$isValid)
                    {
                        $rmsUploadId = 0;
                    }
                }
                else
                {
                    $rmsUploadId = 0;
                }


                if ($rmsUploadId === 0)
                {
                    $this->redirectToRoute('rms-upload.upload-file');
                }
                else
                {
                    $this->redirectToRoute('rms-upload.mapping', array('rmsUploadId' => $rmsUploadId));
                }
            }
            else if (isset($postData['uploadCustomerTonerCost']))
            {
                $this->redirectToRoute('company.customer-pricing'); //There is no customerPricing controller in proposalgen yet
            }
        }

        $this->view->headScript()->appendFile($this->view->baseUrl('/js/app/legacy/default/ClientSearch.js'));
    }

    /**
     * Creates a brand new quote
     *
     * @param int $quoteType The type of quote we're making
     *
     * @return int
     */
    protected function _createNewQuote ($quoteType)
    {
        /**
         * If we are not allowed here
         */
        if (!$this->view->isAllowed(QuoteGeneratorAclModel::RESOURCE_QUOTEGEN_QUOTE_INDEX, AppAclModel::PRIVILEGE_VIEW))
        {
            $this->_flashMessenger->addMessage(array(
                'error' => "You do not have sufficient privileges to access this page. If you feel this is in error please contact your administrator."
            ));
            $this->redirectToRoute('app.dashboard');
        }
        $quote = new QuoteModel();

        return $quote->createNewQuote($quoteType, $this->getSelectedClient()->id, $this->getIdentity()->id)->id;
    }

    /**
     * Allows a user to create a new client
     */
    public function createClientAction ()
    {
        $this->_pageTitle = array('Create Client');
        $clientService    = new ClientService();

        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ['Cancel']))
            {
                $this->redirectToRoute('app.dashboard');
            }

            // Create Client
            $values['dealerId'] = $this->getIdentity()->dealerId;
            $clientId           = $clientService->create($values);

            if ($clientId)
            {
                $userViewedClient = UserViewedClientMapper::getInstance()->find([$this->getIdentity()->id, $clientId]);
                if ($userViewedClient instanceof UserViewedClientModel)
                {
                    $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                    UserViewedClientMapper::getInstance()->save($userViewedClient);
                }
                else
                {
                    $userViewedClient             = new UserViewedClientModel();
                    $userViewedClient->clientId   = $clientId;
                    $userViewedClient->userId     = $this->getIdentity()->id;
                    $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                    UserViewedClientMapper::getInstance()->insert($userViewedClient);
                }

                $this->_flashMessenger->addMessage(['success' => "Client was successfully created."]);

                $this->getMpsSession()->selectedClientId = $clientId;

                // Redirect with client id so that the client is preselected
                $this->redirectToRoute('app.dashboard');
            }
        }

        $this->view->form = $clientService->getForm();
    }

    /**
     * Action to handle editing a client
     */
    public function editClientAction ()
    {
        $this->_pageTitle = array('Edit Client');
        // Get the passed client id
        $clientId = $this->getSelectedClient()->id;
        // Get the client object from the database
        $client = ClientMapper::getInstance()->find($clientId);

        if (!$client)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a client first.'));
            $this->redirectToRoute('company.clients');
        }

        // Start the client service
        $clientService = new ClientService();

        $clientService->populateForm($clientId);
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
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
                $this->_flashMessenger->addMessage(array(
                    'success' => "Client {$client->companyName} successfully updated."
                ));
                // Redirect with client id so that the client is preselected
                $this->redirectToRoute('company.clients', array(
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
     * JSON ACTION: Handles searching for a client by name and dealerId
     */
    public function searchForClientAction ()
    {

        $this->_pageTitle = array('Client Search');
        $searchTerm       = $this->getParam('query', false);
        $results          = array();
        if ($searchTerm !== false)
        {
            $clients = ClientMapper::getInstance()->searchForClientByCompanyNameAndDealer($searchTerm, $this->getIdentity()->dealerId);
            foreach ($clients as $client)
            {
                $results[] = array(
                    "id"          => $client->id,
                    "companyName" => $client->companyName
                );
            }
        }

        $this->sendJson($results);
    }

    /**
     * Allows a user to view all of the clients available
     */
    public function viewAllClientsAction ()
    {
        $this->_pageTitle    = array('Select Client');
        $this->view->clients = ClientMapper::getInstance()->fetchClientListForDealer($this->getIdentity()->dealerId);
    }

    /**
     * Allows a user to view all of the clients available
     */
    public function selectClientAction ()
    {
        $this->_pageTitle   = array('Select Client');
        $this->view->userId = $this->getIdentity()->id;

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['selectClient']))
            {
                $this->selectClient($postData['selectClient']);
            }
        }

        $this->view->headScript()->appendFile($this->view->baseUrl('/js/app/legacy/default/ClientSearch.js'));
    }

    public function selectClientListAction ()
    {
        /**
         * TODO lrobert: Have the /select-client route display data from this action so that we can just have a filterable
         * list of clients to choose from rather than going to a whole new page
         */
        $clientQuery = ClientRepository::getQueryByLastSeen($this->getIdentity()->dealerId, $this->getIdentity()->id);
    }


    public function selectClient ($clientId)
    {
        $client = ClientMapper::getInstance()->find($clientId);
        if ($client)
        {
            $userViewedClient = UserViewedClientMapper::getInstance()->find(array($this->getIdentity()->id, $client->id));
            if ($userViewedClient instanceof UserViewedClientModel)
            {
                $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                UserViewedClientMapper::getInstance()->save($userViewedClient);
            }
            else
            {
                $userViewedClient             = new UserViewedClientModel();
                $userViewedClient->clientId   = $client->id;
                $userViewedClient->userId     = $this->getIdentity()->id;
                $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                UserViewedClientMapper::getInstance()->insert($userViewedClient);
            }


            $this->getMpsSession()->selectedClientId = $clientId;

            // Reload the page
            $this->redirectToRoute('app.dashboard');
        }
        else
        {
            $this->_flashMessenger->addMessage(array('danger' => 'Invalid Client'));
        }
    }
}