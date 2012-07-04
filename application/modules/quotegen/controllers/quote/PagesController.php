<?php

class Quotegen_Quote_PagesController extends Quotegen_Library_Controller_Quote
{

    public function init ()
    {
        parent::init();
        Quotegen_View_Helper_Quotemenu::setActivePage(Quotegen_View_Helper_Quotemenu::PAGES_CONTROLLER);
    }

    /**
     * This function takes care of adding pages to devices
     */
    public function indexAction ()
    {
    }
}