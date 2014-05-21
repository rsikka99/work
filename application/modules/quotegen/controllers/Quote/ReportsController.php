<?php

/**
 * Class Quotegen_Quote_ReportsController
 */
class Quotegen_Quote_ReportsController extends Quotegen_Library_Controller_Quote
{
    public $contexts = array(
        "purchase-quote" => array(
            'docx'
        ),
        'lease-quote'    => array(
            'docx'
        ),
        'order-list'     => array(
            'xlsx'
        ),
        'contract'       => array(
            'docx'
        )
    );

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::REPORTS_CONTROLLER);

        // Require that we have a quote object in the database to use this page
        $this->requireQuote();

        $this->_helper->contextSwitch()->initContext();
    }

    /**
     * This function takes care of displaying reports
     */
    public function indexAction ()
    {
        $this->view->headTitle('Quote');
        $this->view->headTitle('Reports');
        $request = $this->getRequest();

        $form = new Quotegen_Form_Quote_General($this->_quote);
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['goBack']))
            {
                if ($form->isValid($values))
                {
                    $this->_quote->populate($values);
                    $this->saveQuote();
                    Quotegen_Model_Mapper_Quote::getInstance()->save($this->_quote);
                }
                else
                {
                    $this->_flashMessenger->addMessage(array(
                        'danger' => 'Please correct the errors below.'
                    ));
                }
            }
            else
            {
                $this->redirector('index', 'quote_profitability', null, array(
                    'quoteId' => $this->_quoteId
                ));
            }
        }

        $this->view->form           = $form;
        $this->view->navigationForm = new Quotegen_Form_Quote_Navigation(Quotegen_Form_Quote_Navigation::BUTTONS_BACK);
    }

    /**
     * Creates a purchased quote
     */
    public function purchaseQuoteAction ()
    {
        $this->view->filename       = $this->generateReportFilename($this->_quote->getClient(), 'Purchase Quote') . ".docx";
        $this->view->clientId       = $this->_quote->clientId;
        $this->view->quoteId        = $this->_quote->id;
        $this->view->dealerLogoFile = $this->getDealerLogoFile();
    }

    /**
     * Creates a leased quote
     */
    public function leaseQuoteAction ()
    {
        $this->view->filename       = $this->generateReportFilename($this->_quote->getClient(), 'Lease Quote') . ".docx";
        $this->view->clientId       = $this->_quote->clientId;
        $this->view->quoteId        = $this->_quote->id;
        $this->view->dealerLogoFile = $this->getDealerLogoFile();

    }

    /**
     * Creates an order list
     */
    public function orderListAction ()
    {
        $this->view->filename = $this->generateReportFilename($this->_quote->getClient(), ucwords($this->_quote->quoteType) . ' Quote Order List') . ".xlsx";
        $this->view->clientId = $this->_quote->clientId;
        $this->view->quoteId  = $this->_quote->id;
    }

    /**
     * Creates a contract
     */
    public function contractAction ()
    {
        $this->view->filename = $this->generateReportFilename($this->_quote->getClient(), 'Contract') . ".docx";
        $this->view->clientId = $this->_quote->clientId;
        $this->view->quoteId  = $this->_quote->id;
    }
}

