<?php
use MPSToolbox\Legacy\Forms\DeleteConfirmationForm;
use MPSToolbox\Legacy\Mappers\DealerFeatureMapper;
use MPSToolbox\Legacy\Mappers\DealerMapper;
use MPSToolbox\Legacy\Models\DealerFeatureModel;
use MPSToolbox\Legacy\Models\DealerModel;
use MPSToolbox\Legacy\Modules\Admin\Forms\DealerRmsProvidersForm;
use MPSToolbox\Legacy\Modules\Admin\Forms\DealerTonerVendorsForm;
use MPSToolbox\Legacy\Modules\Admin\Forms\DealerForm;
use MPSToolbox\Legacy\Modules\Admin\Mappers\ImageMapper;
use MPSToolbox\Legacy\Modules\Admin\Models\ImageModel;
use MPSToolbox\Legacy\Modules\Admin\Services\DealerRmsProvidersService;
use MPSToolbox\Legacy\Modules\Admin\Services\DealerTonerVendorsService;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonDefaultMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonDefaultModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonModel;
use MPSToolbox\Legacy\Modules\Preferences\Mappers\DealerSettingMapper;
use MPSToolbox\Legacy\Modules\Preferences\Models\DealerSettingModel;
use Tangent\Controller\Action;

/**
 * Class Admin_DealerController
 */
class Admin_DealerController extends Action
{
    /**
     * This action lists all the dealers in the system
     */
    public function indexAction ()
    {
        $this->_pageTitle = array('System', 'Dealers', 'Dealer Management');
        $dealerMapper     = DealerMapper::getInstance();
        $paginator        = new Zend_Paginator(new My_Paginator_MapperAdapter($dealerMapper));

        // Gets the current page for the passed parameter
        $paginator->setCurrentPageNumber($this->getRequest()->getUserParam('page', 1));

        // Sets the amount of dealers that we are showing per page.
        $paginator->setItemCountPerPage(15);

        $this->view->paginator = $paginator;
    }

