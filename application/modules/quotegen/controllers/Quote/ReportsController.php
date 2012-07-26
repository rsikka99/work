<?php

class Quotegen_Quote_ReportsController extends Quotegen_Library_Controller_Quote
{
    public $contexts = array (
            'purchase-quote' => array (
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
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['back']))
            {
                $this->_helper->redirector('index', 'quote_settings', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
        }
    }

    public function purchaseQuoteAction ()
    {
    }

    public function leaseQuoteAction ()
    {
    }

    public function orderListAction ()
    {
    }
}

