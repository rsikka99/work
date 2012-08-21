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
        $form = new Quotegen_Form_Group($this->_quote);
                
        $this->view->form = $form;
    }
}

