<?php
class Default_IndexController extends Tangent_Controller_Action
{
    /**
     * @var int
     */
    protected $_selectedClientId;

    /**
     * The namespace for our mps application
     *
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    /**
     * @var int
     */
    protected $_userId;

    public function init ()
    {
        /* Initialize action controller here */
        $this->_mpsSession      = new Zend_Session_Namespace('mps-tools');
        $this->_userId          = Zend_Auth::getInstance()->getIdentity()->id;


        if (isset($this->_mpsSession->selectedClientId))
        {
            $client = Quotegen_Model_Mapper_Client::getInstance()->find($this->_mpsSession->selectedClientId);
            // Make sure the selected client is ours!
            if ($client && $client->dealerId == Zend_Auth::getInstance()->getIdentity()->dealerId)
            {
                $this->_selectedClientId      = $this->_mpsSession->selectedClientId;
                $this->view->selectedClientId = $this->_selectedClientId;
            }
        }
    }

    /**
     * Main landing page
     */
    public function indexAction ()
    {
        $this->view->userId = $this->_userId;

        $availableReports               = array();
        $availableQuotes                = array();
        $availableHealthchecks          = array();
        $availableHardwareOptimizations = array();
        $rmsUploads                     = array();

        if ($this->_selectedClientId > 0)
        {
            $availableReports                 = Assessment_Model_Mapper_Assessment::getInstance()->fetchAllAssessmentsForClient($this->_selectedClientId);
            $this->view->availableAssessments = $availableReports;

            $availableQuotes             = Quotegen_Model_Mapper_Quote::getInstance()->fetchAllForClient($this->_selectedClientId);
            $this->view->availableQuotes = $availableQuotes;

            $availableHealthchecks             = Healthcheck_Model_Mapper_Healthcheck::getInstance()->fetchAllHealthchecksForClient($this->_selectedClientId);
            $this->view->availableHealthchecks = $availableHealthchecks;

            $availableHardwareOptimizations             = Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance()->fetchAllForClient($this->_selectedClientId);
            $this->view->availableHardwareOptimizations = $availableHardwareOptimizations;

            $rmsUploads                      = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->fetchAllForClient($this->_selectedClientId);
            $this->view->availableRmsUploads = $rmsUploads;
        }

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (isset($postData['createClient']))
            {
                $this->redirector('create-client');
            }
            else if (isset($postData['editClient']))
            {
                $this->redirector('edit-client');
            }
            else if (isset($postData['selectClient']))
            {
                $newClientId = $postData['selectClient'];
                $client      = Quotegen_Model_Mapper_Client::getInstance()->find($newClientId);
                if ($client)
                {
                    $userViewedClient = Quotegen_Model_Mapper_UserViewedClient::getInstance()->find(array($this->_userId, $client->id));
                    if ($userViewedClient instanceof Quotegen_Model_UserViewedClient)
                    {
                        $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                        Quotegen_Model_Mapper_UserViewedClient::getInstance()->save($userViewedClient);
                    }
                    else
                    {
                        $userViewedClient             = new Quotegen_Model_UserViewedClient();
                        $userViewedClient->clientId   = $client->id;
                        $userViewedClient->userId     = $this->_userId;
                        $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                        Quotegen_Model_Mapper_UserViewedClient::getInstance()->insert($userViewedClient);
                    }


                    $this->_mpsSession->selectedClientId = $newClientId;

                    // Reload the page
                    $this->redirector('index');
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
                    $this->_mpsSession->assessmentId  = $assessmentId;
                    $this->redirector('index', 'index', 'assessment');
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
                    $this->redirector('index', 'quote_devices', 'quotegen', array('quoteId' => $selectedQuoteId));
                }
                else
                {
                    // Creating a new one
                }
            }
            else if (isset($postData['createLeasedQuote']))
            {
                $selectedQuoteId = $this->_createNewQuote(Quotegen_Model_Quote::QUOTE_TYPE_LEASED);
                $this->redirector('index', 'quote_devices', 'quotegen', array('quoteId' => $selectedQuoteId));
            }
            else if (isset($postData['createPurchasedQuote']))
            {
                $selectedQuoteId = $this->_createNewQuote(Quotegen_Model_Quote::QUOTE_TYPE_PURCHASED);
                $this->redirector('index', 'quote_devices', 'quotegen', array('quoteId' => $selectedQuoteId));
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

                    $this->_mpsSession->healthcheckId = $healthcheckId;
                    $this->redirector('index', 'index', 'healthcheck');
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
                        $this->_mpsSession->hardwareOptimizationId = $hardwareOptimizationId;
                        $this->redirector('index', 'index', 'hardwareoptimization');
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("warning" => "Please select a hardware optimization"));
                    }
                }
                else
                {
                    $this->_mpsSession->hardwareOptimizationId = $hardwareOptimizationId;
                    $this->redirector('index', 'index', 'hardwareoptimization');
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
                    $this->redirector('index', 'fleet', 'proposalgen');
                }
                else
                {
                    $this->redirector('index', 'fleet', 'proposalgen', array('rmsUploadId' => $rmsUploadId));
                }
            }
        }

        $this->view->headScript()->appendFile($this->view->baseUrl('/js/default/clientSearch.js'));
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
        if (!$this->view->isAllowed(Quotegen_Model_Acl::RESOURCE_QUOTEGEN_QUOTE_INDEX, Application_Model_Acl::PRIVILEGE_VIEW))
        {
            $this->_flashMessenger->addMessage(array(
                                                    'error' => "You do not have sufficient privileges to access this page. If you feel this is in error please contact your administrator."
                                               ));
            $this->redirector('index', null, null);
        }
        $quote = new Quotegen_Model_Quote();

        // Get the system and user defaults and apply overrides for user settings
        $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
        $user         = Application_Model_Mapper_User::getInstance()->find($this->_userId);
        $quoteSetting->applyOverride($user->getUserSettings()->getQuoteSettings());

        // Update current quote object and save new quote items to database
        $quote->populate($quoteSetting->toArray());
        $quote->quoteType               = $quoteType;
        $quote->clientId                = $this->_selectedClientId;
        $quote->dateCreated             = date('Y-m-d H:i:s');
        $quote->dateModified            = date('Y-m-d H:i:s');
        $quote->quoteDate               = date('Y-m-d H:i:s');
        $quote->userId                  = $this->_userId;
        $quote->colorPageMargin         = $quoteSetting->pageMargin;
        $quote->monochromePageMargin    = $quoteSetting->pageMargin;
        $quote->colorOverageMargin      = $quoteSetting->pageMargin;
        $quote->monochromeOverageMargin = $quoteSetting->pageMargin;
        $quoteId                        = Quotegen_Model_Mapper_Quote::getInstance()->insert($quote);

        // Add a default group
        $quoteDeviceGroup            = new Quotegen_Model_QuoteDeviceGroup();
        $quoteDeviceGroup->name      = 'Default Group (Ungrouped)';
        $quoteDeviceGroup->isDefault = 1;
        $quoteDeviceGroup->setGroupPages(0);
        $quoteDeviceGroup->quoteId = $quoteId;
        Quotegen_Model_Mapper_QuoteDeviceGroup::getInstance()->insert($quoteDeviceGroup);

        // If this is a leased quote, select the first leasing schema term
        if ($quote->isLeased())
        {
            // FIXME: Use quote settings?
            $leasingSchemaTerms = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->fetchAll();
            if (count($leasingSchemaTerms) > 0)
            {

                $quoteLeaseTerm                      = new Quotegen_Model_QuoteLeaseTerm();
                $quoteLeaseTerm->quoteId             = $quote->id;
                $quoteLeaseTerm->leasingSchemaTermId = $leasingSchemaTerms [0]->id;
                Quotegen_Model_Mapper_QuoteLeaseTerm::getInstance()->insert($quoteLeaseTerm);
            }
        }

        return $quoteId;
    }

    /**
     * Allows a user to create a new client
     */
    public function createClientAction ()
    {
        $clientService = new Admin_Service_Client();

        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ['Cancel']))
            {
                $this->redirector('index');
            }

            // Create Client
            $values['dealerId'] = Zend_Auth::getInstance()->getIdentity()->dealerId;
            $clientId           = $clientService->create($values);

            if ($clientId)
            {
                $userViewedClient = Quotegen_Model_Mapper_UserViewedClient::getInstance()->find(array($this->_userId, $clientId));
                if ($userViewedClient instanceof Quotegen_Model_UserViewedClient)
                {
                    $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                    Quotegen_Model_Mapper_UserViewedClient::getInstance()->save($userViewedClient);
                }
                else
                {
                    $userViewedClient             = new Quotegen_Model_UserViewedClient();
                    $userViewedClient->clientId   = $clientId;
                    $userViewedClient->userId     = $this->_userId;
                    $userViewedClient->dateViewed = new Zend_Db_Expr("NOW()");
                    Quotegen_Model_Mapper_UserViewedClient::getInstance()->insert($userViewedClient);
                }

                $this->_flashMessenger->addMessage(array(
                                                        'success' => "Client was successfully created."
                                                   ));
                $this->_mpsSession->selectedClientId = $clientId;

                // Redirect with client id so that the client is preselected
                $this->redirector('index', null, null);
            }
        }

        $this->view->form = $clientService->getForm();
    }

    /**
     * Action to handle editing a client
     */
    public function editClientAction ()
    {
        // Get the passed client id
        $clientId = $this->_selectedClientId;
        // Get the client object from the database
        $client = Quotegen_Model_Mapper_Client::getInstance()->find($clientId);

        if (!$client)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Please select a client first.'));
            $this->redirector('index');
        }

        // Start the client service
        $clientService = new Admin_Service_Client();

        $clientService->populateForm($clientId);
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
     * JSON ACTION: Handles searching for a client by name and dealerId
     */
    public function searchForClientAction ()
    {
        $searchTerm = $this->getParam('query', false);
        $results    = array();
        if ($searchTerm !== false)
        {
            $clients = Quotegen_Model_Mapper_Client::getInstance()->searchForClientByCompanyNameAndDealer($searchTerm, Zend_Auth::getInstance()->getIdentity()->dealerId);
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
        $this->view->clients = Quotegen_Model_Mapper_Client::getInstance()->fetchClientListForDealer(Zend_Auth::getInstance()->getIdentity()->dealerId);
    }
}