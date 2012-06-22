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
        //My_Log::info("This is a test log", My_Log::SOURCE_ZENDLOG);
        $writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);
        $logger->log('Logging Started', Zend_Log::INFO);
        $logger->log('Candying that bacon.', Zend_Log::INFO);
        $logger->log('User is not authenticated!', Zend_Log::INFO);
        $logger->log('User has 4 login attempts left.', Zend_Log::INFO);
    }
}


