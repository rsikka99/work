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
            $this->_flashMessenger->addMessage(array('warning' => 'You must select a dealer to edit.'));
            $this->redirect('index');
        }

        /**
         * Fetch the dealer
         */
        $dealerMapper = Admin_Model_Mapper_Dealer::getInstance();
        $dealer       = $dealerMapper->find($dealerId);

        if (!$dealer instanceof Admin_Model_Dealer)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Invalid dealer selected.'));
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
                        $this->_processDealerLogoImage($form, $dealer);

                        // Save dealer object
                        $dealer->populate($form->getValues());
                        $dealerMapper->save($dealer);

                        $db->commit();

                        // All done
                        $this->_flashMessenger->addMessage(array('success' => "{$dealer->dealerName} has been successfully updated!"));
                        $this->redirector("index");
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        $this->_flashMessenger->addMessage(array('danger' => "Error saving dealer to database.  If problem persists please contact your system administrator."));
                        My_Log::logException($e);
                    }
                }
            }
        }

        if ($dealer->dealerLogoImageId > 0)
        {
            $this->view->dealerLogoImagePath = $dealer->getDealerLogoImageFile();
        }
        $this->view->form = $form;
    }

    /**
     * @param Admin_Form_Dealer  $form
     * @param Admin_Model_Dealer $dealer
     */
    protected function _processDealerLogoImage ($form, &$dealer)
    {
        $dealerLogoImage = $form->getDealerLogoImage();
        if ($dealerLogoImage->isUploaded())
        {
            $dealerLogoImage->setDestination(DATA_PATH . '/uploads/');
            if ($dealerLogoImage->receive())
            {
                $uploadedImagePath     = $dealerLogoImage->getFileName();
                $uploadedImageFileName = $dealerLogoImage->getFileName(null, false);

                $data = getimagesize($uploadedImagePath);


                $uploadedImage = null;


                switch ($data['mime'])
                {
                    case "image/gif" :
                        $uploadedImage = imagecreatefromgif($uploadedImagePath);
                        break;
                    case "image/pjpeg" :
                    case "image/jpeg" :
                    case "image/jpg" :
                        $uploadedImage = imagecreatefromjpeg($uploadedImagePath);
                        break;
                    case "image/png" :
                    case "image/x-png" :
                        $uploadedImage = imagecreatefrompng($uploadedImagePath);
                }

                if ($uploadedImage !== null)
                {
                    imagepng($uploadedImage, $uploadedImagePath);
                    $base64ImageString = chunk_split(base64_encode(file_get_contents($uploadedImagePath)));
                    $data              = $base64ImageString;

                    /**
                     * Insert the image into the database
                     */
                    $image           = new Admin_Model_Image();
                    $image->filename = $uploadedImageFileName;
                    $image->image    = $base64ImageString;
                    $imageId         = Admin_Model_Mapper_Image::getInstance()->insert($image);
                    if ($imageId)
                    {
                        if ($dealer->dealerLogoImageId > 0)
                        {
                            Admin_Model_Mapper_Image::getInstance()->delete($dealer->dealerLogoImageId);
                        }
                        $dealer->dealerLogoImageId = $imageId;
                    }
                }
            }
        }
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

                        $this->_processDealerLogoImage($form, $dealer);

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
                            $this->_flashMessenger->addMessage(array('success' => "Dealer {$dealer->dealerName} successfully created"));
                            $this->redirector('index');
                        }
                        else
                        {
                            $this->_flashMessenger->addMessage(array('danger' => "Error saving dealer to database. If problem persists please contact your system administrator."));
                        }
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        $this->_flashMessenger->addMessage(array('danger' => "Error saving dealer to database. If problem persists please contact your system administrator."));
                        My_Log::logException($e);
                    }
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'Errors on form, please correct and try again'));
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

        if ($dealerId == 1)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'You cannot delete the root dealer company.'));
            $this->redirect('index');
        }

        if (!$dealer instanceof Admin_Model_Dealer)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Invalid dealer selected.'));
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
                        $dealerReportSetting = Proposalgen_Model_Mapper_Dealer_Report_Setting::getInstance()->find($dealer->id);
                        if ($dealerReportSetting instanceof Proposalgen_Model_Dealer_Report_Setting)
                        {
                            Proposalgen_Model_Mapper_Report_Setting::getInstance()->delete($dealerReportSetting->reportSettingId);
                        }

                        $dealerQuoteSetting = Quotegen_Model_Mapper_DealerQuoteSetting::getInstance()->find($dealer->id);
                        if ($dealerQuoteSetting instanceof Quotegen_Model_DealerQuoteSetting)
                        {
                            Quotegen_Model_Mapper_QuoteSetting::getInstance()->delete($dealerQuoteSetting->quoteSettingId);
                        }

                        Admin_Model_Mapper_Dealer::getInstance()->delete($dealer);

                        $db->commit();

                        // We have successfully deleted it then redirect and display message
                        $this->_flashMessenger->addMessage(array("success" => "Successfully deleted {$dealer->dealerName}."));
                        $this->redirector("index");
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        My_Log::logException($e);
                        $this->_flashMessenger->addMessage(array("danger" => "Error deleting {$dealer->dealerName}, please try again."));
                    }
                }
            }
        }
        $this->view->form = $form;
    }
}