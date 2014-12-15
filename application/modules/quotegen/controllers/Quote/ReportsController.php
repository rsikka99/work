<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\QuoteGeneralForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Forms\QuoteNavigationForm;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ContractTemplateMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ContractTemplateModel;

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
        $this->_navigation->setActiveStep(\MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteStepsModel::STEP_FINISHED);

        // Require that we have a quote object in the database to use this page
        $this->requireQuote();

        $this->_helper->contextSwitch()->initContext();
    }

    /**
     * This function takes care of displaying reports
     */
    public function indexAction ()
    {
        $this->_pageTitle = array('Quote', 'Reports');
        $request          = $this->getRequest();

        $form = new QuoteGeneralForm($this->_quote);
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['goBack']))
            {
                if ($form->isValid($values))
                {
                    $this->_quote->populate($values);
                    $this->saveQuote();
                    QuoteMapper::getInstance()->save($this->_quote);
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
                $this->redirectToRoute('quotes.hardware-financing', array(
                    'quoteId' => $this->_quoteId
                ));
            }
        }

        $this->view->form           = $form;
        $this->view->navigationForm = new QuoteNavigationForm(QuoteNavigationForm::BUTTONS_BACK);
    }

    /**
     * Creates a purchased quote
     */
    public function purchaseQuoteAction ()
    {
        $this->view->filename       = $this->generateReportFilename($this->_quote->getClient(), My_Brand::getDealerBranding()->purchaseQuoteTitle) . ".docx";
        $this->view->clientId       = $this->_quote->clientId;
        $this->view->quoteId        = $this->_quote->id;
        $this->view->dealerLogoFile = $this->getDealerLogoFile();
    }

    /**
     * Creates a leased quote
     */
    public function leaseQuoteAction ()
    {
        $this->view->filename       = $this->generateReportFilename($this->_quote->getClient(), My_Brand::getDealerBranding()->leaseQuoteTitle) . ".docx";
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
        $contractTemplateId = $this->_getParam('contractTemplateId', false);

        if ($contractTemplateId === false)
        {
            $this->sendJsonError('Invalid Contract Template Id');
        }

        /**
         * Users can only use contract templates that are owned by them or flagged as system templates
         */
        $contractTemplate = ContractTemplateMapper::getInstance()->find((int)$contractTemplateId);
        if (!$contractTemplate instanceof ContractTemplateModel || ($contractTemplate->dealerId != $this->_identity->dealerId && !$contractTemplate->isSystemTemplate))
        {
            $this->sendJsonError('Invalid Contract Template');
        }

        $this->view->contractTemplate = $contractTemplate;
        $this->view->filename         = $this->generateReportFilename($this->_quote->getClient(), 'Contract') . ".docx";
        $this->view->clientId         = $this->_quote->clientId;
        $this->view->quoteId          = $this->_quote->id;
    }
}

