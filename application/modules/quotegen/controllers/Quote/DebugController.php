<?php

class Quotegen_Quote_DebugController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::DEBUG_CONTROLLER);
    }

    public function indexAction ()
    {
    }
}

