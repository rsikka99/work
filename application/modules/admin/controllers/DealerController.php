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
        $dealerId = $this->_getParam('id');

        if (!$dealerId)
        {
            $this->_helper->flashMessenger(array('warning' => 'Error select dealer to delete.'));
            $this->redirect('index');
        }

    }

    public function createAction ()
    {

        $form = new Admin_Form_Dealer();

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if ($form->isValid($values))
            {
                // Create a new dealer object
                $dealer = new Admin_Model_Dealer();
                $dealer->populate($values);
                $dealer->dateCreated = date("Y-m-d");

                // Create a new report setting based on the system default report setting
                // Then assigned the dealer object the new id.
                $reportSetting = new Proposalgen_Model_Report_Setting();
                $reportSetting->ApplyOverride(Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchSystemReportSetting());
                $dealer->reportSettingId = Proposalgen_Model_Mapper_Report_Setting::getInstance()->insert($reportSetting);

                // Create a new quote setting based on the system default quote setting
                // Then assigned the dealer quote setting id object it's new id
                $quoteSetting = new Quotegen_Model_QuoteSetting();
                $quoteSetting->applyOverride(Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting());
                $dealer->quoteSettingId = Quotegen_Model_Mapper_QuoteSetting::getInstance()->insert($quoteSetting);

                // Save the dealer with the id to the database
                $dealerId = Admin_Model_Mapper_Dealer::getInstance()->insert($dealer);

                if ($dealerId)
                {
                    $this->_helper->flashMessenger(array('success' => "Dealer {$dealer->dealerName} successfully created"));
                    $this->redirector('index');
                }
                else
                {
                    $this->_helper->flashMessenger(array('danger' => "Error saving dealer to database.  If problem persists please contact your system administrator."));
                }
            }
            else
            {
                $this->_helper->flashMessenger(array('danger' => 'Errors on form, please correct and try again'));
            }
        }

        $this->view->form = $form;
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
                    // Delete the dealer and the related report setting
                    $reportSettingDeleted = Proposalgen_Model_Mapper_Report_Setting::getInstance()->delete($dealer->reportSettingId);
                    $quoteSettingDeleted = Quotegen_Model_Mapper_QuoteSetting::getInstance()->delete($dealer->quoteSettingId);
                    $dealerDeleted  = Admin_Model_Mapper_Dealer::getInstance()->delete($dealer);

                    // If we have successfully deleted it then redirect and display message
                    if ($dealerDeleted > 0 && $reportSettingDeleted > 0 && $quoteSettingDeleted > 0)
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