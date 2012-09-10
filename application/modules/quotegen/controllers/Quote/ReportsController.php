<?php

class Quotegen_Quote_ReportsController extends Quotegen_Library_Controller_Quote
{
    public $contexts = array (
            'purchase-quote' => array (
                    'docx' 
            ), 
            'lease-quote' => array (
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
        
        $form = new Quotegen_Form_Quote_General($this->_quote);
        
        $populateData = $this->_quote->toArray();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (! isset($values ['goBack']))
            {
                if ($form->isValid($values))
                {
                    $this->_quote->populate($values);
                    $this->saveQuote();
                    Quotegen_Model_Mapper_Quote::getInstance()->save($this->_quote);
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => 'Please correct the errors below.' 
                    ));
                }
            }
            else
            {
                $this->_helper->redirector('index', 'quote_profitability', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
        }
        
        $form->populate($populateData);
        $this->view->form = $form;
        $this->view->navigationForm = new Quotegen_Form_Quote_Navigation(Quotegen_Form_Quote_Navigation::BUTTONS_BACK);
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

