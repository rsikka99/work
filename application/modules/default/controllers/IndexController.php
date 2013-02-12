<?php
class Default_IndexController extends Zend_Controller_Action
{
    /**
     * @var int
     */
    protected $_selectedClientId;

    /**
     * The namespace for our mps application
     *
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    public function init ()
    {
        /* Initialize action controller here */
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
        if (isset($this->_mpsSession->selectedClientId))
        {
            $this->_selectedClientId      = $this->_mpsSession->selectedClientId;
            $this->view->selectedClientId = $this->_selectedClientId;
        }
    }

    /**
     * Main landing page
     */
    public function indexAction ()
    {
        if ($this->_selectedClientId === null)
        {
            $this->_helper->redirector('select-client');
        }

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            /**
             * Change clients
             */
            if (isset($postData['changeClient']))
            {
                unset($this->_mpsSession->selectedClientId);
                $this->_helper->redirector('select-client');
            }
            else if (isset($postData['startNewAssessment']))
            {
                $this->_helper->redirector('index', 'survey', 'proposalgen');
            }
        }

    }

    /**
     * Allows a user to select a client to work with
     */
    public function selectClientAction ()
    {
        $clientId     = $this->_getParam('clientId', false);
        $clientMapper = Quotegen_Model_Mapper_Client::getInstance();
        if ($clientId !== false)
        {
            /**
             * Here we reset our selected client if the id is 0
             */
            if ((int)$clientId === 0)
            {
                unset($this->_mpsSession->selectedClientId);
            }
            else
            {
                $client = $clientMapper->find($clientId);
                if ($client instanceof Quotegen_Model_Client)
                {
                    $this->_mpsSession->selectedClientId = $clientId;
                    $this->_helper->redirector('index');
                }
            }
        }
        $clients             = $clientMapper->fetchAll(null, null, 1000, null);
        $this->view->clients = $clients;
    }
}