    /**
     * Allows a user to view a dealership
     */
    public function viewAction ()
    {

        $dealerId = $this->getRequest()->getUserParam('id', false);

        if ($dealerId === false)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'You must select a dealer to edit.'));
            $this->redirectToRoute('admin.dealers');
        }

        $dealerMapper = DealerMapper::getInstance();
        $dealer       = $dealerMapper->find($dealerId);
        if (!$dealer instanceof DealerModel)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Invalid dealer selected.'));
            $this->redirectToRoute('admin.dealers');
        }

        $this->_pageTitle = array('Dealers', 'System');

        $this->view->dealer = $dealer;
    }

    /**
     * Gets a dealer. Will redirect the user
     *
     * @param $dealerId
     *
     * @return DealerModel
     */
    public function getDealer ($dealerId)
    {
        if ($dealerId === false)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'You must select a dealer to edit.'));
            $this->redirectToRoute('admin.dealers');
        }

        /**
         * Fetch the dealer
         */
        $dealerMapper = DealerMapper::getInstance();
        $dealer       = $dealerMapper->find($dealerId);

        if (!$dealer instanceof DealerModel)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Invalid dealer selected.'));
            $this->redirectToRoute('admin.dealers');
        }

        return $dealer;
    }

    /**
     * This action is used to edit a dealer
     */
    public function editAction ()
    {
        $dealerFeatureMapper = DealerFeatureMapper::getInstance();
        $dealerMapper        = DealerMapper::getInstance();
        $dealerId            = $this->getRequest()->getUserParam('id', false);
        $dealer              = $this->getDealer($dealerId);

        $this->_pageTitle = array('Edit: ' . $dealer->dealerName, 'Dealers', 'System');


        $featureList = $dealerFeatureMapper->fetchFeatureListForDealer($dealerId);
        $features    = array();
        foreach ($featureList as $feature)
        {
            $features['dealerFeatures'][] = $feature->featureId;
        }

        $form = new DealerForm();
        $form->populate($dealer->toArray());
        $form->populate($features);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData ['cancel']))
            {
                $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
            }
            else
            {
                if ($form->isValid($postData))
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    try
                    {
                        $db->beginTransaction();
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
                                $dealerFeature            = new DealerFeatureModel();
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
                        $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        $this->_flashMessenger->addMessage(array('danger' => "Error saving dealer to database.  If problem persists please contact your system administrator."));
                        \Tangent\Logger\Logger::logException($e);
                    }
                }
            }
        }

        if ($dealer->dealerLogoImageId > 0)
        {
            $this->view->dealerLogoImagePath = $dealer->getDealerLogoImageFile(true);
        }
        $this->view->form = $form;
    }

    /**
     * Edits toner vendors for a given dealership
     */
    public function editTonerVendorsAction ()
    {
        $dealerId                 = $this->getRequest()->getUserParam('id', false);
        $dealer                   = $this->getDealer($dealerId);
        $dealerTonerVendorService = new DealerTonerVendorsService();
        $form                     = new DealerTonerVendorsForm();

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['cancel']))
            {
                $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
            }
            else
            {
                if ($form->isValid($postData))
                {
                    $formValues = $form->getValues($postData);

                    $dealerTonerVendorService->updateDealerTonerVendors($dealerId, $formValues);

                    $this->_flashMessenger->addMessage(array('success' => "Dealer Toner Vendors successfully updated"));
                    $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
                }
            }
        }
        else
        {
            $form->populate(array('manufacturerIds' => $dealerTonerVendorService->getDealerTonerVendorsAsArray($dealerId)));
        }

        $this->_pageTitle = array('Edit Toner Vendors for: ' . $dealer->dealerName, 'Dealers', 'System');

        $this->view->form = $form;
    }

    /**
     * Edits RMS Providers for a given dealership
     */
    public function editRmsProvidersAction ()
    {
        $dealerId                 = $this->getRequest()->getUserParam('id', false);
        $dealer                   = $this->getDealer($dealerId);
        $dealerRmsProviderService = new DealerRmsProvidersService();
        $form                     = new DealerRmsProvidersForm();

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['cancel']))
            {
                $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
            }
            else
            {
                if ($form->isValid($postData))
                {
                    $formValues = $form->getValues($postData);

                    $dealerRmsProviderService->updateDealerRmsProviders($dealerId, $formValues);

                    $this->_flashMessenger->addMessage(array('success' => "Dealer RMS Providers successfully updated"));
                    $this->redirectToRoute('admin.dealers.view', array('id' => $dealerId));
                }
            }
        }
        else
        {
            $form->populate(array('rmsProviderIds' => $dealerRmsProviderService->getDealerRmsProvidersAsArray($dealerId)));
        }

        $this->_pageTitle = array('Edit RMS Providers for: ' . $dealer->dealerName, 'Dealers', 'System');

        $this->view->form = $form;
    }

    /**
     * @param DealerForm  $form
     * @param DealerModel $dealer
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
                        imageAlphaBlending($uploadedImage, true);
                        imageSaveAlpha($uploadedImage, true);
                }

                if ($uploadedImage !== null)
                {
                    ob_start();
                    imagepng($uploadedImage, null, 9, PNG_ALL_FILTERS);
                    $base64ImageString = chunk_split(base64_encode(ob_get_contents()));
//                    $base64ImageString =  chunk_split(base64_encode(file_get_contents($uploadedImagePath)));
                    ob_end_clean();

                    /**
                     * Insert the image into the database
                     */
                    $image           = new ImageModel();
                    $image->filename = $uploadedImageFileName;
                    $image->image    = $base64ImageString;
                    $imageId         = ImageMapper::getInstance()->insert($image);
                    if ($imageId)
                    {
                        if ($dealer->dealerLogoImageId > 0)
                        {
                            ImageMapper::getInstance()->delete($dealer->dealerLogoImageId);
                        }
                        $dealer->dealerLogoImageId = $imageId;

                        $dealer->getDealerLogoImageFile(true);
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
        $this->_pageTitle = array('System', 'Dealers', 'Create Dealer');
        $form             = new DealerForm();

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
                        $dealer = new DealerModel();

                        $this->_processDealerLogoImage($form, $dealer);

                        $dealer->populate($values);
                        $dealer->dateCreated = date("Y-m-d");

                        // Save the dealer with the id to the database
                        $dealerId = DealerMapper::getInstance()->insert($dealer);

                        // Copy the systems default reason for the dealers system default reason
                        foreach (DeviceSwapReasonMapper::getInstance()->fetchAllReasonByDealerId(1) as $deviceSwapReason)
                        {
                            $newDeviceSwapReason           = new DeviceSwapReasonModel($deviceSwapReason->toArray());
                            $newDeviceSwapReason->dealerId = $dealerId;
                            // Insert thew row into the database
                            $newDeviceSwapReason->id = DeviceSwapReasonMapper::getInstance()->insert($newDeviceSwapReason);

                            $defaultReason = DeviceSwapReasonDefaultMapper::getInstance()->findDefaultByDealerId($newDeviceSwapReason->deviceSwapReasonCategoryId, 1);

                            if ($defaultReason instanceof DeviceSwapReasonDefaultModel && $deviceSwapReason->id === $defaultReason->deviceSwapReasonId)
                            {
                                $newDefaultReason                             = new DeviceSwapReasonDefaultModel();
                                $newDefaultReason->dealerId                   = $dealerId;
                                $newDefaultReason->deviceSwapReasonId         = $newDeviceSwapReason->id;
                                $newDefaultReason->deviceSwapReasonCategoryId = $newDeviceSwapReason->deviceSwapReasonCategoryId;

                                DeviceSwapReasonDefaultMapper::getInstance()->insert($newDefaultReason);
                            }
                        }

                        $dealerFeatureMapper = DealerFeatureMapper::getInstance();

                        // Loop through our new features
                        foreach ($values ["dealerFeatures"] as $featureId)
                        {
                            $dealerFeature            = new DealerFeatureModel();
                            $dealerFeature->featureId = $featureId;
                            $dealerFeature->dealerId  = $dealerId;
                            $dealerFeatureMapper->insert($dealerFeature);
                        }

                        $db->commit();

                        if ($dealerId)
                        {
                            $this->_flashMessenger->addMessage(array('success' => "Dealer {$dealer->dealerName} successfully created"));
                            $this->redirectToRoute('admin.dealers');
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
                        \Tangent\Logger\Logger::logException($e);
                    }
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'Errors on form, please correct and try again'));
                }
            }
            else
            {
                $this->redirectToRoute('admin.dealers');
            }
        }

        $this->view->form = $form;
    }

    /**
     * This action deletes a dealer
     */
    public function deleteAction ()
    {
        $this->_pageTitle = array('System', 'Dealers', 'Delete Dealer');
        $dealerId         = $this->getRequest()->getUserParam('id');
        $dealer           = DealerMapper::getInstance()->find($dealerId);

        if ($dealerId == 1)
        {
            $this->_flashMessenger->addMessage(array('danger' => 'You cannot delete the root dealer company.'));
            $this->redirect('admin.dealers');
        }

        if (!$dealer instanceof DealerModel)
        {
            $this->_flashMessenger->addMessage(array('warning' => 'Invalid dealer selected.'));
            $this->redirect('admin.dealers');
        }

        $message = "Are you sure you want to delete {$dealer->dealerName}";
        $form    = new DeleteConfirmationForm($message);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if ($form->isValid($postData))
            {
                if (isset($postData ['cancel']))
                {
                    $this->redirectToRoute('admin.dealers');
                }
                else
                {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    try
                    {
                        $db->beginTransaction();

                        // Delete the dealer and the related report setting
                        $reportSetting = DealerSettingMapper::getInstance()->find($dealer->id);
                        if ($reportSetting instanceof DealerSettingModel)
                        {
                            DealerSettingMapper::getInstance()->delete($dealer);
                        }

                        DeviceSwapReasonDefaultMapper::getInstance()->deleteDefaultReasonByDealerId($dealer->id);
                        DeviceSwapReasonMapper::getInstance()->deleteReasonsByDealerId($dealer->id);
                        DealerMapper::getInstance()->delete($dealer);

                        $db->commit();

                        // We have successfully deleted it then redirect and display message
                        $this->_flashMessenger->addMessage(array("success" => "Successfully deleted {$dealer->dealerName}."));
                        $this->redirectToRoute('admin.dealers');
                    }
                    catch (Exception $e)
                    {
                        $db->rollBack();
                        \Tangent\Logger\Logger::logException($e);
                        $this->_flashMessenger->addMessage(array("danger" => "Error deleting {$dealer->dealerName}, please try again."));
                    }
                }
            }
        }
        $this->view->form = $form;
    }
}