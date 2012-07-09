<?php

class Quotegen_Quote_ReportsController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::REPORTS_CONTROLLER);
        
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();
    }

    /**
     * This function takes care of displaying reports
     */
    public function indexAction ()
    {
    }

    public function purchaseQuoteAction ()
    {
    }

    public function orderListAction ()
    {
    }
}

