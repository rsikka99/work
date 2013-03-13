<?php

class Admin_DealerController extends Tangent_Controller_Action
{
    public function indexAction ()
    {
        $dealerMapper = Admin_Model_Mapper_Dealer::getInstance();
        $paginator    = new Zend_Paginator(new My_Paginator_MapperAdapter($dealerMapper));

        // Gets the current page for the passed parameter
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Sets the amount of dealers that we are showing per page.
        $paginator->setItemCountPerPage(15);

        $this->view->paginator = $paginator;
    }

    public function editAction ()
    {

    }

    public function deleteAction ()
    {
        $dealerId = $this->_getParam('id');
        $dealer   = Admin_Model_Mapper_Dealer::getInstance()->find($dealerId);

        $message = "Are you sure you want to delete {$dealer->dealerName}";
        $form    = new Application_Form_Delete($message);

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if ($form->isValid($values))
            {
                if (!isset($values ['cancel']))
                {
                    // Delete the dealer
                    $rowsAffected = Admin_Model_Mapper_Dealer::getInstance()->delete($dealer);

                    // If we have successfully deleted it then redirect and display message
                    if ($rowsAffected > 0)
                    {
                        $this->_helper->flashMessenger(array("success" => "Successfully delete {$dealer->dealerName}."));
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array("danger" => "Error deleting {$dealer->dealerName}, please try again."));
                    }
                    $this->redirector("index");
                }
                else
                {
                    $this->redirector("index");
                }
            }
        }
        $this->view->form = $form;
    }
}