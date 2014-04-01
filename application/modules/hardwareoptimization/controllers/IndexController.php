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
     * This action will redirect us to the latest available step
     */
    public function indexAction ()
    {
        $this->redirectToLatestStep($this->getHardwareOptimization()->stepName);
    }

    /**
     * Handles selecting a RMS upload
     */
    public function selectUploadAction ()
    {
        $this->view->headTitle('Hardware Optimization');
        $this->view->headTitle('Select Upload');

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
                    $this->_flashMessenger->addMessage(array('danger' => 'The upload you selected is not valid.'));
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

    /**
     * Handles displaying and saving of settings
     */
    public function settingsAction ()
    {
        $this->view->headTitle('Hardware Optimization');
        $this->view->headTitle('Settings');

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

    /**
     * This handles displaying and auto optimizing a fleet
     */
    public function optimizeAction ()
    {
        $this->view->headTitle('Hardware Optimization');
        $this->view->headTitle('Optimize');
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
     * Analyzes the customers fleet and uses a savings threshold to determine which devices to automatically replace
     */
    protected function _analyzeFleet ()
    {
        $db                                       = Zend_Db_Table::getDefaultAdapter();
        $optimization                             = $this->getOptimizationViewModel();
        $hardwareOptimizationDeviceInstanceMapper = Hardwareoptimization_Model_Mapper_Hardware_Optimization_DeviceInstance::getInstance();

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

                $hardwareOptimizationDeviceInstance = $deviceInstance->getHardwareOptimizationDeviceInstance($this->_hardwareOptimization->id);

                if ($suggestedDevice instanceof Proposalgen_Model_MasterDevice)
                {
                    $hardwareOptimizationDeviceInstance->action         = Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_REPLACE;
                    $hardwareOptimizationDeviceInstance->masterDeviceId = $suggestedDevice->id;
                }
                else
                {
                    $action = $deviceInstance->getAction($optimization->getCostPerPageSettingForDealer());
                    switch ($action)
                    {
                        case Proposalgen_Model_DeviceInstance::ACTION_KEEP:
                            $hardwareOptimizationDeviceInstance->action = Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_KEEP;
                            break;
                        case Proposalgen_Model_DeviceInstance::ACTION_RETIRE:
                            $hardwareOptimizationDeviceInstance->action = Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_RETIRE;
                            break;
                        case Proposalgen_Model_DeviceInstance::ACTION_REPLACE:
                            $hardwareOptimizationDeviceInstance->action = Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_DNR;
                            break;
                    }
                }

                $deviceSwapReasonId = Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->findDefaultByDealerId($deviceInstance->getDefaultDeviceSwapReasonCategoryId($this->_hardwareOptimization->id), $this->_identity->dealerId)->deviceSwapReasonId;

                $hardwareOptimizationDeviceInstance->deviceSwapReasonId = $deviceSwapReasonId;

                $hardwareOptimizationDeviceInstanceMapper->save($hardwareOptimizationDeviceInstance);
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
            Proposalgen_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance()->deleteAllDeviceInstanceReplacementsByHardwareOptimizationId($this->_hardwareOptimization->id);
            Hardwareoptimization_Model_Mapper_Hardware_Optimization_DeviceInstance::getInstance()->resetAllForHardwareOptimization($this->_hardwareOptimization->id);
        }
        catch (Exception $e)
        {
            Tangent_Log::logException($e);

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
        $deviceInstance->processOverrides($this->_hardwareOptimization->getHardwareOptimizationSetting()->adminCostPerPage, $this->_hardwareOptimization->id);

        $hardwareOptimizationDeviceInstance = $deviceInstance->getHardwareOptimizationDeviceInstance($this->_hardwareOptimization->id);
        $replacementDevice                  = $hardwareOptimizationDeviceInstance->getMasterDevice();

        $hasReplacementDevice = ($replacementDevice instanceof Proposalgen_Model_MasterDevice);

        $device = array(
            "deviceInstance" => array(
                "deviceName"             => "{$deviceInstance->getMasterDevice()->getManufacturer()->fullname} {$deviceInstance->getMasterDevice()->modelName}",
                "ipAddress"              => $deviceInstance->ipAddress,
                "age"                    => $deviceInstance->getAge(),
                "isColor"                => (int)$deviceInstance->getMasterDevice()->isColor(),
                "serialNumber"           => $deviceInstance->serialNumber,
                "lifeUsage"              => number_format($deviceInstance->getLifeUsage() * 100) . '%',
                "lifePageCount"          => number_format($deviceInstance->getMeter()->endMeterLife),
                "maxLifePageCount"       => number_format($deviceInstance->getMasterDevice()->calculateEstimatedMaxLifeCount()),
                "monoAmpv"               => $this->view->formatPageVolume($deviceInstance->getPageCounts()->getBlackPageCount()->getMonthly()),
                "colorAmpv"              => $this->view->formatPageVolume($deviceInstance->getPageCounts()->getColorPageCount()->getMonthly()),
                "costPerPageMonochrome"  => $this->view->formatCostPerPage($deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->monochromeCostPerPage),
                "costPerPageColor"       => $this->view->formatCostPerPage($deviceInstance->calculateCostPerPage($costPerPageSetting)->getCostPerPage()->colorCostPerPage),
                "jitSuppliesSupported"   => (int)$deviceInstance->isCapableOfReportingTonerLevels,
                "isCopy"                 => (int)$deviceInstance->getMasterDevice()->isCopier,
                "isFax"                  => (int)$deviceInstance->getMasterDevice()->isFax,
                "pageCoverageMonochrome" => ($deviceInstance->pageCoverageMonochrome > 0) ? number_format((float)$deviceInstance->pageCoverageMonochrome, 2) . '%' : 'N/A',
                "pageCoverageCyan"       => ($deviceInstance->pageCoverageCyan > 0) ? number_format((float)$deviceInstance->pageCoverageCyan, 2) . '%' : 'N/A',
                "pageCoverageMagenta"    => ($deviceInstance->pageCoverageMagenta > 0) ? number_format((float)$deviceInstance->pageCoverageMagenta, 2) . '%' : 'N/A',
                "pageCoverageYellow"     => ($deviceInstance->pageCoverageYellow > 0) ? number_format((float)$deviceInstance->pageCoverageYellow, 2) . '%' : 'N/A',
                "ppmBlack"               => ($deviceInstance->getMasterDevice()->ppmBlack > 0) ? number_format($deviceInstance->getMasterDevice()->ppmBlack) : 'N/A',
                "ppmColor"               => ($deviceInstance->getMasterDevice()->ppmColor > 0) ? number_format($deviceInstance->getMasterDevice()->ppmColor) : 'N/A'
            ),
            "hasReplacement" => (int)$hasReplacementDevice
        );


        if ($hasReplacementDevice)
        {
            $device ["replacementDevice"] = array(
                "deviceName"            => "{$replacementDevice->getManufacturer()->fullname} {$replacementDevice->modelName}",
                "age"                   => $replacementDevice->getAge(),
                "isColor"               => (int)$replacementDevice->isColor(),
                "costPerPageMonochrome" => $this->view->formatCostPerPage($deviceInstance->calculateCostPerPage($replacementCostPerPageSetting, $replacementDevice)->getCostPerPage()->monochromeCostPerPage),
                "costPerPageColor"      => $this->view->formatCostPerPage($deviceInstance->calculateCostPerPage($replacementCostPerPageSetting, $replacementDevice)->getCostPerPage()->colorCostPerPage),
                "maxLifePageCount"      => number_format($replacementDevice->calculateEstimatedMaxLifeCount()),
                "isCopy"                => (int)$replacementDevice->isCopier,
                "isFax"                 => (int)$replacementDevice->isFax,
                "ppmBlack"              => ($replacementDevice->ppmBlack > 0) ? number_format($replacementDevice->ppmBlack) : 'N/A',
                "ppmColor"              => ($replacementDevice->ppmColor > 0) ? number_format($replacementDevice->ppmColor) : 'N/A',
                "reason"                => $deviceInstance->getReason($this->_hardwareOptimization->id)
            );
        }

        $this->sendJson($device);
    }

    /**
     * Updates the replacement device chosen
     */
    public function updateReplacementDeviceAction ()
    {
        $hardwareOptimizationDeviceInstanceMapper = Hardwareoptimization_Model_Mapper_Hardware_Optimization_DeviceInstance::getInstance();

        // Setup the required mappers
        $optimization                = $this->getOptimizationViewModel();
        $deviceInstanceReasonElement = null;

        // Setup the master device labor and parts cost per page
        Proposalgen_Model_MasterDevice::$ReportLaborCostPerPage = $this->_hardwareOptimization->getHardwareOptimizationSetting()->laborCostPerPage;
        Proposalgen_Model_MasterDevice::$ReportPartsCostPerPage = $this->_hardwareOptimization->getHardwareOptimizationSetting()->partsCostPerPage;

        /**
         * Require a device instance
         */
        $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
        if ($deviceInstanceId === false)
        {
            $this->sendJsonError("Invalid data passed.");
        }

        $deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceInstanceId);

        // Check if device belongs to RMS
        if (!$deviceInstance instanceof Proposalgen_Model_DeviceInstance)
        {
            $this->sendJsonError("Invalid device selected.");
        }

        if ($this->_hardwareOptimization->rmsUploadId !== $deviceInstance->rmsUploadId)
        {
            $this->sendJsonError("You do not have permission to edit this device instance.");
        }

        $hardwareOptimizationDeviceInstance = $deviceInstance->getHardwareOptimizationDeviceInstance($this->_hardwareOptimization->id);
        $replacementDeviceId                = $this->_getParam("replacementDeviceId", false);

        $costDelta                   = 0;
        $deviceInstanceReasonElement = null;

        if ($replacementDeviceId !== false)
        {
            if ((int)$replacementDeviceId > 0)
            {
                /**
                 * REPLACE
                 */
                $deviceSwap = Hardwareoptimization_Model_Mapper_Device_Swap::getInstance()->find(array($replacementDeviceId, $this->_identity->dealerId));

                if ($deviceSwap instanceof Hardwareoptimization_Model_Device_Swap)
                {
                    $hardwareOptimizationDeviceInstance->action         = Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_REPLACE;
                    $hardwareOptimizationDeviceInstance->masterDeviceId = $replacementDeviceId;

                    /**
                     * Recalculate the cost deltas
                     */
                    $replacementMasterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->findForReports($replacementDeviceId,
                        $this->_identity->dealerId,
                        $this->_hardwareOptimization->getHardwareOptimizationSetting()->laborCostPerPage,
                        $this->_hardwareOptimization->getHardwareOptimizationSetting()->partsCostPerPage
                    );


                    $costDelta = $deviceInstance->calculateMonthlyCost($optimization->getCostPerPageSettingForDealer()) -
                                 $deviceInstance->calculateMonthlyCost($optimization->getCostPerPageSettingForReplacements(), $replacementMasterDevice);


                    /**
                     * Update the reason to match the default
                     */
                    $deviceSwapReasonId = Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->findDefaultByDealerId($deviceInstance->getDefaultDeviceSwapReasonCategoryId($this->_hardwareOptimization->id), $this->_identity->dealerId)->deviceSwapReasonId;

                    $hardwareOptimizationDeviceInstance->deviceSwapReasonId = $deviceSwapReasonId;

                    $form                        = new Proposalgen_Form_DeviceSwapChoice(array($deviceInstance), $this->_dealerId, $this->_hardwareOptimization->id);
                    $deviceInstanceReasonElement = $form->getElement("deviceInstanceReason_" . $deviceInstanceId);
                }
                else
                {
                    $this->sendJsonError("Invalid replacement device selected.");
                }
            }
            else
            {
                /**
                 * Any other action
                 */
                switch ($replacementDeviceId)
                {
                    case Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_KEEP:
                    case Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_RETIRE:
                    case Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_DNR:
                        $hardwareOptimizationDeviceInstance->action         = $replacementDeviceId;
                        $hardwareOptimizationDeviceInstance->masterDeviceId = new Zend_Db_Expr("NULL");
                        break;
                    default:
                        $this->sendJsonError("Invalid action selected.");
                        break;
                }

                if ($hardwareOptimizationDeviceInstance->action === Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_DNR)
                {

                    /**
                     * Update the reason to match the default
                     */
                    $deviceSwapReasonId = Hardwareoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->findDefaultByDealerId($deviceInstance->getDefaultDeviceSwapReasonCategoryId($this->_hardwareOptimization->id), $this->_identity->dealerId)->deviceSwapReasonId;

                    $hardwareOptimizationDeviceInstance->deviceSwapReasonId = $deviceSwapReasonId;

                    $form                        = new Proposalgen_Form_DeviceSwapChoice(array($deviceInstance), $this->_dealerId, $this->_hardwareOptimization->id);
                    $deviceInstanceReasonElement = $form->getElement("deviceInstanceReason_" . $deviceInstanceId);
                }
            }
        }
        else
        {
            $this->sendJsonError("Invalid action selected.");
        }


        /**
         * Save the data
         */
        $hardwareOptimizationDeviceInstanceMapper->save($hardwareOptimizationDeviceInstance);

        /**
         * We're sending back updated numbers so that we can update the page with the new calculations
         * Monochrome CPP, Color CPP, Total Cost, Margin $, Margin %
         */
        $this->sendJson(array(
            "monochromeCpp"           => $this->view->formatCostPerPage($optimization->calculateDealerWeightedAverageMonthlyCostPerPageWithReplacements()->monochromeCostPerPage),
            "colorCpp"                => $this->view->formatCostPerPage($optimization->calculateDealerWeightedAverageMonthlyCostPerPageWithReplacements()->colorCostPerPage),
            "totalCost"               => $this->view->currency($optimization->calculateDealerMonthlyCostWithReplacements()),
            "replaceReason"           => ($deviceInstanceReasonElement !== null) ? $deviceInstanceReasonElement->renderViewHelper() : " ",
            "marginDollar"            => $this->view->currency($optimization->calculateDealerMonthlyProfitUsingTargetCostPerPageAndReplacements()),
            "costDelta"               => (float)$costDelta,
            "rawCostDelta"            => (float)$costDelta,
            "marginPercent"           => number_format(Tangent_Accounting::reverseEngineerMargin((float)$optimization->calculateDealerMonthlyCostWithReplacements(), (float)$optimization->calculateDealerMonthlyRevenueUsingTargetCostPerPage()), 2) . "%",
            "numberOfDevicesReplaced" => number_format($optimization->getNumberOfDevicesWithReplacements()),
        ));
    }

    /**
     * Handles displaying the summary table
     */
    public function summaryTableAction ()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->hardwareoptimization = $this->_hardwareOptimization;
        $this->view->optmizationViewModel = $this->getOptimizationViewModel();
    }


    /**
     * Gets the device list for the optimize page
     */
    public function deviceListAction ()
    {
        $jqGridService              = new Tangent_Service_JQGrid();
        $hardwareoptimizationMapper = Hardwareoptimization_Model_Mapper_Hardware_Optimization::getInstance();

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
            $row['monoCpp']                 = $row['rawMonoCpp'];
            $row['colorCpp']                = ($row['isColor']) ? $row['rawColorCpp'] : '';
            $row['costDelta']               = $row['rawCostDelta'];
            $row['monthlyCost']             = $row['rawMonthlyCost'];

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

        // Send back jqGrid JSON data
        $this->sendJson($jqGridService->createPagerResponseArray());
    }

    /**
     * Updates a device instance reason
     */
    public function updateDeviceSwapReasonAction ()
    {
        $hardwareOptimizationDeviceInstanceMapper = Hardwareoptimization_Model_Mapper_Hardware_Optimization_DeviceInstance::getInstance();
        $deviceSwapReasonMapper                   = Hardwareoptimization_Model_Mapper_Device_Swap_Reason::getInstance();

        /**
         * Require a device instance
         */
        $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
        if ($deviceInstanceId === false)
        {
            $this->sendJsonError("Invalid data passed.");
        }

        $deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceInstanceId);

        // Check if device belongs to RMS
        if (!$deviceInstance instanceof Proposalgen_Model_DeviceInstance)
        {
            $this->sendJsonError("Invalid device selected.");
        }

        if ($this->_hardwareOptimization->rmsUploadId !== $deviceInstance->rmsUploadId)
        {
            $this->sendJsonError("You do not have permission to edit this device instance.");
        }

        $replacementReasonId = $this->_getParam("replacementReasonId");
        $replacementReason   = $deviceSwapReasonMapper->find($replacementReasonId);

        if (!$replacementReason instanceof Hardwareoptimization_Model_Device_Swap_Reason)
        {
            $this->sendJsonError("Invalid device swap reason.");
        }

        try
        {
            $hardwareOptimizationDeviceInstance                     = $deviceInstance->getHardwareOptimizationDeviceInstance($this->_hardwareOptimization->id);
            $hardwareOptimizationDeviceInstance->deviceSwapReasonId = $replacementReason->id;
            $hardwareOptimizationDeviceInstanceMapper->save($hardwareOptimizationDeviceInstance);
        }
        catch (Exception $e)
        {
            Tangent_Log::logException($e);
            $this->sendJsonError("Error saving device reason. Please try again. #" . Tangent_Log::getUniqueId());
        }

        $this->sendJson(array("success" => "Reason Saved!"));
    }

    /**
     * Getter for _deviceSwapForm
     *
     * @param $devices
     *
     * @return \Proposalgen_Form_DeviceSwapChoice
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