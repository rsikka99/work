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

    /**
     * The namespace for our proposal generator
     *
     * @deprecated This will eventually be migrated to be in mps-tools session namespace
     * @var Zend_Session_Namespace
     */
    protected $_proposalSession;

    public function init ()
    {
        /* Initialize action controller here */
        $this->_mpsSession      = new Zend_Session_Namespace('mps-tools');
        $this->_proposalSession = new Zend_Session_Namespace('proposalgenerator_report');
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

        if ($this->_selectedClientId > 0)
        {
            $availableReports                 = Proposalgen_Model_Mapper_Assessment::getInstance()->fetchAllAssessmentsForClient($this->_selectedClientId);
            $this->view->availableAssessments = $availableReports;

            $availableQuotes             = Quotegen_Model_Mapper_Quote::getInstance()->fetchAllForClient($this->_selectedClientId);
            $this->view->availableQuotes = $availableQuotes;
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
                $selectedReportId = $postData['selectAssessment'];

                $validReportIds = array(0);
                foreach ($availableReports as $report)
                {
                    $validReportIds[] = $report->id;
                }

                $inArray = new Zend_Validate_InArray($validReportIds);

                if ($inArray->isValid($selectedReportId))
                {
                    $this->_proposalSession->reportId = $selectedReportId;
                    $this->redirector('index', 'survey', 'proposalgen');
                }
            }
            else if (isset($postData['selectQuote']))
            {
                $selectedQuoteId = $postData['selectQuote'];

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
            else if (isset($postData['createHardwareOptimization']))
            {
                $hardwareOptimizationId = $this->_createNewHardwareOptimization();
                $this->redirector('index', 'index', 'hardwareoptimization', array('hardwareOptimizationId' => $hardwareOptimizationId));
            }
        }

        $this->view->headScript()->appendFile($this->view->baseUrl('/js/default/clientSearch.js'));
    }

    protected function _createNewHardwareOptimization ()
    {
        $hardwareOptimization           = new Proposalgen_Model_Hardware_Optimization;
        $hardwareOptimization->clientId = $this->_selectedClientId;
        $hardwareOptimizationId         = Proposalgen_Model_Mapper_Hardware_Optimization::getInstance()->insert($hardwareOptimization);

        return $hardwareOptimizationId;
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
        $userSetting  = Quotegen_Model_Mapper_UserQuoteSetting::getInstance()->fetchUserQuoteSetting($this->_userId);
        $quoteSetting->applyOverride($userSetting);

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