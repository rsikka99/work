<?php
class Hardwareoptimization_IndexController extends Hardwareoptimization_Library_Controller_Action
{
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

            if ($this->getHardwareOptimization()->rmsUploadId > 0)
            {
                if (isset($postData['saveAndContinue']))
                {
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
        }

        $this->view->rmsUpload      = $this->getHardwareOptimization()->getRmsUpload();
        $this->view->navigationForm = new Hardwareoptimization_Form_Hardware_Optimization_Navigation(Hardwareoptimization_Form_Hardware_Optimization_Navigation::BUTTONS_NEXT);
    }

    public function settingsAction ()
    {
        $this->_navigation->setActiveStep(Hardwareoptimization_Model_Hardware_Optimization_Steps::STEP_SETTINGS);

        $user                        = Application_Model_Mapper_User::getInstance()->find($this->_identity->id);
        $hardwareOptimizationService = new Hardwareoptimization_Service_Setting($this->_hardwareOptimization->getHardwareOptimizationSetting()->toArray());

        $defaultHardwareOptimizationSettings = $user->getDealer()->getDealerSettings()->getHardwareOptimizationSettings();
        $defaultHardwareOptimizationSettings->populate($user->getUserSettings()->getHardwareOptimizationSettings()->toArray());
        $form = $hardwareOptimizationService->getFormWithDefaults($defaultHardwareOptimizationSettings->toArray());

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
        $devicesViewModel = new Hardwareoptimization_ViewModel_Devices($this->_hardwareOptimization);

        // Every time we save anything related to a report, we should save it (updates the modification date)
        $form = new Proposalgen_Form_DeviceSwapChoice($devicesViewModel->purchasedDeviceInstances, Zend_Auth::getInstance()->getIdentity()->dealerId);

        // Get all devices
        // $devices = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchDevicesInstancesForMapping($this->getProposal()->report->rmsUploadId, 'id', 'asc');
        $devices = $devicesViewModel->purchasedDeviceInstances;

        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();
            if ($form->isValid($postData))
            {
                if ($form->getValue('Submit') || isset($postData["saveAndContinue"]))
                {
                    if ($this->_processSaveProfitability($form))
                    {
                        if(isset($postData["saveAndContinue"]))
                        {
                            $this->updateStepName();
                            $this->saveHardwareOptimization();
                            $this->gotoNextNavigationStep($this->_navigation);
                        }
                        $this->_helper->flashMessenger(array(
                                                            'success' => "Your changes have been saved."
                                                       ));
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array(
                                                            'danger' => "There was an error saving your replacement choices."
                                                       ));
                    }
                }
                else if ($form->getValue('Analyze'))
                {
                    // Analyze the fleet. If it is successful we need to rebuild our form
                    if ($this->_analyzeFleet())
                    {
                        $this->_helper->flashMessenger(array(
                                                            'success' => "We've optimized your fleet. Please review the changes before proceeding."
                                                       ));
                        $this->_helper->redirector('optimize', null, null, array());
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array(
                                                            'danger' => "There was an error saving your replacement choices."
                                                       ));
                    }
                }
                else if ($form->getValue('ResetReplacements'))
                {
                    if ($this->_resetReplacements())
                    {
                        $this->_helper->flashMessenger(array(
                                                            'success' => "Device replacements have been reset."
                                                       ));
                        $this->_helper->redirector('optimize', null, null, array());
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array(
                                                            'danger' => "There was an error resetting your replacement choices."
                                                       ));
                    }
                }
                else if ($form->getValues('Cancel'))
                {

                    $this->gotoPreviousNavigationStep($this->_navigation);
                }
            }
        }

        $this->view->form                 = $form;
        $this->view->devices              = $devices;
        $this->view->hardwareOptimization = $this->_hardwareOptimization;
        $this->view->optmizationViewModel = $this->getOptimizationViewModel();
        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation(Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT);
        $this->view->title          = 'Hardware Optimization';
    }

    /**
     * Returns Json based on id that has been pased via query string
     *
     * @return data <json>
     */
    public function getDeviceByDeviceInstanceIdAction ()
    {
        $optimization       = $this->getOptimizationViewModel();
        $costPerPageSetting = $optimization->getCostPerPageSettingForDealer();

        $instanceId     = $this->_getParam('deviceInstanceId');
        $deviceInstance = null;
        $deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($instanceId);
        $deviceInstance->processOverrides($this->_hardwareOptimization->getHardwareOptimizationSetting()->adminCostPerPage);

        $replacementDevice    = $deviceInstance->getReplacementMasterDevice();
        $hasReplacementDevice = ($replacementDevice instanceof Proposalgen_Model_MasterDevice);

        $device = array(
            "deviceInstance" => array(
                "deviceName"            => "{$deviceInstance->getMasterDevice()->getManufacturer()->fullname} {$deviceInstance->getMasterDevice()->modelName}",
                "ipAddress"             => $deviceInstance->ipAddress,
                "isColor"               => (int)$deviceInstance->getMasterDevice()->isColor(),
                "serialNumber"          => $deviceInstance->serialNumber,
                "lifePageCount"         => number_format($deviceInstance->getLifePageCount()),
                "monoAmpv"              => number_format($deviceInstance->getAverageMonthlyBlackAndWhitePageCount()),
                "colorAmpv"             => number_format($deviceInstance->getAverageMonthlyColorPageCount()),
                "costPerPageMonochrome" => $this->view->currency((float)$deviceInstance->calculateCostPerPage($costPerPageSetting)
                    ->monochromeCostPerPage, array(
                                                  "precision" => 4
                                             )),
                "costPerPageColor"      => $this->view->currency((float)$deviceInstance->calculateCostPerPage($costPerPageSetting)
                    ->colorCostPerPage, array(
                                             "precision" => 4
                                        )),
                "jitSuppliesSupported"  => (int)$deviceInstance->reportsTonerLevels,
                "isCopy"                => (int)$deviceInstance->getMasterDevice()->isCopier,
                "isFax"                 => (int)$deviceInstance->getMasterDevice()->isFax,
                "isScan"                => (int)$deviceInstance->getMasterDevice()->isScanner,
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
                "costPerPageMonochrome" => $this->view->currency((float)$deviceInstance->calculateCostPerPage($costPerPageSetting, $replacementDevice)
                    ->monochromeCostPerPage, array(
                                                  "precision" => 4
                                             )),
                "costPerPageColor"      => $this->view->currency((float)$deviceInstance->calculateCostPerPage($costPerPageSetting, $replacementDevice)
                    ->colorCostPerPage, array(
                                             "precision" => 4
                                        )),
                "isCopy"                => (int)$replacementDevice->isCopier,
                "isFax"                 => (int)$replacementDevice->isFax,
                "isScan"                => (int)$replacementDevice->isScanner,
                "ppmBlack"              => ($replacementDevice->ppmBlack > 0) ? number_format($replacementDevice->ppmBlack) : 'N/A',
                "ppmColor"              => ($replacementDevice->ppmColor > 0) ? number_format($replacementDevice->ppmColor) : 'N/A',
                "reason"                => 'Reason preset, change me.'
            );
        }

        $this->_helper->json($device);
    }

    /**
     * Processes the data returned from the form to apply replacements to a customers fleet.
     *
     * @param Proposalgen_Form_DeviceSwapChoice $form
     *
     * @return bool
     */
    protected function _processSaveProfitability ($form)
    {

        $optimization                    = $this->getOptimizationViewModel();
        $deviceInstanceReplacementMapper = Proposalgen_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance();
        $db                              = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try
        {
            /*
             * Loop through our devices
             */
            foreach ($optimization->getPurchasedDevices() as $deviceInstance)
            {
                $masterDeviceId = $form->getValue("deviceInstance_{$deviceInstance->id}");

                // Only bother with posted data
                if ($masterDeviceId === null)
                {
                    continue;
                }

                $currentReplacementMasterDevice = $deviceInstance->getReplacementMasterDevice();
                /*
                 * We should only save if we have a new master device id and the device isn't supposed to be retired
                 */
                if ((int)$masterDeviceId !== 0 && $deviceInstance->getAction() !== Proposalgen_Model_DeviceInstance::ACTION_RETIRE)
                {
                    if ($currentReplacementMasterDevice && (int)$currentReplacementMasterDevice->id === (int)$masterDeviceId)
                    {
                        continue;
                    }
                    // Save / update replacement devices
                    $deviceInstanceReplacement = $deviceInstanceReplacementMapper->find($deviceInstance->id);
                    if ($deviceInstanceReplacement instanceof Proposalgen_Model_Device_Instance_Replacement_Master_Device)
                    {
                        $deviceInstanceReplacement->masterDeviceId = $masterDeviceId;
                        // Is this necessary ?
                        $deviceInstanceReplacement->setMasterDevice(null);
                        $deviceInstanceReplacementMapper->save($deviceInstanceReplacement);
                        $deviceInstance->setReplacementMasterDevice(null);
                    }
                    else
                    {
                        $deviceInstanceReplacement                         = new Proposalgen_Model_Device_Instance_Replacement_Master_Device();
                        $deviceInstanceReplacement->masterDeviceId         = (int)$masterDeviceId;
                        $deviceInstanceReplacement->deviceInstanceId       = $deviceInstance->id;
                        $deviceInstanceReplacement->hardwareOptimizationId = $this->_dealerId;
                        $deviceInstanceReplacementMapper->insert($deviceInstanceReplacement);
                    }
                }
                else
                    // 1635
                {
                    if ($currentReplacementMasterDevice)
                    {
                        $deviceInstanceReplacement                   = new Proposalgen_Model_Device_Instance_Replacement_Master_Device();
                        $deviceInstanceReplacement->deviceInstanceId = $deviceInstance->id;
                        $deviceInstanceReplacementMapper->delete($deviceInstanceReplacement);
                        // Make sure the instance doesn't remember the replacement device
                        $deviceInstance->setReplacementMasterDevice(null);
                    }
                }
            }

            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
            My_Log::logException($e);

            return false;
        }

        return true;
    }

    /**
     * Finds a suitable replacement for a device instance or returns null if no replacement was found
     *
     * @param Proposalgen_Model_DeviceInstance     $deviceInstance
     * @param Proposalgen_Model_MasterDevice[]     $replacementDevices
     * @param Proposalgen_Model_CostPerPageSetting $costPerPageSetting
     * @param Proposalgen_Model_CostPerPageSetting $replacementCostPerPageSetting
     * @param number                               $costSavingsThreshold
     *
     * @return Proposalgen_Model_MasterDevice
     */
    protected function _findReplacement (Proposalgen_Model_DeviceInstance $deviceInstance, $replacementDevices, Proposalgen_Model_CostPerPageSetting $costPerPageSetting, Proposalgen_Model_CostPerPageSetting $replacementCostPerPageSetting, $costSavingsThreshold)
    {
        $suggestedDevice           = null;
        $greatestSavings           = 0;
        $deviceInstanceMonthlyCost = $deviceInstance->calculateMonthlyCost($costPerPageSetting);

        /* @var $replacementDevice Proposalgen_Model_MasterDevice */
        foreach ($replacementDevices as $masterDevice)
        {
            $deviceReplacementCost = $deviceInstance->calculateMonthlyCost($replacementCostPerPageSetting, $masterDevice);
            $costDelta             = ($deviceInstanceMonthlyCost - $deviceReplacementCost);
            if ($costDelta > $costSavingsThreshold && $costDelta > $greatestSavings)
            {
                $suggestedDevice = $masterDevice;
                $greatestSavings = $costDelta;
            }
        }

        return $suggestedDevice;
    }

    /**
     * Analyzes the customers fleet and uses a threshold to determine which devices to automatically replace
     */
    protected function _analyzeFleet ()
    {
        $db                              = Zend_Db_Table::getDefaultAdapter();
        $optimization                    = $this->getOptimizationViewModel();
        $savingsThreshold                = $this->_hardwareOptimization->getHardwareOptimizationSetting()->costThreshold;
        $deviceInstanceReplacementMapper = Proposalgen_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance();

        try
        {
            $db->beginTransaction();

            // Delete all our replacements
            if (!$this->_resetReplacements())
            {
                throw new Exception("Error resetting replacements!");
            }

            $blackReplacementDevices    = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getBlackReplacementDevices($this->_dealerId, false);
            $blackMfpReplacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getBlackMfpReplacementDevices($this->_dealerId, false);
            $colorReplacementDevices    = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getColorReplacementDevices($this->_dealerId, false);
            $colorMfpReplacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getColorMfpReplacementDevices($this->_dealerId, false);

            /* @var $replacementMasterDevice Proposalgen_Model_MasterDevice */
            foreach (array_merge($blackReplacementDevices, $blackMfpReplacementDevices, $colorReplacementDevices, $colorMfpReplacementDevices) as $replacementMasterDevice)
            {
                $replacementMasterDevice->processOverrides($this->_hardwareOptimization->getHardwareOptimizationSetting()->adminCostPerPage);
            }

            $costPerPageSetting            = $optimization->getCostPerPageSettingForDealer();
            $replacementCostPerPageSetting = $optimization->getCostPerPageSettingForReplacements();

            foreach ($optimization->getPurchasedDevices() as $deviceInstance)
            {
                $suggestedDevice = null;

                if ($deviceInstance->getMasterDevice()->tonerConfigId === Proposalgen_Model_TonerConfig::BLACK_ONLY)
                {
                    if ($deviceInstance->getMasterDevice()->isCopier)
                    {
                        $suggestedDevice = $this->_findReplacement($deviceInstance, $blackMfpReplacementDevices, $costPerPageSetting, $replacementCostPerPageSetting, $savingsThreshold);
                    }
                    else
                    {
                        $suggestedDevice = $this->_findReplacement($deviceInstance, $blackReplacementDevices, $costPerPageSetting, $replacementCostPerPageSetting, $savingsThreshold);
                    }
                }
                else
                {
                    if ($deviceInstance->getMasterDevice()->isCopier)
                    {
                        $suggestedDevice = $this->_findReplacement($deviceInstance, $colorMfpReplacementDevices, $costPerPageSetting, $replacementCostPerPageSetting, $savingsThreshold);
                    }
                    else
                    {
                        $suggestedDevice = $this->_findReplacement($deviceInstance, $colorReplacementDevices, $costPerPageSetting, $replacementCostPerPageSetting, $savingsThreshold);
                    }
                }

                if ($suggestedDevice instanceof Proposalgen_Model_MasterDevice)
                {
                    $newDevice                         = new Proposalgen_Model_Device_Instance_Replacement_Master_Device();
                    $newDevice->masterDeviceId         = $suggestedDevice->id;
                    $newDevice->deviceInstanceId       = $deviceInstance->id;
                    $newDevice->hardwareOptimizationId = $this->_hardwareOptimization->id;

                    $deviceInstanceReplacementMapper->insert($newDevice);
                }
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();

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
}