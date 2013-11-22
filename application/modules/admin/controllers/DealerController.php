<?php

/**
 * Class Admin_DealerController
 */
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
        $dealerMapper        = Admin_Model_Mapper_Dealer::getInstance();
        $dealerFeatureMapper = Application_Model_Mapper_Dealer_Feature::getInstance();
        $dealer              = $dealerMapper->find($dealerId);

        if (!$dealer instanceof Admin_Model_Dealer)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Invalid dealer selected.'));
            $this->redirect('index');
        }
        $featureList = $dealerFeatureMapper->fetchFeatureListForDealer($dealerId);
        $features    = array();
        foreach ($featureList as $feature)
        {
            $features['dealerFeatures'][] = $feature->featureId;
        }

        $form = new Admin_Form_Dealer();
        $form->populate($dealer->toArray());
        $form->populate($features);

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

                        $dealerFeatureList = $dealerFeatureMapper->fetchFeatureListForDealer($dealerId);

                        // Loop through our new features
                        foreach ($postData ["dealerFeatures"] as $featureId)
                        {
                            $hasFeature = false;
                            foreach ($dealerFeatureList as $existingFeature)
                            {
                                if ($existingFeature->featureId == $featureId)
                                {
                                    $hasFeature = true;
                                    break;
                                }
                            }

                            // If the feature is new
                            if (!$hasFeature)
                            {
                                $dealerFeature            = new Application_Model_Dealer_Feature();
                                $dealerFeature->featureId = $featureId;
                                $dealerFeature->dealerId  = $dealerId;
                                $dealerFeatureMapper->insert($dealerFeature);
                            }
                        }

                        // Loop through all of our old features and delete ones that we no longer have
                        foreach ($dealerFeatureList as $existingFeature)
                        {
                            $stillHasFeature = false;
                            foreach ($postData["dealerFeatures"] as $featureId)
                            {
                                if ($existingFeature->featureId == $featureId)
                                {
                                    $stillHasFeature = true;
                                    break;
                                }
                            }

                            if (!$stillHasFeature)
                            {
                                $dealerFeatureMapper->delete($existingFeature);
                            }
                        }

                        $db->commit();

                        // All done
                        $this->_flashMessenger->addMessage(array('success' => "{$dealer->dealerName} has been successfully updated!"));
                        $this->redirector("index");
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        $this->_flashMessenger->addMessage(array('danger' => "Error saving dealer to database.  If problem persists please contact your system administrator."));
                        Tangent_Log::logException($e);
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

                        $newDeviceSwapReasonArray = array();
                        // Copy the systems default reason for the dealers system default reason
                        foreach (Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->fetchAllReasonByDealerId(1) as $deviceSwapReason)
                        {
                            $newDeviceSwapReason           = new Hardwareoptimization_Model_Device_Swap_Reason($deviceSwapReason->toArray());
                            $newDeviceSwapReason->dealerId = $dealerId;
                            // Insert thew row into the database
                            $newDeviceSwapReason->id = Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->insert($newDeviceSwapReason);

                            $defaultReason = Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->findDefaultByDealerId($newDeviceSwapReason->deviceSwapReasonCategoryId, 1);

                            if ($defaultReason instanceof Hardwareoptimization_Model_Device_Swap_Reason_Default && $deviceSwapReason->id === $defaultReason->deviceSwapReasonId)
                            {
                                $newDefaultReason                             = new Hardwareoptimization_Model_Device_Swap_Reason_Default();
                                $newDefaultReason->dealerId                   = $dealerId;
                                $newDefaultReason->deviceSwapReasonId         = $newDeviceSwapReason->id;
                                $newDefaultReason->deviceSwapReasonCategoryId = $newDeviceSwapReason->deviceSwapReasonCategoryId;

                                Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->insert($newDefaultReason);
                            }
                        }

                        $dealerFeatureMapper = Application_Model_Mapper_Dealer_Feature::getInstance();

                        // Loop through our new features
                        foreach ($values ["dealerFeatures"] as $featureId)
                        {
                            $dealerFeature            = new Application_Model_Dealer_Feature();
                            $dealerFeature->featureId = $featureId;
                            $dealerFeature->dealerId  = $dealerId;
                            $dealerFeatureMapper->insert($dealerFeature);
                        }

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
                        Tangent_Log::logException($e);
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
                        $reportSetting = Preferences_Model_Mapper_Dealer_Setting::getInstance()->find($dealer->id);
                        if ($reportSetting instanceof Preferences_Model_Dealer_Setting)
                        {
                            Preferences_Model_Mapper_Dealer_Setting::getInstance()->delete($dealer);
                        }

                        Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->deleteDefaultReasonByDealerId($dealer->id);
                        Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance()->deleteReasonsByDealerId($dealer->id);
                        Admin_Model_Mapper_Dealer::getInstance()->delete($dealer);

                        $db->commit();

                        // We have successfully deleted it then redirect and display message
                        $this->_flashMessenger->addMessage(array("success" => "Successfully deleted {$dealer->dealerName}."));
                        $this->redirector("index");
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        Tangent_Log::logException($e);
                        $this->_flashMessenger->addMessage(array("danger" => "Error deleting {$dealer->dealerName}, please try again."));
                    }
                }
            }
        }
        $this->view->form = $form;
    }
}