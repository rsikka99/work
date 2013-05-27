<?php

/**
 * Class Admin_LogController
 */
class Admin_LogController extends Tangent_Controller_Action
{
    public function indexAction ()
    {
        $this->_helper->_layout->setLayout('layout-fluid');
        $this->view->lines = file_get_contents(DATA_PATH . "/logs/application.log");
    }
}