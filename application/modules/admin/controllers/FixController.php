<?php

/**
 * Class Admin_FixController
 */
class Admin_FixController extends Tangent_Controller_Action
{

    /**
     * Keeps track of whether or not we are root
     *
     * @var bool
     */
    protected $_currentUserIsRoot = false;

    public function init ()
    {
        if (Zend_Auth::getInstance()->getIdentity()->id != 1)
        {
            throw new Exception("You are not allowed in here!");
        }
    }

    /**
     * Blank
     */
    public function indexAction ()
    {

    }

    /**
     * Used to fix toners.
     */
    public function tonersAction ()
    {
        $fixTonerService = new Admin_Service_Fix_Toner();
        $form            = $fixTonerService->getForm();

        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();

            if (isset($values ["performUpload"]))
            {
                /*
                * Handle Upload
                */
                if ($form->isValid($values))
                {
                    $success = $fixTonerService->processUpload($values);

                    if ($success)
                    {
                        $this->_flashMessenger->addMessage(array("success" => "Processed Toners"));
                        $this->redirector('toners', null, null);
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("danger" => $fixTonerService->errorMessages));
                    }
                }
            }
        }

        $this->view->form = $form;
    }
}

