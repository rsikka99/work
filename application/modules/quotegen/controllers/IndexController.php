<?php

/**
 * Class Quotegen_IndexController
 */
class Quotegen_IndexController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        $this->_helper->contextSwitch()
                      ->addActionContext('get-reports-for-client', array(
                          'xml',
                          'json'
                      ))
                      ->setAutoJsonSerialization(true)
                      ->initContext();

        parent::init();
    }

    public function indexAction ()
    {
        $request = $this->getRequest();

        $user = Application_Model_Mapper_User::getInstance()->find($this->_userId);
        // Get the system and user defaults and apply overrides for user settings
        $quoteSetting = Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting();
        $userSetting  = $user->getUserSettings()->getQuoteSettings();
        $quoteSetting->applyOverride($userSetting);

        $quoteForm = new Quotegen_Form_Quote();

        $clientId = $this->_getParam('clientId', false);
        if ($clientId)
        {
            $quoteForm->populate(array('clientId' => $clientId));
        }

        if ($request->isPost())
        {
            $values = $request->getPost();

            if ($quoteForm->isValid($values))
            {
                $formValues = $quoteForm->getValues();

                // Update current quote object and save new quote items to database
                $this->_quote->populate($quoteSetting->toArray());
                $this->_quote->populate($formValues);
                $this->_quote->dateCreated             = date('Y-m-d H:i:s');
                $this->_quote->quoteDate               = date('Y-m-d H:i:s');
                $this->_quote->userId                  = $this->_userId;
                $this->_quote->colorPageMargin         = $quoteSetting->pageMargin;
                $this->_quote->monochromePageMargin    = $quoteSetting->pageMargin;
                $this->_quote->colorOverageMargin      = $quoteSetting->pageMargin;
                $this->_quote->monochromeOverageMargin = $quoteSetting->pageMargin;
                $quoteId                               = $this->saveQuote();

                // Add a default group
                $quoteDeviceGroup            = new Quotegen_Model_QuoteDeviceGroup();
                $quoteDeviceGroup->name      = 'Default Group (Ungrouped)';
                $quoteDeviceGroup->isDefault = 1;
                $quoteDeviceGroup->setGroupPages(0);
                $quoteDeviceGroup->quoteId = $quoteId;

                // If this is a leased quote, select the first leasing schema term
                if ($this->_quote->isLeased())
                {
                    // FIXME: Use quote settings?
                    $leasingSchemaTerms = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->fetchAll();
                    if (count($leasingSchemaTerms) > 0)
                    {

                        $quoteLeaseTerm                      = new Quotegen_Model_QuoteLeaseTerm();
                        $quoteLeaseTerm->quoteId             = $this->_quote->id;
                        $quoteLeaseTerm->leasingSchemaTermId = $leasingSchemaTerms [0]->id;
                        Quotegen_Model_Mapper_QuoteLeaseTerm::getInstance()->insert($quoteLeaseTerm);
                    }
                }

                // Redirect to the first page of the quote workflow
                $this->redirector('index', 'quote_devices', null, array('quoteId' => $quoteId));
            }
        }
        $this->view->quoteForm = $quoteForm;
    }

    /**
     * Allows a user to work with an existing quote
     */
    public function existingQuoteAction ()
    {
        $request           = $this->getRequest();
        $existingQuoteForm = new Quotegen_Form_SelectQuote($this->_userId);

        if ($request->isPost())
        {
            $values = $request->getPost();

            if (isset($values ['quoteId']))
            {
                // Get the clientId and find the client
                $clientId = $this->_getParam('clientId');
                // Load the quotes for the current client
                $this->view->quotes = $this->getQuotesForClient($clientId);

                // Existing Quote
                if ($existingQuoteForm->isValid($values))
                {
                    // Redirect to the build controller
                    $this->redirector('index', 'quote_devices', null, array(
                        'quoteId' => $values ['quoteId']
                    ));
                }
                else
                {
                    $this->_flashMessenger->addMessage(array(
                        'danger' => "There was an error selecting your quote. Please try again."
                    ));
                }
            }
        }
        $this->view->existingQuoteForm = $existingQuoteForm;
    }

    /**
     * Gets an array of quotes that belong to a user/client
     *
     * @param int $clientId
     *
     * @internal param int $userId
     *
     * @return array The array of quotes
     */
    public function getQuotesForClient ($clientId)
    {
        $client = Quotegen_Model_Mapper_Client::getInstance()->find($clientId);

        $quoteList = array();
        // Ensure that the client exists
        if ($client instanceof Quotegen_Model_Client)
        {
            // If the client exists get all quotes for the client
            $quotes = Quotegen_Model_Mapper_Quote::getInstance()->fetchAllForClient($client->id);

            // Create a quote array to create option data
            /* @var $quote Quotegen_Model_Quote */
            foreach ($quotes as $quote)
            {
                $quoteArray   = array(
                    'id'         => $quote->id,
                    'clientName' => $quote->getClient()->companyName,
                    'quotedate'  => $quote->quoteDate,
                    'isLeased'   => $quote->isLeased()
                );
                $quoteList [] = $quoteArray;
            }
        }

        return $quoteList;
    }

    public function getReportsForClientAction ()
    {
        // Get the clientId and find the client
        $clientId           = $this->_getParam('clientId');
        $this->view->quotes = $this->getQuotesForClient($clientId);
    }

    public function createClientAction ()
    {
        $clientService = new Admin_Service_Client();

        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ['cancel']))
            {
                $this->redirector('index');
            }

            // Create Client
            $clientId = $clientService->create($values);

            if ($clientId)
            {
                $this->_flashMessenger->addMessage(array(
                    'success' => "Client was successfully created."
                ));

                // Redirect with client id so that the client is preselected
                $this->redirector('index', null, null, array(
                    'clientId' => $clientId
                ));
            }
        }

        $this->view->form = $clientService->getForm();
    }
}

