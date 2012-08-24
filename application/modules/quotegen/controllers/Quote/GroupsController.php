<?php

class Quotegen_Quote_GroupsController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::GROUPS_CONTROLLER);
    }

    /**
     * This function takes care of editing quote settings
     */
    public function indexAction ()
    {
        $form = new Quotegen_Form_Quote_Group($this->_quote);
        
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            
            if (isset($values ['goBack']))
            {
                $this->_helper->redirector('index', 'quote_devices', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
            else if (isset($values ['saveAndContinue']))
            {
                $this->_helper->redirector('index', 'quote_pages', null, array (
                        'quoteId' => $this->_quoteId 
                ));
            }
        }
        
        $this->view->form = $form;
    }
}

