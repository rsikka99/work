<?php

class Quotegen_Quote_ProfitabilityController extends Quotegen_Library_Controller_Quote
{
    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::PROFITABILITY_CONTROLLER);
    }

    /**
     * This function takes care of editing quote settings
     */
    public function indexAction ()
    {
        
    }
}

