<?php

class Default_IndexController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // action body
        My_Log::log('Sample Log (DefaultModule - IndexController - IndexAction): Logging Started', Zend_Log::INFO);
        My_Log::log('Sample Log (DefaultModule - IndexController - IndexAction): Candying that bacon.', Zend_Log::INFO);
        My_Log::log('Sample Log (DefaultModule - IndexController - IndexAction): User is not authenticated!', Zend_Log::INFO);
        My_Log::log('Sample Log (DefaultModule - IndexController - IndexAction): User has 4 login attempts left.', Zend_Log::INFO);
    }
}


