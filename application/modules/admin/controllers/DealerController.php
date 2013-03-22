<?php

class Admin_DealerController extends Tangent_Controller_Action
{
    /**
     * This action lists all the dealers in the system
     */
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

    /**
     * This action is used to edit a dealer
     */
    public function editAction ()
    {
        $dealerId = $this->_getParam('id', false);

        if ($dealerId === false)
        {
            $this->flashMessenger(array('warning' => 'You must select a dealer to edit.'));
            $this->redirect('index');
        }

        /**
         * Fetch the dealer
         */
        $dealerMapper = Admin_Model_Mapper_Dealer::getInstance();
        $dealer       = $dealerMapper->find($dealerId);

        if (!$dealer instanceof Admin_Model_Dealer)
        {
            $this->flashMessenger(array('warning' => 'Invalid dealer selected.'));
            $this->redirect('index');
        }

        $form = new Admin_Form_Dealer();
        $form->populate($dealer->toArray());

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData ['cancel']))
            {
                $this->redirector("index");
            }
            else
            {
                if ($form->isValid($postData))
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    try
                    {
                        // Save dealer object
                        $dealer->populate($form->getValues());
                        $dealerMapper->save($dealer);

                        // All done
                        $this->_helper->flashMessenger(array('success' => "{$dealer->dealerName} has been successfully updated!"));
                        $this->redirector("index");
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        $this->_helper->flashMessenger(array('danger' => "Error saving dealer to database.  If problem persists please contact your system administrator."));
                        My_Log::logException($e);
                    }
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * This action creates a brand new dealer
     */
    public function createAction ()
    {

        $form = new Admin_Form_Dealer();

        $request = $this->getRequest();
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (!isset($values ['cancel']))
            {
                if ($form->isValid($values))
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    try
                    {
                        $db->beginTransaction();

                        // Create a new dealer object
                        $dealer = new Admin_Model_Dealer();
                        $dealer->populate($values);
                        $dealer->dateCreated = date("Y-m-d");

                        // Save the dealer with the id to the database
                        $dealerId = Admin_Model_Mapper_Dealer::getInstance()->insert($dealer);

                        // Create a new report setting based on the system default report setting
                        // Then assigned the dealer object the new id.
                        $reportSetting = new Proposalgen_Model_Report_Setting();
                        $reportSetting->ApplyOverride(Proposalgen_Model_Mapper_Report_Setting::getInstance()->fetchSystemReportSetting());
                        $dealer->reportSettingId = Proposalgen_Model_Mapper_Report_Setting::getInstance()->insert($reportSetting);

                        $surveySetting = new Proposalgen_Model_Survey_Setting();
                        $surveySetting->ApplyOverride(Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchSystemDefaultSurveySettings());
                        $dealer->surveySettingId = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->insert($surveySetting);

                        $dealerReportSetting                  = new Proposalgen_Model_Dealer_Report_Setting();
                        $dealerReportSetting->reportSettingId = $dealer->reportSettingId;
                        $dealerReportSetting->dealerId        = $dealer->id;
                        Proposalgen_Model_Mapper_Dealer_Report_Setting::getInstance()->insert($dealerReportSetting);

                        $dealerSurveySetting                  = new Proposalgen_Model_Dealer_Survey_Setting();
                        $dealerSurveySetting->surveySettingId = $dealer->surveySettingId;
                        $dealerSurveySetting->dealerId        = $dealer->id;
                        Proposalgen_Model_Mapper_Dealer_Survey_Setting::getInstance()->insert($dealerSurveySetting);

                        // Create a new quote setting based on the system default quote setting
                        // Then assigned the dealer quote setting id object it's new id
                        $quoteSetting = new Quotegen_Model_QuoteSetting();
                        $quoteSetting->applyOverride(Quotegen_Model_Mapper_QuoteSetting::getInstance()->fetchSystemQuoteSetting());
                        $dealer->quoteSettingId = Quotegen_Model_Mapper_QuoteSetting::getInstance()->insert($quoteSetting);

                        $dealerQuoteSetting                 = new Quotegen_Model_DealerQuoteSetting();
                        $dealerQuoteSetting->quoteSettingId = $dealer->quoteSettingId;
                        $dealerQuoteSetting->dealerId       = $dealer->id;
                        Quotegen_Model_Mapper_DealerQuoteSetting::getInstance()->insert($dealerQuoteSetting);


                        $db->commit();

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
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        $this->_helper->flashMessenger(array('danger' => "Error saving dealer to database.  If problem persists please contact your system administrator."));
                        My_Log::logException($e);
                    }
                }
                else
                {
                    $this->_helper->flashMessenger(array('danger' => 'Errors on form, please correct and try again'));
                }
            }
            else
            {
                $this->redirector('index');
            }
        }

        $this->view->form = $form;
    }

    /**
     * This action deletes a dealer
     */
    public function deleteAction ()
    {
        $dealerId = $this->_getParam('id');
        $dealer   = Admin_Model_Mapper_Dealer::getInstance()->find($dealerId);

        if (!$dealer instanceof Admin_Model_Dealer)
        {
            $this->flashMessenger(array('warning' => 'Invalid dealer selected.'));
            $this->redirect('index');
        }


        $message = "Are you sure you want to delete {$dealer->dealerName}";
        $form    = new Application_Form_Delete($message);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData))
            {
                if (isset($postData ['cancel']))
                {
                    $this->redirector("index");
                }
                else
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    try
                    {
                        $db->beginTransaction();

                        // Delete the dealer and the related report setting
                        $reportSettingDeleted = Proposalgen_Model_Mapper_Report_Setting::getInstance()->delete($dealer->reportSettingId);
                        $quoteSettingDeleted  = Quotegen_Model_Mapper_QuoteSetting::getInstance()->delete($dealer->quoteSettingId);
                        $dealerDeleted        = Admin_Model_Mapper_Dealer::getInstance()->delete($dealer);


                        /**
                         * Deleting did not work. Throw an exception so that we can roll back the database
                         */
                        if ($dealerDeleted == 0 || $reportSettingDeleted == 0 || $quoteSettingDeleted == 0)
                        {
                            $message = "Error Deleting Dealer. ";
                            if ($dealerDeleted == 0)
                            {
                                $message .= "Dealer row did not exist.";
                            }
                            if ($reportSettingDeleted == 0)
                            {
                                $message .= "Report setting row did not exist.";
                            }
                            if ($quoteSettingDeleted == 0)
                            {
                                $message .= "Quote setting row did not exist.";
                            }
                            throw new Exception($message);
                        }

                        $db->commit();

                        // We have successfully deleted it then redirect and display message
                        $this->_helper->flashMessenger(array("success" => "Successfully deleted {$dealer->dealerName}."));
                        $this->redirector("index");
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        My_Log::logException($e);
                        $this->_helper->flashMessenger(array("danger" => "Error deleting {$dealer->dealerName}, please try again."));
                    }
                }
            }
        }
        $this->view->form = $form;
    }
}