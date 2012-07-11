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
        $form = new Quotegen_Form_PagesAndPricing($this->_quote);
        
        // Require that we have a quote object in the database to use this page
        $this->requireQuote();
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ['back']))
            {
                $this->_helper->redirector('index', 'quote_devices', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
            else
            {
                if ($form->isValid($values))
                {
                    $this->_helper->flashMessenger(array (
                            'info' => 'Form is valid!'
                    ));
                }
                else
                {
                    
                    $this->_helper->flashMessenger(array (
                            'danger' => 'Please fix the errors below before saving.'
                    ));
                    $form->buildBootstrapErrorDecorators();
                }
                
                
                $this->_helper->flashMessenger(array (
                        'info' => 'Saving on this page is not implemented yet!' 
                ));
                if (isset($values ['saveAndContinue']))
                {
                    $this->_helper->redirector('index', 'quote_settings', null, array (
                            'quoteId' => $this->_quoteId 
                    ));
                }
            }
        }
        
        $this->view->form = $form;
    }
}