<?php

class Quotegen_Quote_ReportsController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::REPORTS_CONTROLLER);
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

