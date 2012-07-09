<?php

class Quotegen_Quote_PricingController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::PRICING_CONTROLLER);
    }

    /**
     * This function takes care of adding pages to devices
     */
    public function indexAction ()
    {
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $this->_helper->flashMessenger(array (
                    'info' => 'Saving on this page is not implemented yet!' 
            ));
        }
    }
}