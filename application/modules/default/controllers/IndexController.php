<?php
class Default_IndexController extends Zend_Controller_Action
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
            $this->_selectedClientId      = $this->_mpsSession->selectedClientId;
            $this->view->selectedClientId = $this->_selectedClientId;
        }
    }

    /**
     * Main landing page
     */
    public function indexAction ()
    {
        if ($this->_selectedClientId > 0)
        {
            $availableReports                 = Proposalgen_Model_Mapper_Report::getInstance()->fetchAllReportsForClient($this->_selectedClientId);
            $this->view->availableAssessments = $availableReports;

            $availableQuotes             = Quotegen_Model_Mapper_Quote::getInstance()->fetchAllForClient($this->_selectedClientId);
            $this->view->availableQuotes = $availableQuotes;
        }


        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (isset($postData['createClient']))
            {
                $this->_helper->redirector('create-client');
            }
            else if (isset($postData['selectClient']))
            {
                $newClientId = $postData['selectClient'];
                $client      = Quotegen_Model_Mapper_Client::getInstance()->find($newClientId);
                if ($client)
                {
                    $this->_mpsSession->selectedClientId = $newClientId;

                    // Reload the page
                    $this->_helper->redirector();
                }
                else
                {
                    $this->_helper->flashMessenger(array('danger' => 'Invalid Client'));
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
                    $this->_helper->redirector('index', 'survey', 'proposalgen');
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
                    $this->_helper->redirector('index', 'quote_devices', 'quotegen', array('quoteId' => $selectedQuoteId));
                }
                else
                {
                    // Creating a new one


                }
            }
            else if (isset($postData['createLeasedQuote']))
            {
                $selectedQuoteId = $this->_createNewQuote(Quotegen_Model_Quote::QUOTE_TYPE_LEASED);
                $this->_helper->redirector('index', 'quote_devices', 'quotegen', array('quoteId' => $selectedQuoteId));
            }
            else if (isset($postData['createPurchasedQuote']))
            {
                $selectedQuoteId = $this->_createNewQuote(Quotegen_Model_Quote::QUOTE_TYPE_PURCHASED);
                $this->_helper->redirector('index', 'quote_devices', 'quotegen', array('quoteId' => $selectedQuoteId));
            }
        }


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
            if (isset($values ['cancel']))
            {
                $this->_helper->redirector('index');
            }

            // Create Client
            $clientId = $clientService->create($values);

            if ($clientId)
            {
                $this->_helper->flashMessenger(array(
                                                    'success' => "Client was successfully created."
                                               ));
                $this->_mpsSession->selectedClientId = $clientId;

                // Redirect with client id so that the client is preselected
                $this->_helper->redirector('index', null, null);
            }
        }

        $this->view->form = $clientService->getForm();
    }
}