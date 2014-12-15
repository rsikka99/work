<?php
use MPSToolbox\Legacy\Modules\Admin\Services\FixTonerService;
use Tangent\Controller\Action;

/**
 * Class Admin_FixController
 */
class Admin_FixController extends Action
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
        $this->_pageTitle = array('Fix Functions');
    }

    /**
     * Used to fix toners.
     */
    public function tonersAction ()
    {
        $this->_pageTitle = array('Fix Toners');
        $fixTonerService  = new FixTonerService();
        $form             = $fixTonerService->getForm();

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
                        $this->redirectToRoute('admin.fix-toners');
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array("danger" => $fixTonerService->errorMessages));
                    }
                }
            }
            else
            {
                $this->redirectToRoute('admin');
            }
        }

        $this->view->form = $form;
    }
}

