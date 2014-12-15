<?php
use Tangent\Controller\Action;

/**
 * Class Admin_LogController
 */
class Admin_LogController extends Action
{
    public function indexAction ()
    {
        $this->_pageTitle  = array('Application Log');
        $this->view->lines = file_get_contents(DATA_PATH . "/logs/application.log");
    }
}