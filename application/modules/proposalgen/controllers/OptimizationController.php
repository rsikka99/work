<?php
class Proposalgen_OptimizationController extends Proposalgen_Library_Controller_Proposal
{
    public function indexAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_OPTIMIZATION);
        // Get all devices

        // Every time we save anything related to a report, we should save it (updates the modification date)
        $this->saveReport();
        $form = new Proposalgen_Form_DeviceSwapChoice($this->getProposal());

        // Get all devices
        $devices = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchDevicesInstancesForMapping($this->_report->id, 'reportId', 'asc');

        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();
            if ($form->isValid($postData))
            {

                if ($form->getValue('Submit'))
                {

                    if ($this->_processSaveProfitability($form))
                    {
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
                        $this->_helper->redirector('index', null, null, array());
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
                        $this->_helper->redirector('index', null, null, array());
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array(
                                                            'danger' => "There was an error resetting your replacement choices."
                                                       ));
                    }
                }
            }
        }

        $this->view->form     = $form;
        $this->view->devices  = $devices;
        $this->view->proposal = $this->getProposal();

        // Every time we save anything related to a report, we should save it (updates the modification date)
//        $this->saveReport();
        // Call the base controller to send us to the next logical step in the proposal.
//        $this->gotoNextStep();

        $this->view->title = 'Hardware Optimization';
    }


    /**
     * Returns Json based on id that has been pased via query string
     *
     * @return data <json>
     */
    public function getDeviceByDeviceInstanceIdAction ()
    {
        $proposal           = $this->getProposal();
        $costPerPageSetting = $proposal->getCostPerPageSettingForDealer();

        $instanceId     = $this->_getParam('deviceInstanceId');
        $deviceInstance = null;
        $deviceInstance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($instanceId);
        $deviceInstance->processOverrides($this->Report);

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

        $proposal                        = $this->getProposal();
        $deviceInstanceReplacementMapper = Proposalgen_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance();
        $db                              = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try
        {
            /*
             * Loop through our devices
             */
            foreach ($proposal->getPurchasedDevices() as $deviceInstance)
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
                if ((int)$masterDeviceId !== 0 && $deviceInstance->getAction() !== 'Retire')
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
                        $deviceInstanceReplacement                   = new Proposalgen_Model_Device_Instance_Replacement_Master_Device();
                        $deviceInstanceReplacement->masterDeviceId   = (int)$masterDeviceId;
                        $deviceInstanceReplacement->deviceInstanceId = $deviceInstance->id;
                        $deviceInstanceReplacementMapper->insert($deviceInstanceReplacement);
                    }

                }
                else
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
     * @param number                               $costSavingsThreshold
     *
     * @return Proposalgen_Model_MasterDevice
     */
    protected function _findReplacement (Proposalgen_Model_DeviceInstance $deviceInstance, $replacementDevices, Proposalgen_Model_CostPerPageSetting $costPerPageSetting, $costSavingsThreshold)
    {
        $suggestedDevice           = null;
        $greatestSavings           = 0;
        $deviceInstanceMonthlyCost = $deviceInstance->calculateMonthlyCost($costPerPageSetting);

        /* @var $replacementDevice Proposalgen_Model_MasterDevice */
        foreach ($replacementDevices as $masterDevice)
        {
            $costDelta = ($deviceInstanceMonthlyCost - $deviceInstance->calculateMonthlyCost($costPerPageSetting, $masterDevice));
            if ($costDelta > $costSavingsThreshold && $costDelta > $greatestSavings)
            {
                $suggestedDevice = $masterDevice;
            }
        }

        return $suggestedDevice;
    }

    /**
     * Analyzes the customers fleet and uses a threshold to determine which devices to automatically replace
     */
    protected function _analyzeFleet ()
    {
        $db       = Zend_Db_Table::getDefaultAdapter();
        $proposal = $this->getProposal();

        $savingsThreshold                = $proposal->report->getReportSettings()->costThreshold;
        $deviceInstanceReplacementMapper = Proposalgen_Model_Mapper_Device_Instance_Replacement_Master_Device::getInstance();
        try
        {
            $db->beginTransaction();

            // Delete all our replacements
            if (!$this->_resetReplacements())
            {
                throw new Exception("Error resetting replacements!");
            }

            $idCount                    = array();
            $blackReplacementDevices    = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getBlackReplacementDevices(false);
            $blackMfpReplacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getBlackMfpReplacementDevices(false);
            $colorReplacementDevices    = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getColorReplacementDevices(false);
            $colorMfpReplacementDevices = Proposalgen_Model_Mapper_ReplacementDevice::getInstance()->getColorMfpReplacementDevices(false);

            $costPerPageSetting = $proposal->getCostPerPageSettingForDealer();

            foreach ($proposal->getPurchasedDevices() as $deviceInstance)
            {

                $suggestedDevice = null;

                if ($deviceInstance->getMasterDevice()->tonerConfigId === Proposalgen_Model_TonerConfig::BLACK_ONLY)
                {
                    if ($deviceInstance->getMasterDevice()->isCopier)
                    {
                        $suggestedDevice = $this->_findReplacement($deviceInstance, $blackMfpReplacementDevices, $costPerPageSetting, $savingsThreshold);
                    }
                    else
                    {
                        $suggestedDevice = $this->_findReplacement($deviceInstance, $blackReplacementDevices, $costPerPageSetting, $savingsThreshold);
                    }
                }
                else
                {
                    if ($deviceInstance->getMasterDevice()->isCopier)
                    {
                        $suggestedDevice = $this->_findReplacement($deviceInstance, $colorMfpReplacementDevices, $costPerPageSetting, $savingsThreshold);
                    }
                    else
                    {
                        $suggestedDevice = $this->_findReplacement($deviceInstance, $colorReplacementDevices, $costPerPageSetting, $savingsThreshold);
                    }
                }

                if ($suggestedDevice instanceof Proposalgen_Model_MasterDevice)
                {

                    $newDevice                   = new Proposalgen_Model_Device_Instance_Replacement_Master_Device();
                    $newDevice->masterDeviceId   = $suggestedDevice->id;
                    $newDevice->deviceInstanceId = $deviceInstance->id;
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
            $deviceInstanceReplacementMapper->deleteAllDeviceInstancesForReport($this->getReport()->id);
        }
        catch (Exception $e)
        {
            return false;
        }

        return true;
    }


}