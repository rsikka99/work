<?php

/**
 * Class Admin_ContractTemplate
 */
class Admin_ContractTemplateController extends Tangent_Controller_Action
{
    /**
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    /**
     * Displays all clients
     */
    public function indexAction ()
    {
        $this->view->headTitle('System');
        $this->view->headTitle('Contracts');
        $this->view->headTitle('Template Management');
    }
}

