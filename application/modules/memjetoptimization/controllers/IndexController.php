<?php
/**
 * Class Memjetoptimization_IndexController
 */
class Memjetoptimization_IndexController extends Memjetoptimization_Library_Controller_Action
{
    /**
     * @var Proposalgen_Form_DeviceSwap
     */
    protected $_deviceSwapForm;
    /**
     * @var Memjetoptimization_ViewModel_Devices
     */
    protected $_deviceViewModel;


    /**
     * This action will redirect us to the latest available step
     */
    public function indexAction ()
    {
        $this->redirectToLatestStep($this->getMemjetOptimization()->stepName);
    }

    /**
     * Handles selecting a RMS upload
     */
    public function selectUploadAction ()
    {
        $this->view->headTitle('Memjet Optimization');
        $this->view->headTitle('Select Upload');
        $this->_navigation->setActiveStep(Memjetoptimization_Model_Memjet_Optimization_Steps::STEP_FLEET_UPLOAD);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (isset($postData['selectRmsUploadId']))
            {
                $selectRmsUploadService = new Proposalgen_Service_SelectRmsUpload($this->_mpsSession->selectedClientId);
                $rmsUpload              = $selectRmsUploadService->validateRmsUploadId($postData['selectRmsUploadId']);
                if ($rmsUpload instanceof Proposalgen_Model_Rms_Upload)
                {
                    $this->getMemjetOptimization()->rmsUploadId = $rmsUpload->id;
                    $this->updateStepName();
                    $this->saveMemjetOptimization();
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
            if ($this->getMemjetOptimization()->rmsUploadId > 0)
            {
                if (isset($postData['saveAndContinue']))
                {
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
        }

        $this->view->numberOfUploads = count(Proposalgen_Model_Mapper_Rms_Upload::getInstance()->fetchAllForClient($this->getMemjetOptimization()->clientId));
        $this->view->rmsUpload       = $this->getMemjetOptimization()->getRmsUpload();
        $this->view->navigationForm  = new Memjetoptimization_Form_Memjet_Optimization_Navigation(Memjetoptimization_Form_Memjet_Optimization_Navigation::BUTTONS_NEXT);
    }

    public function settingsAction ()
    {
        $this->view->headTitle('Memjet Optimization');
        $this->view->headTitle('Settings');
        $this->_navigation->setActiveStep(Memjetoptimization_Model_Memjet_Optimization_Steps::STEP_SETTINGS);

        $user = Application_Model_Mapper_User::getInstance()->find($this->_identity->id);

        $defaultMemjetOptimizationSettings = clone $user->getDealer()->getDealerSettings()->getMemjetOptimizationSettings();
        $defaultMemjetOptimizationSettings->populate($user->getUserSettings()->getMemjetOptimizationSettings()->toArray());
        $memjetOptimizationService = new Memjetoptimization_Service_Setting($this->_memjetOptimization->getMemjetOptimizationSetting(), $defaultMemjetOptimizationSettings, $this->_memjetOptimization->id);

        $form = $memjetOptimizationService->getFormWithDefaults();

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (!isset($postData['goBack']))
            {
                // Save
                $this->saveMemjetOptimization();
                $memjetOptimizationService->update($postData, $defaultMemjetOptimizationSettings->toArray());

                if (isset($postData['saveAndContinue']))
                {
                    $this->updateStepName();
                    $this->saveMemjetOptimization();
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
     * Displays all our devices and allows the user to optimize a fleet
     */
    public function optimizeAction ()
    {
        $this->view->headTitle('Memjet Optimization');
        $this->view->headTitle('Optimize');
        // Mark the step we're on as active
        $this->_navigation->setActiveStep(Memjetoptimization_Model_Memjet_Optimization_Steps::STEP_OPTIMIZE);

        // $form = $this->getDeviceSwapForm($this->getDeviceViewModel()->purchasedDeviceInstances->getDeviceInstances());
        $form = new Memjetoptimization_Form_OptimizeActions();

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
                        $this->saveMemjetOptimization();
                        $this->gotoNextNavigationStep($this->_navigation);
                    }
                }
                else if ($form->getValue('Analyze'))
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

        $this->view->form               = $form;
        $this->view->memjetOptimization = $this->_memjetOptimization;
        $this->view->navigationForm     = new Proposalgen_Form_Assessment_Navigation(Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT);
    }

    /**
     * Analyzes the customers fleet and uses average monthly page volume to determine which devices to automatically replace
     */
    protected function _memjetAnalyzeFleet ()
    {
        $db                              = Zend_Db_Table::getDefaultAdapter();
        $optimization                    = $this->getOptimizationViewModel();
        $deviceInstanceReplacementMapper = Memjetoptimization_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance();

        try
        {
            $db->beginTransaction();

            // Delete all our replacements
            if (!$this->_resetReplacements())
            {
                throw new Exception("Error resetting replacements!");
            }

            $blackReplacementDevices    = Admin_Model_Mapper_Memjet_Device_Swap::getInstance()->getBlackReplacementDevices($this->_dealerId, false);
            $blackMfpReplacementDevices = Admin_Model_Mapper_Memjet_Device_Swap::getInstance()->getBlackMfpReplacementDevices($this->_dealerId, false);
            $colorReplacementDevices    = Admin_Model_Mapper_Memjet_Device_Swap::getInstance()->getColorReplacementDevices($this->_dealerId, false);
            $colorMfpReplacementDevices = Admin_Model_Mapper_Memjet_Device_Swap::getInstance()->getColorMfpReplacementDevices($this->_dealerId, false);

            /* @var $replacementMasterDevice Admin_Model_Memjet_Device_Swap */
            foreach (array_merge($blackReplacementDevices, $blackMfpReplacementDevices, $colorReplacementDevices, $colorMfpReplacementDevices) as $replacementMasterDevice)
            {
                $replacementMasterDevice->getMasterDevice()->processOverrides($this->_memjetOptimization->getMemjetOptimizationSetting()->adminCostPerPage);
            }

            $memjetDeviceReplacement = new Memjetoptimization_Model_Optimization_MemjetDeviceReplacement(
                array(
                     'black'    => $blackReplacementDevices,
                     'blackmfp' => $blackMfpReplacementDevices,
                     'color'    => $colorReplacementDevices,
                     'colormfp' => $colorMfpReplacementDevices
                ),
                $this->_dealerId,
                $this->_memjetOptimization->getMemjetOptimizationSetting()->lossThreshold,
                $this->_memjetOptimization->getMemjetOptimizationSetting()->costThreshold,
                $optimization->getCostPerPageSettingForDealer(),
                $optimization->getCostPerPageSettingForReplacements(),
                $this->_memjetOptimization->getMemjetOptimizationSetting()->laborCostPerPage,
                $this->_memjetOptimization->getMemjetOptimizationSetting()->partsCostPerPage,
                $this->_memjetOptimization->getMemjetOptimizationSetting()->blackToColorRatio
            );

            foreach ($optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $suggestedDevice = $memjetDeviceReplacement->findReplacement($deviceInstance);

                if ($suggestedDevice instanceof Proposalgen_Model_MasterDevice)
                {
                    $newDevice                       = new Memjetoptimization_Model_Device_Instance_Replacement_Master_Device();
                    $newDevice->masterDeviceId       = $suggestedDevice->id;
                    $newDevice->deviceInstanceId     = $deviceInstance->id;
                    $newDevice->memjetOptimizationId = $this->_memjetOptimization->id;
                    $deviceInstanceReplacementMapper->insert($newDevice);
                }
            }

            /**
             * This saves all the reasons for each device
             */
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
            $deviceInstanceReplacementMapper = Memjetoptimization_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance();
            $deviceInstanceReplacementMapper->deleteAllDeviceInstanceReplacementsByMemjetOptimizationId($this->_memjetOptimization->id);
        }
        catch (Exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     * Returns JSON based on id that has been passed via query string
     */
    public function getDeviceByDeviceInstanceIdAction ()
    {
        Proposalgen_Model_MasterDevice::$ReportLaborCostPerPage = $this->_memjetOptimization->getMemjetOptimizationSetting()->laborCostPerPage;
        Proposalgen_Model_MasterDevice::$ReportPartsCostPerPage = $this->_memjetOptimization->getMemjetOptimizationSetting()->partsCostPerPage;

        $optimization                  = $this->getOptimizationViewModel();
        $costPerPageSetting            = $optimization->getCostPerPageSettingForDealer();
        $replacementCostPerPageSetting = $optimization->getCostPerPageSettingForReplacements();

        $instanceId     = $this->_getParam('deviceInstanceId');
        $deviceInstance = null;
        $deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($instanceId);
        $deviceInstance->processOverrides($this->_memjetOptimization->getMemjetOptimizationSetting()->adminCostPerPage);

        $replacementDevice    = $deviceInstance->getReplacementMasterDeviceForMemjetOptimization($this->_memjetOptimization->id);
        $hasReplacementDevice = ($replacementDevice instanceof Proposalgen_Model_MasterDevice);

        $device = array(
            "deviceInstance" => array(
                "deviceName"            => "{$deviceInstance->getMasterDevice()->getManufacturer()->fullname} {$deviceInstance->getMasterDevice()->modelName}",
                "ipAddress"             => $deviceInstance->ipAddress,
                "isColor"               => (int)$deviceInstance->getMasterDevice()->isColor(),
                "serialNumber"          => $deviceInstance->serialNumber,
                "lifePageCount"         => number_format($deviceInstance->getMeter()->endMeterLife),
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
                "reason"                => $deviceInstance->getMemjetReason($this->_memjetOptimization->id)
            );
        }

        $this->sendJson($device);
    }

    /**
     * Handles updating single device replacements on the optimize page
     */
    public function updateReplacementDeviceAction ()
    {
        // Setup the required mappers
        $memjetDeviceInstanceReplacementMasterDeviceMapper = Memjetoptimization_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance();
        $deviceInstanceDeviceSwapReasonMapper              = Memjetoptimization_Model_Mapper_Device_Instance_Device_Swap_Reason::getInstance();
        $memjetOptimizationViewModel                       = $this->getOptimizationViewModel();
        $memjetOptimization                                = $this->getMemjetOptimization();
        $deviceInstanceReasonElement                       = null;
        $blackToColorRatio                                 = $this->getMemjetOptimization()->getMemjetOptimizationSetting()->blackToColorRatio;

        // Setup the master device labor and parts cost per page
        Proposalgen_Model_MasterDevice::$ReportLaborCostPerPage = $this->_memjetOptimization->getMemjetOptimizationSetting()->laborCostPerPage;
        Proposalgen_Model_MasterDevice::$ReportPartsCostPerPage = $this->_memjetOptimization->getMemjetOptimizationSetting()->partsCostPerPage;

        // Get and see if we have a device instance id passed, if not send a JSON error
        $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
        if ($deviceInstanceId === false)
        {
            $this->sendJsonError("Invalid data passed.");
        }

        $deviceInstanceId = (int)str_replace("deviceInstance_", "", $deviceInstanceId);
        $deviceInstance   = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceInstanceId);

        // Check if device belongs to RMS
        if (!$deviceInstance instanceof Proposalgen_Model_DeviceInstance || $this->_memjetOptimization->rmsUploadId !== $deviceInstance->rmsUploadId)
        {
            $this->sendJsonError("You do not have permission to edit this device instance.");
        }

        $replacementDeviceId = (int)$this->_getParam("replacementDeviceId");
        $replacementDevice   = Proposalgen_Model_Mapper_MasterDevice::getInstance()->findForReports($replacementDeviceId, $this->_identity->dealerId, $this->_memjetOptimization->getMemjetOptimizationSetting()->laborCostPerPage, $this->_memjetOptimization->getMemjetOptimizationSetting()->partsCostPerPage);
        $whereKey            = array($deviceInstanceId, $this->_memjetOptimization->id);

        if ($replacementDeviceId == 0)
        {
            // Delete the row from the database
            $memjetDeviceInstanceReplacementMasterDeviceMapper->delete($whereKey);
            $costDelta = 0;
        }
        else
        {
            $deviceInstanceReplacementMasterDevice = $memjetDeviceInstanceReplacementMasterDeviceMapper->find($whereKey);
            if ($deviceInstanceReplacementMasterDevice instanceof Memjetoptimization_Model_Device_Instance_Replacement_Master_Device)
            {
                // Update the device information
                $deviceInstanceReplacementMasterDevice->masterDeviceId = $replacementDeviceId;
                $memjetDeviceInstanceReplacementMasterDeviceMapper->save($deviceInstanceReplacementMasterDevice);
            }
            else
            {
                // Insert the device into the table
                $deviceInstanceReplacementMasterDevice                       = new Memjetoptimization_Model_Device_Instance_Replacement_Master_Device();
                $deviceInstanceReplacementMasterDevice->deviceInstanceId     = $deviceInstanceId;
                $deviceInstanceReplacementMasterDevice->masterDeviceId       = $replacementDeviceId;
                $deviceInstanceReplacementMasterDevice->memjetOptimizationId = $this->_memjetOptimization->id;
                $memjetDeviceInstanceReplacementMasterDeviceMapper->insert($deviceInstanceReplacementMasterDevice);
            }


            $costDelta = $deviceInstance->calculateMonthlyCost($memjetOptimizationViewModel->getCostPerPageSettingForDealer()) -
                         $deviceInstance->calculateMonthlyCost($memjetOptimizationViewModel->getCostPerPageSettingForReplacements(), $replacementDevice, ($replacementDevice != null && $deviceInstance->getMasterDevice()->isColor() == false && $replacementDevice->isColor()) ? $blackToColorRatio : null);

            $form                        = new Memjetoptimization_Form_DeviceSwapChoice(array($deviceInstance), $this->_dealerId, $this->_memjetOptimization->id);
            $deviceInstanceReasonElement = $form->getElement("deviceInstanceReason_" . $deviceInstanceId);
        }

        // Save the device reason into the table
        $deviceSwapReasonId             = Memjetoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->findDefaultByDealerId($deviceInstance->getDefaultMemjetDeviceSwapReasonCategoryId($this->_memjetOptimization->id), $this->_identity->dealerId)->deviceSwapReasonId;
        $deviceInstanceDeviceSwapReason = $deviceInstanceDeviceSwapReasonMapper->find(array($this->_memjetOptimization->id, $deviceInstanceId));
        if ($deviceInstanceDeviceSwapReason instanceof Memjetoptimization_Model_Device_Instance_Device_Swap_Reason)
        {
            $deviceInstanceDeviceSwapReason->deviceSwapReasonId = $deviceSwapReasonId;
            $deviceInstanceDeviceSwapReasonMapper->save($deviceInstanceDeviceSwapReason);
        }
        else
        {
            $deviceInstanceDeviceSwapReason                       = new Memjetoptimization_Model_Device_Instance_Device_Swap_Reason();
            $deviceInstanceDeviceSwapReason->deviceInstanceId     = $deviceInstanceId;
            $deviceInstanceDeviceSwapReason->deviceSwapReasonId   = $deviceSwapReasonId;
            $deviceInstanceDeviceSwapReason->memjetOptimizationId = $this->_memjetOptimization->id;
            $deviceInstanceDeviceSwapReasonMapper->insert($deviceInstanceDeviceSwapReason);
        }

        $estimatedPageCounts = $deviceInstance->getPageCounts(($replacementDevice != null && $deviceInstance->getMasterDevice()->isColor() == false && $replacementDevice->isColor()) ? $blackToColorRatio : null);

        $grossMarginDelta           = (float)$memjetOptimizationViewModel->calculateDealerMonthlyProfitUsingTargetCostPerPageAndReplacements($blackToColorRatio) -
                                      (float)$memjetOptimizationViewModel->calculateDealerMonthlyProfitUsingTargetCostPerPage();
        $grossMarginDeltaIsPositive = ($grossMarginDelta >= 0);
        $grossMarginDelta           = abs($grossMarginDelta);

        $customerCostDelta         = (float)$memjetOptimizationViewModel->calculateDealerMonthlyRevenueUsingTargetCostPerPageWithReplacements($blackToColorRatio) -
                                     (float)$memjetOptimizationViewModel->calculateDealerMonthlyRevenueUsingTargetCostPerPage();
        $customerCostDeltaPositive = ($customerCostDelta >= 0);
        $customerCostDelta         = abs($customerCostDelta);

        /**
         * Send data back so we can update the page on the fly
         */
        $this->sendJson(
             array(
                  "summary" => array(
                      /**
                       * Summary page Page Data
                       */
                      "monochromeCpp"               => $this->view->currency($memjetOptimizationViewModel->calculateDealerWeightedAverageMonthlyCostPerPageWithReplacements($blackToColorRatio)->monochromeCostPerPage, array("precision" => 4)),
                      "colorCpp"                    => $this->view->currency($memjetOptimizationViewModel->calculateDealerWeightedAverageMonthlyCostPerPageWithReplacements($blackToColorRatio)->colorCostPerPage, array("precision" => 4)),
                      "totalRevenue"                => $this->view->currency((float)$memjetOptimizationViewModel->calculateDealerMonthlyRevenueUsingTargetCostPerPageWithReplacements($blackToColorRatio), array('precision' => 2)),
                      "monoVolume"                  => number_format($memjetOptimizationViewModel->getPageCounts($blackToColorRatio)->getBlackPageCount()->getMonthly()),
                      "monoVolumePercent"           => number_format($memjetOptimizationViewModel->getPageCounts($blackToColorRatio)->getMonochromePagePercentage()),
                      "colorVolume"                 => number_format($memjetOptimizationViewModel->getPageCounts($blackToColorRatio)->getColorPageCount()->getMonthly()),
                      "colorVolumePercent"          => number_format($memjetOptimizationViewModel->getPageCounts($blackToColorRatio)->getColorPagePercentage()),
                      "totalCost"                   => $this->view->currency($memjetOptimizationViewModel->calculateDealerMonthlyCostWithReplacements($blackToColorRatio)),
                      "marginDollar"                => $this->view->currency($memjetOptimizationViewModel->calculateDealerMonthlyProfitUsingTargetCostPerPageAndReplacements($blackToColorRatio)),
                      "marginPercent"               => number_format(Tangent_Accounting::reverseEngineerMargin((float)$memjetOptimizationViewModel->calculateDealerMonthlyCostWithReplacements(), (float)$memjetOptimizationViewModel->calculateDealerMonthlyRevenueUsingTargetCostPerPage()), 2),
                      "grossMarginDelta"            => $this->view->currency($grossMarginDelta),
                      "grossMarginDeltaIsPositive"  => ($grossMarginDeltaIsPositive) ? true : false,
                      "customerCostDelta"           => $this->view->currency($customerCostDelta),
                      "customerCostDeltaIsPositive" => ($customerCostDeltaPositive) ? true : false,
                      "numberOfDevicesReplaced"     => number_format($memjetOptimizationViewModel->getNumberOfDevicesWithReplacements()),
                  ),
                  "device"  => array(
                      /**
                       * Device Data
                       */

                      "costDelta"          => $this->view->currency($costDelta),
                      "rawCostDelta"       => (float)$costDelta,
                      "estimatedMonoAmpv"  => number_format($estimatedPageCounts->getBlackPageCount()->getMonthly()),
                      "estimatedColorAmpv" => number_format($estimatedPageCounts->getColorPageCount()->getMonthly()),
                      "replaceReason"      => ($deviceInstanceReasonElement !== null) ? $deviceInstanceReasonElement->renderViewHelper() : " ",
                  ),
             )
        );
    }

    /**
     * Renders our summary table for us
     */
    public function summaryTableAction ()
    {
        $this->_helper->layout()->disableLayout();
        $this->view->memjetOptimization   = $this->_memjetOptimization;
        $this->view->optmizationViewModel = $this->getOptimizationViewModel();
    }


    /**
     * Provider the device list for jqgrid on the optimize page
     */
    public function deviceListAction ()
    {
        $jqGridService            = new Tangent_Service_JQGrid();
        $memjetoptimizationMapper = Memjetoptimization_Model_Mapper_Memjet_Optimization::getInstance();
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
        $jqGridService->setRecordCount($memjetoptimizationMapper->fetchAllForMemjetOptimization($this->_memjetOptimization->id, null, null, null, true));

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
        $rows = $memjetoptimizationMapper->fetchAllForMemjetOptimization($this->_memjetOptimization->id, $this->getOptimizationViewModel()->getCostPerPageSettingForDealer(), $jqGridService->getRecordsPerPage(), $startRecord);

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

        // Send back jqGrid JSON data
        $this->sendJson($jqGridService->createPagerResponseArray());
    }

    /**
     * Processes device swaps reason saves.
     *
     * @param bool $deleteSwapReasons If this is set, it will reset all the device swap reason for this Memjet optimization
     *
     * @throws Exception
     * @return bool
     */
    protected function _saveDeviceSwapReason ($deleteSwapReasons = false)
    {
        $success                              = true;
        $deviceInstanceDeviceSwapReasonMapper = Memjetoptimization_Model_Mapper_Device_Instance_Device_Swap_Reason::getInstance();
        $deviceInstances                      = $this->getOptimizationViewModel()->getDevices()->purchasedDeviceInstances->getDeviceInstances();
        try
        {
            if ($deleteSwapReasons)
            {
                // Delete all the device instances device swap reasons for this Memjet optimization id
                $deviceInstanceDeviceSwapReasonMapper->deleteAllByMemjetOptimizationId($this->_memjetOptimization->id);
            }

            foreach ($deviceInstances as $deviceInstance)
            {
                $defaultReason = Memjetoptimization_Model_Mapper_Device_Swap_Reason_Default::getInstance()->findDefaultByDealerId($deviceInstance->getDefaultMemjetDeviceSwapReasonCategoryId($this->_memjetOptimization->id), $this->_identity->dealerId);
                // If we have found the default reason process save / insert
                if ($defaultReason instanceof Memjetoptimization_Model_Device_Swap_Reason_Default)
                {
                    $deviceInstanceDeviceSwapReason                       = new Memjetoptimization_Model_Device_Instance_Device_Swap_Reason();
                    $deviceInstanceDeviceSwapReason->memjetOptimizationId = $this->_memjetOptimization->id;
                    $deviceInstanceDeviceSwapReason->deviceInstanceId     = $deviceInstance->id;
                    $deviceInstanceDeviceSwapReason->deviceSwapReasonId   = $defaultReason->deviceSwapReasonId;

                    // If deleteSwapReasons we know that we don't need to worry about finding and device swap reason, insert them all
                    // Or if we have a result in the database for this device instance we skip it.
                    if ($deleteSwapReasons || !$deviceInstanceDeviceSwapReasonMapper->find(array($this->_memjetOptimization->id, $deviceInstance->id)) instanceof Memjetoptimization_Model_Device_Instance_Device_Swap_Reason)
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

    /**
     * Updates a single devices swap reason
     */
    public function updateDeviceSwapReasonAction ()
    {
        $deviceInstanceDeviceSwapReasonMapper = Memjetoptimization_Model_Mapper_Device_Instance_Device_Swap_Reason::getInstance();
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

            // Check if device belongs to RMS
            if (!$deviceInstance instanceof Proposalgen_Model_DeviceInstance || $this->_memjetOptimization->rmsUploadId !== $deviceInstance->rmsUploadId)
            {
                $this->sendJsonError("You do not have permission to edit this device instance.");
            }

            $deviceInstanceDeviceSwapReason = $deviceInstanceDeviceSwapReasonMapper->find(array($this->_memjetOptimization->id, $deviceInstance->id));

            if ($deviceInstanceDeviceSwapReason instanceof Memjetoptimization_Model_Device_Instance_Device_Swap_Reason)
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
     * @return Memjetoptimization_Form_DeviceSwapChoice
     */
    public function getDeviceSwapForm ($devices)
    {
        if (!isset($this->_deviceSwapForm))
        {
            $this->_deviceSwapForm = new Memjetoptimization_Form_DeviceSwapChoice($devices, $this->_identity->dealerId, $this->_memjetOptimization->id);
        }

        return $this->_deviceSwapForm;
    }
}