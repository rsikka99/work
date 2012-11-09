<?php

class Admin_LogsController extends Zend_Controller_Action
{

    public function init ()
    {
        /* Initialize action controller here */
    }

    public function indexAction ()
    {
        // action body
        $this->view->logs = Admin_Model_Mapper_Log::getInstance()->fetchAll();
        
    }
}

