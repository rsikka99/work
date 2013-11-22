<?php
/**
 * Class Hardwareoptimization_IndexController
 */
class Hardwareoptimization_IndexController extends Hardwareoptimization_Library_Controller_Action
{
    /**
     * @var Proposalgen_Form_DeviceSwap
     */
    protected $_deviceSwapForm;
    /**
     * @var Hardwareoptimization_ViewModel_Devices
     */
    protected $_deviceViewModel;


    /**
     * This action will redirect us to the latest available step
     */
    public function indexAction ()
    {
        $this->redirectToLatestStep($this->getHardwareOptimization()->stepName);
    }

    /**
     * Handles selecting an rms upload
     */
    public function selectUploadAction ()
    {
        $this->_navigation->setActiveStep(Hardwareoptimization_Model_Hardware_Optimization_Steps::STEP_FLEET_UPLOAD);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (isset($postData['selectRmsUploadId']))
            {
                $selectRmsUploadService = new Proposalgen_Service_SelectRmsUpload($this->_mpsSession->selectedClientId);
                $rmsUpload              = $selectRmsUploadService->validateRmsUploadId($postData['selectRmsUploadId']);
                if ($rmsUpload instanceof Proposalgen_Model_Rms_Upload)
                {
                    $this->getHardwareOptimization()->rmsUploadId = $rmsUpload->id;
                    $this->updateStepName();
                    $this->saveHardwareOptimization();
                    $this->gotoNextNavigationStep($this->_navigation);
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'The Upload you selected is not valid.'));
                }
            }
            else if (isset($postData['noUploads']))
            {
                $this->redirector('index', 'fleet', 'proposalgen');
            }
            if ($this->getHardwareOptimization()->rmsUploadId > 0)
            {
                if (isset($postData['saveAndContinue']))
                {
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
        }
        $this->view->numberOfUploads = count(Proposalgen_Model_Mapper_Rms_Upload::getInstance()->fetchAllForClient($this->getHardwareOptimization()->clientId));
        $this->view->rmsUpload       = $this->getHardwareOptimization()->getRmsUpload();
        $this->view->navigationForm  = new Hardwareoptimization_Form_Hardware_Optimization_Navigation(Hardwareoptimization_Form_Hardware_Optimization_Navigation::BUTTONS_NEXT);
    }

    public function settingsAction ()
    {
        $this->_navigation->setActiveStep(Hardwareoptimization_Model_Hardware_Optimization_Steps::STEP_SETTINGS);

        $user = Application_Model_Mapper_User::getInstance()->find($this->_identity->id);

        $defaultHardwareOptimizationSettings = clone $user->getDealer()->getDealerSettings()->getHardwareOptimizationSettings();
        $defaultHardwareOptimizationSettings->populate($user->getUserSettings()->getHardwareOptimizationSettings()->toArray());
        $hardwareOptimizationService = new Hardwareoptimization_Service_Setting($this->_hardwareOptimization->getHardwareOptimizationSetting(), $defaultHardwareOptimizationSettings, $this->_hardwareOptimization->id);

        $form = $hardwareOptimizationService->getFormWithDefaults();

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (!isset($postData['goBack']))
            {
                // Save
                $this->saveHardwareOptimization();
                $hardwareOptimizationService->update($postData, $defaultHardwareOptimizationSettings->toArray());

                if (isset($postData['saveAndContinue']))
                {
                    $this->updateStepName();
                    $this->saveHardwareOptimization();
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
            else
            {
                $this->gotoPreviousNavigationStep($this->_navigation);
            }
        }
        $this->view->form = $form;
    }

    public function optimizeAction ()
    {
        // Mark the step we're on as active
        $this->_navigation->setActiveStep(Hardwareoptimization_Model_Hardware_Optimization_Steps::STEP_OPTIMIZE);

        // $form = $this->getDeviceSwapForm($this->getDeviceViewModel()->purchasedDeviceInstances->getDeviceInstances());
        $form = new Hardwareoptimization_Form_OptimizeActions();

        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();

            if ($form->isValid($postData))
            {
                if ($form->getValue('Submit') || isset($postData["saveAndContinue"]))
                {
                    if (isset($postData["saveAndContinue"]))
                    {
                        $this->updateStepName();
                        $this->saveHardwareOptimization();
                        $this->gotoNextNavigationStep($this->_navigation);
                    }
                }
                else if ($form->getValue('Analyze'))
                {
                    // Analyze the fleet. If it is successful we need to rebuild our form
                    if ($this->_analyzeFleet())
                    {
                        $this->_flashMessenger->addMessage(array('success' => "We've optimized your fleet. Please review the changes before proceeding."));
                        $this->redirector('optimize', null, null, array());
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array('danger' => "There was an error saving your replacement choices."));
                    }
                }
                else if ($form->getValue('MemjetAnalyze'))
                {
                    // Analyze the fleet. If it is successful we need to rebuild our form
                    if (My_Feature::canAccess(My_Feature::MEMJET_OPTIMIZATION) && $this->_memjetAnalyzeFleet())
                    {
                        $this->_flashMessenger->addMessage(array('success' => "We've optimized your fleet. Please review the changes before proceeding."));
                        $this->redirector('optimize', null, null, array());
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array('danger' => "There was an error saving your replacement choices."));
                    }
                }
                else if ($form->getValue('ResetReplacements'))
                {
                    if ($this->_resetReplacements())
                    {
                        $this->_flashMessenger->addMessage(array('success' => "Device replacements have been reset."));
                        $this->redirector('optimize', null, null, array());
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array('danger' => "There was an error resetting your replacement choices."));
                    }
                }
                else if ($form->getValues('Cancel'))
                {

                    $this->gotoPreviousNavigationStep($this->_navigation);
                }
            }
        }

        $this->view->form                 = $form;
        $this->view->hardwareOptimization = $this->_hardwareOptimization;
        $this->view->navigationForm       = new Proposalgen_Form_Assessment_Navigation(Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT);
    }

    /**
     * Analyzes the customers fleet and uses a threshold to determine which devices to automatically replace
     */
    protected function _analyzeFleet ()
    {
        $db                              = Zend_Db_Table::getDefaultAdapter();
        $optimization                    = $this->getOptimizationViewModel();
        $deviceInstanceReplacementMapper = Proposalgen_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance();

        try
        {
            $db->beginTransaction();

            // Delete all our replacements
            if (!$this->_resetReplacements())
            {
                throw new Exception("Error resetting replacements!");
            }

            $blackReplacementDevices    = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getBlackReplacementDevices($this->_dealerId, false);
            $blackMfpReplacementDevices = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getBlackMfpReplacementDevices($this->_dealerId, false);
            $colorReplacementDevices    = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getColorReplacementDevices($this->_dealerId, false);
            $colorMfpReplacementDevices = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->getColorMfpReplacementDevices($this->_dealerId, false);

            /* @var $replacementMasterDevice Hardwareoptimization_Model_Device_Swap */
            foreach (array_merge($blackReplacementDevices, $blackMfpReplacementDevices, $colorReplacementDevices, $colorMfpReplacementDevices) as $replacementMasterDevice)
            {
                $replacementMasterDevice->getMasterDevice()->processOverrides($this->_hardwareOptimization->getHardwareOptimizationSetting()->adminCostPerPage);
            }

            $standardDeviceReplacement = new Hardwareoptimization_Model_Optimization_StandardDeviceReplacement(
                array(
                     'black'    => $blackReplacementDevices,
                     'blackmfp' => $blackMfpReplacementDevices,
                     'color'    => $colorReplacementDevices,
                     'colormfp' => $colorMfpReplacementDevices
                ),
                $this->_dealerId,
                $this->_hardwareOptimization->getHardwareOptimizationSetting()->costThreshold,
                $optimization->getCostPerPageSettingForDealer(),
                $optimization->getCostPerPageSettingForReplacements(),
                $this->_hardwareOptimization->getHardwareOptimizationSetting()->laborCostPerPage,
                $this->_hardwareOptimization->getHardwareOptimizationSetting()->partsCostPerPage
            );

            foreach ($optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $suggestedDevice = $standardDeviceReplacement->findReplacement($deviceInstance);

                if ($suggestedDevice instanceof Proposalgen_Model_MasterDevice)
                {
                    $newDevice                         = new Proposalgen_Model_Device_Instance_Replacement_Master_Device();
                    $newDevice->masterDeviceId         = $suggestedDevice->id;
                    $newDevice->deviceInstanceId       = $deviceInstance->id;
                    $newDevice->hardwareOptimizationId = $this->_hardwareOptimization->id;

                    $deviceInstanceReplacementMapper->insert($newDevice);
                }
            }

            if (!$this->_saveDeviceSwapReason(true))
            {
                return false;
            }

            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
            Tangent_Log::logException($e);

            return false;
        }

        return true;
    }

    /**
     * Resets all the replacements in a fleet
     *
     * @return boolean True if everything goes according to plan
     */
    protected function _resetReplacements ()
    {
        try
        {
            $deviceInstanceReplacementMapper = Proposalgen_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance();
            $deviceInstanceReplacementMapper->deleteAllDeviceInstanceReplacementsByHardwareOptimizationId($this->_hardwareOptimization->id);
        }
        catch (Exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     * Returns Json based on id that has been passed via query string
     */
    public function getDeviceByDeviceInstanceIdAction ()
    {
        Proposalgen_Model_MasterDevice::$ReportLaborCostPerPage = $this->_hardwareOptimization->getHardwareOptimizationSetting()->laborCostPerPage;
        Proposalgen_Model_MasterDevice::$ReportPartsCostPerPage = $this->_hardwareOptimization->getHardwareOptimizationSetting()->partsCostPerPage;

        $optimization                  = $this->getOptimizationViewModel();
        $costPerPageSetting            = $optimization->getCostPerPageSettingForDealer();
        $replacementCostPerPageSetting = $optimization->getCostPerPageSettingForReplacements();

        $instanceId     = $this->_getParam('deviceInstanceId');
        $deviceInstance = null;
        $deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($instanceId);
        $deviceInstance->processOverrides($this->_hardwareOptimization->getHardwareOptimizationSetting()->adminCostPerPage);

        $replacementDevice    = $deviceInstance->getReplacementMasterDeviceForHardwareOptimization($this->_hardwareOptimization->id);
        $hasReplacementDevice = ($replacementDevice instanceof Proposalgen_Model_MasterDevice);

        $device = array(
            "deviceInstance" => array(
                "deviceName"            => "{$deviceInstance->getMasterDevice()->getManufacturer()->fullname} {$deviceInstance->getMasterDevice()->modelName}",
                "ipAddress"             => $deviceInstance->ipAddress,
                "isColor"               => (int)$deviceInstance->getMasterDevice()->isColor(),
                "serialNumber"          => $deviceInstance->serialNumber,
                "lifePageCount"         => number_format($deviceInstance->getLifePageCount()),
                "monoAmpv"              => number_format($deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly()),
                "colorAmpv"             => number_format($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly()),
                "costPerPageMonochrome" => $this->view->currency((float)$deviceInstance->calculateCostPerPage($costPerPageSetting)->monochromeCostPerPage, array("precision" => 4)),
                "costPerPageColor"      => $this->view->currency((float)$deviceInstance->calculateCostPerPage($costPerPageSetting)->colorCostPerPage, array("precision" => 4)),
                "jitSuppliesSupported"  => (int)$deviceInstance->reportsTonerLevels,
                "isCopy"                => (int)$deviceInstance->getMasterDevice()->isCopier,
                "isFax"                 => (int)$deviceInstance->getMasterDevice()->isFax,
                "ppmBlack"              => ($deviceInstance->getMasterDevice()->ppmBlack > 0) ? number_format($deviceInstance->getMasterDevice()->ppmBlack) : 'N/A',
                "ppmColor"              => ($deviceInstance->getMasterDevice()->ppmColor > 0) ? number_format($deviceInstance->getMasterDevice()->ppmColor) : 'N/A'
            ),
            "hasReplacement" => (int)$hasReplacementDevice
        );


        if ($hasReplacementDevice)
        {
            $device ["replacementDevice"] = array(
                "deviceName"            => "{$replacementDevice->getManufacturer()->fullname} {$replacementDevice->modelName}",
                "isColor"               => (int)$replacementDevice->isColor(),
                "costPerPageMonochrome" => $this->view->currency((float)$deviceInstance->calculateCostPerPage($replacementCostPerPageSetting, $replacementDevice)->monochromeCostPerPage, array("precision" => 4)),
                "costPerPageColor"      => $this->view->currency((float)$deviceInstance->calculateCostPerPage($replacementCostPerPageSetting, $replacementDevice)->colorCostPerPage, array("precision" => 4)),
                "isCopy"                => (int)$replacementDevice->isCopier,
                "isFax"                 => (int)$replacementDevice->isFax,
                "ppmBlack"              => ($replacementDevice->ppmBlack > 0) ? number_format($replacementDevice->ppmBlack) : 'N/A',
                "ppmColor"              => ($replacementDevice->ppmColor > 0) ? number_format($replacementDevice->ppmColor) : 'N/A',
                "reason"                => $deviceInstance->getReason($this->_hardwareOptimization->id)
            );
        }

        $this->sendJson($device);
    }

    public function updateReplacementDeviceAction ()
    {
        // Setup the required mappers
        $deviceInstanceReplacementMasterDeviceMapper = Proposalgen_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance();
        $deviceInstanceDeviceSwapReasonMapper        = Hardwareoptimization_Model_Mapper_Device_Instance_Device_Swap_Reason::getInstance();
        $optimization                                = $this->getOptimizationViewModel();
        $deviceInstanceReasonElement                 = null;

        // Setup the master device labor and parts cost per page
        Proposalgen_Model_MasterDevice::$ReportLaborCostPerPage = $this->_hardwareOptimization->getHardwareOptimizationSetting()->laborCostPerPage;
        Proposalgen_Model_MasterDevice::$ReportPartsCostPerPage = $this->_hardwareOptimization->getHardwareOptimizationSetting()->partsCostPerPage;

        // Get and see if we have a device instance id passed, if not send a json error
        $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
        if ($deviceInstanceId === false)
        {
            $this->sendJsonError("Invalid data passed.");
        }

        $deviceInstanceId = (int)str_replace("deviceInstance_", "", $deviceInstanceId);
        $deviceInstance   = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceInstanceId);

        // Check if device belongs to rms
        if (!$deviceInstance instanceof Proposalgen_Model_DeviceInstance || $this->_hardwareOptimization->rmsUploadId !== $deviceInstance->rmsUploadId)
        {
            $this->sendJsonError("You do not have permission to edit this device instance.");
        }

        $replacementDeviceId = (int)$this->_getParam("replacementDeviceId");
        $whereKey            = array($deviceInstanceId, $this->_hardwareOptimization->id);

        if ($replacementDeviceId == 0)
        {
            // Delete the row from the database
            $deviceInstanceReplacementMasterDeviceMapper->delete($whereKey);
            $costDelta = 0;
        }
        else
        {
            $deviceInstanceReplacementMasterDevice = $deviceInstanceReplacementMasterDeviceMapper->find($whereKey);
            if ($deviceInstanceReplacementMasterDevice instanceof Proposalgen_Model_Device_Instance_Replacement_Master_Device)
            {
                // Update the device information
                $deviceInstanceReplacementMasterDevice->masterDeviceId = $replacementDeviceId;
                $deviceInstanceReplacementMasterDeviceMapper->save($deviceInstanceReplacementMasterDevice);
            }
            else
            {
                // Insert the device into the table
                $deviceInstanceReplacementMasterDevice                         = new Proposalgen_Model_Device_Instance_Replacement_Master_Device();
                $deviceInstanceReplacementMasterDevice->deviceInstanceId       = $deviceInstanceId;
                $deviceInstanceReplacementMasterDevice->masterDeviceId         = $replacementDeviceId;
                $deviceInstanceReplacementMasterDevice->hardwareOptimizationId = $this->_hardwareOptimization->id;
                $deviceInstanceReplacementMasterDeviceMapper->insert($deviceInstanceReplacementMasterDevice);
            }
            $costDelta = $deviceInstance->calculateMonthlyCost($optimization->getCostPerPageSettingForDealer()) -
                         $deviceInstance->calculateMonthlyCost($optimization->getCostPerPageSettingForReplacements(), Proposalgen_Model_Mapper_MasterDevice::getInstance()->findForReports($replacementDeviceId, $this->_identity->dealerId, $this->_hardwareOptimization->getHardwareOptimizationSetting()->laborCostPerPage, $this->_hardwareOptimization->getHardwareOptimizationSetting()->partsCostPerPage));

            $form                        = new Proposalgen_Form_DeviceSwapChoice(array($deviceInstance), $this->_dealerId, $this->_hardwareOptimization->id);
            $deviceInstanceReasonElement = $form->getElement("deviceInstanceReason_" . $deviceInstanceId);
        }

        // Save the device reason into the table
        $deviceSwapReasonId             = Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->findDefaultByDealerId($deviceInstance->getDefaultDeviceSwapReasonCategoryId($this->_hardwareOptimization), $this->_identity->dealerId)->deviceSwapReasonId;
        $deviceInstanceDeviceSwapReason = $deviceInstanceDeviceSwapReasonMapper->find(array($this->_hardwareOptimization->id, $deviceInstanceId));

        if ($deviceInstanceDeviceSwapReason instanceof Hardwareoptimization_Model_Device_Instance_Device_Swap_Reason)
        {
            $deviceInstanceDeviceSwapReason->deviceSwapReasonId = $deviceSwapReasonId;
            $deviceInstanceDeviceSwapReasonMapper->save($deviceInstanceDeviceSwapReason);
        }
        else
        {
            $deviceInstanceDeviceSwapReason                         = new Hardwareoptimization_Model_Device_Instance_Device_Swap_Reason();
            $deviceInstanceDeviceSwapReason->deviceInstanceId       = $deviceInstanceId;
            $deviceInstanceDeviceSwapReason->deviceSwapReasonId     = $deviceSwapReasonId;
            $deviceInstanceDeviceSwapReason->hardwareOptimizationId = $this->_hardwareOptimization->id;
            $deviceInstanceDeviceSwapReasonMapper->insert($deviceInstanceDeviceSwapReason);
        }

        // Add calculated amounts to json
        // Monochrome CPP, Color CPP, Total Cost, Margin $, Margin %
        $this->sendJson(array(
                             "monochromeCpp" => $this->view->currency($optimization->calculateDealerWeightedAverageMonthlyCostPerPageWithReplacements()->monochromeCostPerPage, array("precision" => 4)),
                             "colorCpp"      => $this->view->currency($optimization->calculateDealerWeightedAverageMonthlyCostPerPageWithReplacements()->colorCostPerPage, array("precision" => 4)),
                             "totalCost"     => $this->view->currency($optimization->calculateDealerMonthlyCostWithReplacements()),
                             "replaceReason" => ($deviceInstanceReasonElement !== null) ? $deviceInstanceReasonElement->renderViewHelper() : " ",
                             "marginDollar"  => $this->view->currency($optimization->calculateDealerMonthlyProfitUsingTargetCostPerPageAndReplacements()),
                             "costDelta"     => $this->view->currency($costDelta),
                             "rawCostDelta"  => (float)$costDelta,
                             "marginPercent" => number_format(Tangent_Accounting::reverseEngineerMargin((float)$optimization->calculateDealerMonthlyCostWithReplacements(), (float)$optimization->calculateDealerMonthlyRevenueUsingTargetCostPerPage()), 2) . "%",
                        ));
    }

    public function summaryTableAction ()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->hardwareoptimization = $this->_hardwareOptimization;
        $this->view->optmizationViewModel = $this->getOptimizationViewModel();
    }


    public function deviceListAction ()
    {
        $jqGridService              = new Tangent_Service_JQGrid();
        $hardwareoptimizationMapper = Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance();
        $this->_saveDeviceSwapReason();

        /*
         * Grab the incoming parameters
         */
        $jqGridServiceParameters = array(
            'sidx' => $this->_getParam('sidx', 'device'),
            'sord' => $this->_getParam('sord', 'desc'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10)
        );

        $jqGridService->parseJQGridPagingRequest($jqGridServiceParameters);

        // We are not sorting in this function call - default sorting is monthly cost which will be handled by uSort
        $jqGridService->setRecordCount($hardwareoptimizationMapper->fetchAllForHardwareOptimization($this->_hardwareOptimization->id, null, null, null, true));

        // Validate current page number since we don't want to be out of bounds
        if ($jqGridService->getCurrentPage() < 1)
        {
            $jqGridService->setCurrentPage(1);
        }
        else if ($jqGridService->getCurrentPage() > $jqGridService->calculateTotalPages())
        {
            $jqGridService->setCurrentPage($jqGridService->calculateTotalPages());
        }

        // Return a small subset of the results based on the jqGrid parameters
        $startRecord = $jqGridService->getRecordsPerPage() * ($jqGridService->getCurrentPage() - 1);

        if ($startRecord < 0)
        {
            $startRecord = 0;
        }
        // Array of devices
        $rows = $hardwareoptimizationMapper->fetchAllForHardwareOptimization($this->_hardwareOptimization->id, $this->getOptimizationViewModel()->getCostPerPageSettingForDealer(), $jqGridService->getRecordsPerPage(), $startRecord);

        /* @var $deviceInstances Proposalgen_Model_DeviceInstance [] */
        $deviceInstances = $rows['deviceInstances'];
        $jsonDataRows    = $rows['jsonData'];

        // We only want to pass the devices that are being shown in the pager to form
        $form = $this->getDeviceSwapForm($deviceInstances);

        // Parse the data
        foreach ($jsonDataRows as &$row)
        {
            $replacementDeviceElement       = $form->getElement("deviceInstance_{$row['deviceInstanceId']}");
            $replacementDeviceReasonElement = $form->getElement("deviceInstanceReason_{$row['deviceInstanceId']}");
            $row['action']                  = $replacementDeviceElement->renderViewHelper();
            $row['monoCpp']                 = $this->view->currency($row['rawMonoCpp'], array('precision' => 4));
            $row['colorCpp']                = ($row['isColor']) ? $this->view->currency($row['rawColorCpp'], array('precision' => 4)) : 'N/A';
            $row['costDelta']               = $this->view->currency($row['rawCostDelta'], array('precision' => 2));
            $row['monthlyCost']             = $this->view->currency($row['rawMonthlyCost'], array('precision' => 2));

            if ($replacementDeviceReasonElement !== null)
            {
                $row['reason'] = $replacementDeviceReasonElement->renderViewHelper();
            }
            else
            {
                $row['reason'] = '';
            }
        }

        $jqGridService->setRows($jsonDataRows);

        // Send back jqGrid json data
        $this->sendJson($jqGridService->createPagerResponseArray());
    }

    /**
     * Processes device swaps reason saves.
     *
     * @param bool $deleteSwapReasons If this is set, it will reset all the device swap reason for this hardware optimization
     *
     * @throws Exception
     * @return bool
     */
    protected function _saveDeviceSwapReason ($deleteSwapReasons = false)
    {
        $success                              = true;
        $deviceInstanceDeviceSwapReasonMapper = Hardwareoptimization_Model_Mapper_Device_Instance_Device_Swap_Reason::getInstance();
        $deviceInstances                      = $this->getOptimizationViewModel()->getDevices()->purchasedDeviceInstances->getDeviceInstances();
        try
        {
            if ($deleteSwapReasons)
            {
                // Delete all the device instances device swap reasons for this hardware optimization id
                $deviceInstanceDeviceSwapReasonMapper->deleteAllByHardwareOptimizationId($this->_hardwareOptimization->id);
            }

            foreach ($deviceInstances as $deviceInstance)
            {
                $defaultReason = Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->findDefaultByDealerId($deviceInstance->getDefaultDeviceSwapReasonCategoryId($this->_hardwareOptimization->id), $this->_identity->dealerId);
                // If we have found the default reason process save / insert
                if ($defaultReason instanceof Hardwareoptimization_Model_Device_Swap_Reason_Default)
                {
                    $deviceInstanceDeviceSwapReason                         = new Hardwareoptimization_Model_Device_Instance_Device_Swap_Reason();
                    $deviceInstanceDeviceSwapReason->hardwareOptimizationId = $this->_hardwareOptimization->id;
                    $deviceInstanceDeviceSwapReason->deviceInstanceId       = $deviceInstance->id;
                    $deviceInstanceDeviceSwapReason->deviceSwapReasonId     = $defaultReason->deviceSwapReasonId;

                    // If deleteSwapReasons we know that we don't need to worry about finding and device swap reason, insert them all
                    // Or if we have a result in the database for this device instance we skip it.
                    if ($deleteSwapReasons || !$deviceInstanceDeviceSwapReasonMapper->find(array($this->_hardwareOptimization->id, $deviceInstance->id)) instanceof Hardwareoptimization_Model_Device_Instance_Device_Swap_Reason)
                    {
                        $deviceInstanceDeviceSwapReasonMapper->insert($deviceInstanceDeviceSwapReason);
                    }
                }
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Passing up the chain.", 0, $e);
        }

        return $success;
    }

    public function updateDeviceSwapReasonAction ()
    {
        $deviceInstanceDeviceSwapReasonMapper = Hardwareoptimization_Model_Mapper_Device_Instance_Device_Swap_Reason::getInstance();
        $deviceInstanceDeviceSwapReason       = null;
        $deviceInstanceId                     = $this->_getParam("deviceInstanceId", false);

        if ($deviceInstanceId === false)
        {
            $this->sendJsonError("Invalid data passed.");
        }

        try
        {
            $deviceInstanceId = (int)str_replace("deviceInstanceReason_", "", $deviceInstanceId);
            $deviceInstance   = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceInstanceId);

            // Check if device belongs to rms
            if (!$deviceInstance instanceof Proposalgen_Model_DeviceInstance || $this->_hardwareOptimization->rmsUploadId !== $deviceInstance->rmsUploadId)
            {
                $this->sendJsonError("You do not have permission to edit this device instance.");
            }

            $deviceInstanceDeviceSwapReason = $deviceInstanceDeviceSwapReasonMapper->find(array($this->_hardwareOptimization->id, $deviceInstance->id));

            if ($deviceInstanceDeviceSwapReason instanceof Hardwareoptimization_Model_Device_Instance_Device_Swap_Reason)
            {
                $deviceInstanceDeviceSwapReason->deviceSwapReasonId = $this->_getParam("replacementReasonId");
                $deviceInstanceDeviceSwapReasonMapper->save($deviceInstanceDeviceSwapReason);
            }
            else
            {
                $this->sendJsonError("Error finding device reason. Please try again.");
            }
        }
        catch (Exception $e)
        {
            $this->sendJsonError("Error saving device reason. Please try again.");
        }

        $this->sendJson($deviceInstanceDeviceSwapReason);
    }

    /**
     * Getter for _deviceSwapForm
     *
     * @param $devices
     *
     * @return \Proposalgen_Form_DeviceSwap
     */
    public function getDeviceSwapForm ($devices)
    {
        if (!isset($this->_deviceSwapForm))
        {
            $this->_deviceSwapForm = new Proposalgen_Form_DeviceSwapChoice($devices, $this->_identity->dealerId, $this->_hardwareOptimization->id);
        }

        return $this->_deviceSwapForm;
    }
}