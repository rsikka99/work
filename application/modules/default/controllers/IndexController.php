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
     * $r->addRoute('app.dashboard',                     new R('/',                                            ['module' => 'default', 'controller' => 'index', 'action' => 'index'             ]));
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
            $this->view->client = $client;

            if (!$this->getSelectedUpload() instanceof RmsUploadEntity)
            {
                if (RmsUploadRepository::countForClient($client->id) == 1)
                {
                    $rmsUpload                                  = RmsUploadRepository::fetchForClient($client->id)->first();
                    $this->getMpsSession()->selectedRmsUploadId = $rmsUpload->id;
                }
            }

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
        $this->_pageTitle = ['Your first client'];

        $clientService = new ClientService();

        if ($this->getRequest()->isPost())
        {
            $postData             = $this->getRequest()->getParams();
            $postData['dealerId'] = $this->getIdentity()->dealerId;
            $newClientId          = $clientService->create($postData);
            if ($newClientId !== false)
            {
                $this->getMpsSession()->selectedClientId = $newClientId;
                $this->_flashMessenger->addMessage(['success' => 'Congratulations! You\'ve successfully created your first client.']);
                $this->redirectToRoute('app.dashboard.no-uploads');
            }
            else
            {
                $this->_flashMessenger->addMessage(['danger' => 'We had trouble validating your data. Please review the errors on the form and try again.']);
            }
        }

        $this->view->form = $clientService->getForm();
    }

    /**
     * Helps a user create their first upload
     */
    public function noUploadsAction ()
    {
        $this->_pageTitle = ['Upload fleet data'];

        $uploadService = new RmsUploadService($this->getIdentity()->id, $this->getIdentity()->dealerId, $this->getSelectedClient()->id);

        $form = $uploadService->getForm();
        $form->removeElement('goBack');

        $form->setAction($this->view->url([], 'rms-upload'));

        $this->view->form = $form;
    }

    /**
     * Lets a user select an upload
     * $r->addRoute('app.dashboard.select-upload',       new R('/select-upload',                               ['module' => 'default', 'controller' => 'index', 'action' => 'select-upload'     ]));
     */
    public function selectUploadAction ()
    {
        $this->_pageTitle = ['Select Upload'];
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
        else
        {
            $this->redirectToRoute('app.dashboard.no-uploads');
        }
    }

    /**
     * Unsets the selected client
     * $r->addRoute('app.dashboard.change-client',       new R('/clients/change',                              ['module' => 'default', 'controller' => 'index', 'action' => 'change-client'     ]));
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
     * $r->addRoute('app.dashboard.change-upload',       new R('/rms-uploads/change',                          ['module' => 'default', 'controller' => 'index', 'action' => 'change-upload'     ]));
     */
    public function changeUploadAction ()
    {
        $mpsSession = $this->getMpsSession();
        unset($mpsSession->selectedRmsUploadId);

        $this->redirectToRoute('app.dashboard.select-upload');
    }

    /**
     * Handles the deletion of reports
     *
$r->addRoute('app.dashboard.delete-assessment',   new R('/delete-assessment/:assessmentId',             ['module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     ]));
$r->addRoute('app.dashboard.delete-optimization', new R('/delete-optimization/:hardwareOptimizationId', ['module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     ]));
$r->addRoute('app.dashboard.delete-healthcheck',  new R('/delete-healthcheck/:healthcheckId',           ['module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     ]));
$r->addRoute('app.dashboard.delete-quote',        new R('/delete-quote/:quoteId',                       ['module' => 'default', 'controller' => 'index', 'action' => 'delete-report'     ]));

     *
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
     *$r->addRoute('app.dashboard.delete-rms-upload',   new R('/rms-uploads/delete/:rmsUploadId',             ['module' => 'default', 'controller' => 'index', 'action' => 'delete-rms-upload' ]));
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

        $this->redirectToRoute('app.dashboard.select-upload');
    }

    /**
     * Main landing page
     */
    protected function showDashboard ()
    {
        $this->_pageTitle   = ['Dashboard'];
        $this->view->userId = $this->getIdentity()->id;

        $availableReports               = [];
        $availableQuotes                = [];
        $availableHealthchecks          = [];
        $availableHardwareOptimizations = [];
        $rmsUploads                     = [];

        $hasUpload = $this->getSelectedUpload() instanceof RmsUploadEntity;

        if ($this->getSelectedClient()->id > 0)
        {
            // Quotes don't require an rms upload
            $availableQuotes             = QuoteMapper::getInstance()->fetchAllForClient($this->getSelectedClient()->id);
            $this->view->availableQuotes = $availableQuotes;

            if ($hasUpload)
            {
                $availableReports               = AssessmentMapper::getInstance()->fetchAllAssessmentsForClient($this->getSelectedClient()->id, $this->getSelectedUpload()->id);
                $availableHealthchecks          = HealthCheckMapper::getInstance()->fetchAllHealthchecksForClient($this->getSelectedClient()->id, $this->getSelectedUpload()->id);
                $availableHardwareOptimizations = HardwareOptimizationMapper::getInstance()->fetchAllForClient($this->getSelectedClient()->id, $this->getSelectedUpload()->id);

                $this->view->availableAssessments           = $availableReports;
                $this->view->availableHealthchecks          = $availableHealthchecks;
                $this->view->availableHardwareOptimizations = $availableHardwareOptimizations;
            }
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
                    $userViewedClient = UserViewedClientMapper::getInstance()->find([$this->getIdentity()->id, $client->id]);
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
                    $this->_flashMessenger->addMessage(['danger' => 'Invalid Client']);
                }


            }
            else if (isset($postData['selectAssessment']))
            {
                $assessmentId = $postData['selectAssessment'];

                $validAssessmentIds = [0];
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

                $validQuoteIds = [];
                foreach ($availableQuotes as $quote)
                {
                    $validQuoteIds[] = $quote->id;
                }

                $inArray = new Zend_Validate_InArray($validQuoteIds);

                if ($inArray->isValid($selectedQuoteId))
                {
                    $this->redirectToRoute('quotes', ['quoteId' => $selectedQuoteId]);
                }
                else
                {
                    // Creating a new one
                }
            }
            else if (isset($postData['createLeasedQuote']))
            {
                $selectedQuoteId = $this->_createNewQuote(QuoteModel::QUOTE_TYPE_LEASED);
                $this->redirectToRoute('quotes', ['quoteId' => $selectedQuoteId]);
            }
            else if (isset($postData['createPurchasedQuote']))
            {
                $selectedQuoteId = $this->_createNewQuote(QuoteModel::QUOTE_TYPE_PURCHASED);
                $this->redirectToRoute('quotes', ['quoteId' => $selectedQuoteId]);
            }
            else if (isset($postData['selectHealthcheck']))
            {
                $healthcheckId = $postData['selectHealthcheck'];

                $validReportIds = [0];
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
                    $this->_flashMessenger->addMessage(["warning" => "Please select a health check"]);
                }
            }
            else if (isset($postData['selectHardwareOptimization']))
            {
                $hardwareOptimizationId = $postData['selectHardwareOptimization'];

                if ($hardwareOptimizationId > 0)
                {
                    $validReportIds = [0];
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
                        $this->_flashMessenger->addMessage(["warning" => "Please select a hardware optimization"]);
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
                    $this->redirectToRoute('rms-upload');
                }
                else
                {
                    $this->redirectToRoute('rms-upload.mapping', ['rmsUploadId' => $rmsUploadId]);
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
            $this->_flashMessenger->addMessage([
                'error' => "You do not have sufficient privileges to access this page. If you feel this is in error please contact your administrator."
            ]);
            $this->redirectToRoute('app.dashboard');
        }
        $quote = new QuoteModel();

        return $quote->createNewQuote($quoteType, $this->getSelectedClient()->id, $this->getIdentity()->id)->id;
    }

    /**
     * Allows a user to create a new client
     * @deprecated
     */
    public function createClientAction ()
    {
        throw new Exception('deprecated');
    }

    /**
     * Action to handle editing a client
     * @deprecated
     */
    public function editClientAction ()
    {
        throw new Exception('deprecated');
    }


    /**
     * JSON ACTION: Handles searching for a client by name and dealerId
     * @deprecated
     */
    public function searchForClientAction ()
    {
        throw new Exception('deprecated');
    }

    /**
     * Allows a user to view all of the clients available
     * @deprecated
     */
    public function viewAllClientsAction ()
    {
        throw new Exception('deprecated');
    }

    /**
     * Allows a user to view all of the clients available
     * $r->addRoute('app.dashboard.select-client',       new R('/select-client',                               ['module' => 'default', 'controller' => 'index', 'action' => 'select-client'     ]));
     */
    public function selectClientAction ()
    {
        $this->_pageTitle   = ['Select Client'];
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
            $userViewedClient = UserViewedClientMapper::getInstance()->find([$this->getIdentity()->id, $client->id]);
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

            if (!$this->getSelectedUpload() instanceof RmsUploadEntity)
            {
                $uploadCount = RmsUploadRepository::countForClient($clientId);

                if ($uploadCount > 1)
                {
                    $this->redirectToRoute('app.dashboard.select-upload');
                }
                else if ($uploadCount == 1)
                {
                    $rmsUpload                                  = RmsUploadRepository::fetchForClient($clientId)->first();
                    $this->getMpsSession()->selectedRmsUploadId = $rmsUpload->id;
                }
                else
                {
                    $this->redirectToRoute('app.dashboard');
                }
            }

            // Reload the page
            $this->redirectToRoute('app.dashboard');
        }
        else
        {
            $this->_flashMessenger->addMessage(['danger' => 'Invalid Client']);
        }
    }
}