<?php
use MPSToolbox\Legacy\Mappers\UserMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\OptimizeActionsForm;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\HardwareOptimizationNavigationForm;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\DeviceSwapReasonDefaultMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationDeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers\HardwareOptimizationMapper;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonCategoryModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\OptimizationStandardDeviceReplacementModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationStepsModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationDeviceInstanceModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\DeviceSwapChoiceForm;
use MPSToolbox\Legacy\Modules\Assessment\Forms\AssessmentNavigationForm;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceReplacementMasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\SelectRmsUploadService;
use Tangent\Service\JQGrid;

/**
 * Class Hardwareoptimization_IndexController
 */
class Hardwareoptimization_IndexController extends Hardwareoptimization_Library_Controller_Action
{
    /**
     * @var \MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\DeviceSwapChoiceForm
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
        $this->_pageTitle = array('Hardware Optimization', 'Select Upload');

        $this->_navigation->setActiveStep(HardwareOptimizationStepsModel::STEP_FLEET_UPLOAD);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (isset($postData['selectRmsUploadId']))
            {
                $selectRmsUploadService = new SelectRmsUploadService($this->_mpsSession->selectedClientId);
                $rmsUpload              = $selectRmsUploadService->validateRmsUploadId($postData['selectRmsUploadId']);
                if ($rmsUpload instanceof RmsUploadModel)
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
                $this->redirectToRoute('rms-upload.upload-file');
            }
            if ($this->getHardwareOptimization()->rmsUploadId > 0)
            {
                if (isset($postData['saveAndContinue']))
                {
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
        }

        $this->view->numberOfUploads = count(RmsUploadMapper::getInstance()->fetchAllForClient($this->getHardwareOptimization()->clientId));
        $this->view->rmsUpload       = $this->getHardwareOptimization()->getRmsUpload();
        $this->view->navigationForm  = new HardwareOptimizationNavigationForm(HardwareOptimizationNavigationForm::BUTTONS_NEXT);
    }

    /**
     * Handles displaying and saving of settings
     */
    public function settingsAction ()
    {
        $this->_pageTitle = array('Hardware Optimization', 'Settings');
        $this->_navigation->setActiveStep(HardwareOptimizationStepsModel::STEP_SETTINGS);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (!isset($postData['goBack']))
            {
                $this->saveClientSettingsForm($postData);
                $this->saveHardwareOptimization();

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
        else
        {
            $this->showClientSettingsForm();
        }
    }

    /**
     * This handles displaying and auto optimizing a fleet
     */
    public function optimizeAction ()
    {
        $this->_pageTitle = array('Hardware Optimization', 'Optimize');
        // Mark the step we're on as active
        $this->_navigation->setActiveStep(HardwareOptimizationStepsModel::STEP_OPTIMIZE);

        // $form = $this->getDeviceSwapForm($this->getDeviceViewModel()->purchasedDeviceInstances->getDeviceInstances());
        $form = new OptimizeActionsForm();

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
                        $this->redirectToRoute('hardwareoptimization.optimization');
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
                        $this->redirectToRoute('hardwareoptimization.optimization');
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
        $this->view->hardwareOptimization = $this->getHardwareOptimization();
        $this->view->deviceActionCount    = HardwareOptimizationDeviceInstanceMapper::getInstance()->deviceActionCounts($this->getHardwareOptimization()->id);
        $this->view->navigationForm       = new AssessmentNavigationForm(AssessmentNavigationForm::BUTTONS_BACK_NEXT);
    }

    /**
     * Analyzes the customers fleet and uses a savings threshold to determine which devices to automatically replace
     */
    protected function _analyzeFleet ()
    {
        $db                                       = Zend_Db_Table::getDefaultAdapter();
        $optimization                             = $this->getOptimizationViewModel();
        $hardwareOptimizationDeviceInstanceMapper = HardwareOptimizationDeviceInstanceMapper::getInstance();

        try
        {
            $db->beginTransaction();

            // Delete all our replacements
            if (!$this->_resetReplacements())
            {
                throw new Exception("Error resetting replacements!");
            }

            $doFunctionalityUpgrade = $this->_hardwareOptimization->getClient()->getClientSettings()->optimizationSettings->autoOptimizeFunctionality;

            $blackReplacementDevices    = DeviceSwapMapper::getInstance()->getBlackReplacementDevices($this->_dealerId, true, $doFunctionalityUpgrade);
            $blackMfpReplacementDevices = DeviceSwapMapper::getInstance()->getBlackMfpReplacementDevices($this->_dealerId, $doFunctionalityUpgrade);
            $colorReplacementDevices    = DeviceSwapMapper::getInstance()->getColorReplacementDevices($this->_dealerId, true);
            $colorMfpReplacementDevices = DeviceSwapMapper::getInstance()->getColorMfpReplacementDevices($this->_dealerId);

            $standardDeviceReplacement = new OptimizationStandardDeviceReplacementModel(
                array(
                    'black'    => $blackReplacementDevices,
                    'blackmfp' => $blackMfpReplacementDevices,
                    'color'    => $colorReplacementDevices,
                    'colormfp' => $colorMfpReplacementDevices
                ),
                $this->_dealerId,
                $this->_hardwareOptimization->getClient()->getClientSettings()->optimizationSettings->costThreshold,
                $optimization->getCostPerPageSettingForDealer(),
                $optimization->getCostPerPageSettingForReplacements(),
                $this->_hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromeLaborCostPerPage,
                $this->_hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromePartsCostPerPage
            );


            $blackReplacementDevices    = DeviceSwapMapper::getInstance()->getBlackReplacementDevices($this->_dealerId, false, $doFunctionalityUpgrade);
            $blackMfpReplacementDevices = DeviceSwapMapper::getInstance()->getBlackMfpReplacementDevices($this->_dealerId, $doFunctionalityUpgrade);
            $colorReplacementDevices    = DeviceSwapMapper::getInstance()->getColorReplacementDevices($this->_dealerId, false);
            $colorMfpReplacementDevices = DeviceSwapMapper::getInstance()->getColorMfpReplacementDevices($this->_dealerId);

            $functionalityDeviceReplacement = new \MPSToolbox\Legacy\Modules\HardwareOptimization\Models\OptimizationFunctionalityDeviceReplacementModel(
                array(
                    'black'    => $blackReplacementDevices,
                    'blackmfp' => $blackMfpReplacementDevices,
                    'color'    => $colorReplacementDevices,
                    'colormfp' => $colorMfpReplacementDevices
                ),
                $this->_dealerId,
                $this->_hardwareOptimization->getClient()->getClientSettings()->optimizationSettings->lossThreshold,
                $this->_hardwareOptimization->getClient()->getClientSettings()->optimizationSettings->costThreshold,
                $optimization->getCostPerPageSettingForDealer(),
                $optimization->getCostPerPageSettingForReplacements(),
                $this->_hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromeLaborCostPerPage,
                $this->_hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromePartsCostPerPage,
                $this->_hardwareOptimization->getClient()->getClientSettings()->optimizationSettings->blackToColorRatio
            );

            foreach ($optimization->getDevices()->purchasedDeviceInstances->getDeviceInstances() as $deviceInstance)
            {
                $functionalityUpgradeAttempted = false;

                $suggestedDevice = $standardDeviceReplacement->findReplacement($deviceInstance);

                /**
                 * Optimize by functionality if we don't have a cheap device to use
                 */
                if (!$suggestedDevice instanceof MasterDeviceModel && $doFunctionalityUpgrade)
                {
                    $suggestedDevice               = $functionalityDeviceReplacement->findReplacement($deviceInstance);
                    $functionalityUpgradeAttempted = true;
                }

                $hardwareOptimizationDeviceInstance = $deviceInstance->getHardwareOptimizationDeviceInstance($this->_hardwareOptimization->id);

                if ($suggestedDevice instanceof MasterDeviceModel)
                {
                    if ($functionalityUpgradeAttempted)
                    {
                        $hardwareOptimizationDeviceInstance->action = HardwareOptimizationDeviceInstanceModel::ACTION_UPGRADE;
                    }
                    else
                    {
                        $hardwareOptimizationDeviceInstance->action = HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE;
                    }
                    $hardwareOptimizationDeviceInstance->masterDeviceId = $suggestedDevice->id;
                }
                else
                {
                    $action = $deviceInstance->getAction($optimization->getCostPerPageSettingForDealer());
                    switch ($action)
                    {
                        case DeviceInstanceModel::ACTION_KEEP:
                            $hardwareOptimizationDeviceInstance->action = HardwareOptimizationDeviceInstanceModel::ACTION_KEEP;
                            break;
                        case DeviceInstanceModel::ACTION_RETIRE:
                            $hardwareOptimizationDeviceInstance->action = HardwareOptimizationDeviceInstanceModel::ACTION_RETIRE;
                            break;
                        case DeviceInstanceModel::ACTION_REPLACE:
                            $hardwareOptimizationDeviceInstance->action = HardwareOptimizationDeviceInstanceModel::ACTION_DNR;
                            break;
                    }
                }

                $categoryId         = $deviceInstance->getDefaultDeviceSwapReasonCategoryId($this->_hardwareOptimization->id);
                $deviceSwapReasonId = DeviceSwapReasonDefaultMapper::getInstance()->findDefaultByDealerId($categoryId, $this->_identity->dealerId)->deviceSwapReasonId;

                $hardwareOptimizationDeviceInstance->deviceSwapReasonId = $deviceSwapReasonId;

                $hardwareOptimizationDeviceInstanceMapper->save($hardwareOptimizationDeviceInstance);
            }

            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
            \Tangent\Logger\Logger::logException($e);

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
            DeviceInstanceReplacementMasterDeviceMapper::getInstance()->deleteAllDeviceInstanceReplacementsByHardwareOptimizationId($this->_hardwareOptimization->id);
            HardwareOptimizationDeviceInstanceMapper::getInstance()->resetAllForHardwareOptimization($this->_hardwareOptimization->id);
        }
        catch (Exception $e)
        {
            \Tangent\Logger\Logger::logException($e);

            return false;
        }

        return true;
    }

    /**
     * Returns Json based on id that has been passed via query string
     */
    public function getDeviceByDeviceInstanceIdAction ()
    {
        MasterDeviceModel::$ReportLaborCostPerPage = $this->_hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromeLaborCostPerPage;
        MasterDeviceModel::$ReportPartsCostPerPage = $this->_hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromePartsCostPerPage;

        $optimization                  = $this->getOptimizationViewModel();
        $costPerPageSetting            = $optimization->getCostPerPageSettingForDealer();
        $replacementCostPerPageSetting = $optimization->getCostPerPageSettingForReplacements();

        $instanceId     = $this->_getParam('deviceInstanceId');
        $deviceInstance = null;
        $deviceInstance = DeviceInstanceMapper::getInstance()->find($instanceId);

        $hardwareOptimizationDeviceInstance = $deviceInstance->getHardwareOptimizationDeviceInstance($this->_hardwareOptimization->id);
        $replacementDevice                  = $hardwareOptimizationDeviceInstance->getMasterDevice();

        $hasReplacementDevice = ($replacementDevice instanceof MasterDeviceModel);

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
                "ppmColor"               => ($deviceInstance->getMasterDevice()->ppmColor > 0) ? number_format($deviceInstance->getMasterDevice()->ppmColor) : 'N/A',
                "location"               => (strlen($deviceInstance->location) > 0) ? $deviceInstance->location : 'N/A',
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
        $hardwareOptimizationDeviceInstanceMapper = HardwareOptimizationDeviceInstanceMapper::getInstance();

        // Setup the required mappers
        $optimization                = $this->getOptimizationViewModel();
        $deviceInstanceReasonElement = null;

        // Setup the master device labor and parts cost per page
        MasterDeviceModel::$ReportLaborCostPerPage = $this->_hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromeLaborCostPerPage;
        MasterDeviceModel::$ReportPartsCostPerPage = $this->_hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromePartsCostPerPage;

        /**
         * Require a device instance
         */
        $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
        if ($deviceInstanceId === false)
        {
            $this->sendJsonError("Invalid data passed.");
        }

        $deviceInstance = DeviceInstanceMapper::getInstance()->find($deviceInstanceId);

        // Check if device belongs to RMS
        if (!$deviceInstance instanceof DeviceInstanceModel)
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

        /**
         * A replacement device id must have come through
         */
        if ($replacementDeviceId === false)
        {
            $this->sendJsonError("Invalid action selected.");
        }

        if ((int)$replacementDeviceId > 0)
        {
            /**
             * REPLACE
             */
            $deviceSwap = DeviceSwapMapper::getInstance()->find(array($replacementDeviceId, $this->_identity->dealerId));

            if ($deviceSwap instanceof DeviceSwapModel)
            {
                $hardwareOptimizationDeviceInstance->action         = HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE;
                $hardwareOptimizationDeviceInstance->masterDeviceId = $replacementDeviceId;

                /**
                 * Recalculate the cost deltas
                 */
                $replacementMasterDevice = MasterDeviceMapper::getInstance()->findForReports($replacementDeviceId,
                    $this->_identity->dealerId,
                    $this->_hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromeLaborCostPerPage,
                    $this->_hardwareOptimization->getClient()->getClientSettings()->proposedFleetSettings->defaultMonochromePartsCostPerPage
                );


                $costDelta = $deviceInstance->calculateMonthlyCost($optimization->getCostPerPageSettingForDealer()) -
                             $deviceInstance->calculateMonthlyCost($optimization->getCostPerPageSettingForReplacements(), $replacementMasterDevice);

                if ($costDelta < $this->_hardwareOptimization->getClient()->getClientSettings()->optimizationSettings->costThreshold)
                {
                    $hardwareOptimizationDeviceInstance->action = HardwareOptimizationDeviceInstanceModel::ACTION_UPGRADE;
                }


                /**
                 * Update the reason to match the default
                 */
                $deviceSwapReasonId = DeviceSwapReasonDefaultMapper::getInstance()->findDefaultByDealerId($deviceInstance->getDefaultDeviceSwapReasonCategoryId($this->_hardwareOptimization->id), $this->_identity->dealerId)->deviceSwapReasonId;

                $hardwareOptimizationDeviceInstance->deviceSwapReasonId = $deviceSwapReasonId;

                $form                        = new DeviceSwapChoiceForm(array($deviceInstance), $this->_dealerId, $this->_hardwareOptimization->id);
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
                case HardwareOptimizationDeviceInstanceModel::ACTION_KEEP:
                case HardwareOptimizationDeviceInstanceModel::ACTION_RETIRE:
                case HardwareOptimizationDeviceInstanceModel::ACTION_DNR:
                    $hardwareOptimizationDeviceInstance->action         = $replacementDeviceId;
                    $hardwareOptimizationDeviceInstance->masterDeviceId = new Zend_Db_Expr("NULL");
                    break;
                default:
                    $this->sendJsonError("Invalid action selected.");
                    break;
            }

            if ($hardwareOptimizationDeviceInstance->action === HardwareOptimizationDeviceInstanceModel::ACTION_DNR)
            {

                /**
                 * Update the reason to match the default
                 */
                $deviceSwapReasonId = DeviceSwapReasonDefaultMapper::getInstance()->findDefaultByDealerId($deviceInstance->getDefaultDeviceSwapReasonCategoryId($this->_hardwareOptimization->id), $this->_identity->dealerId)->deviceSwapReasonId;

                $hardwareOptimizationDeviceInstance->deviceSwapReasonId = $deviceSwapReasonId;

                $form                        = new DeviceSwapChoiceForm(array($deviceInstance), $this->_dealerId, $this->_hardwareOptimization->id);
                $deviceInstanceReasonElement = $form->getElement("deviceInstanceReason_" . $deviceInstanceId);
            }
        }

        /**
         * Save the data
         */
        $hardwareOptimizationDeviceInstanceMapper->save($hardwareOptimizationDeviceInstance);

        $devicesGroupedByAction = $optimization->getDevicesGroupedByAction();

        /**
         * We're sending back updated numbers so that we can update the page with the new calculations
         * Monochrome CPP, Color CPP, Total Cost, Margin $, Margin %
         */
        $this->sendJson([
            "summary"           => [
                "current"   => [
                    "monochromeCpp"        => $optimization->calculateDealerWeightedAverageMonthlyCostPerPage()->monochromeCostPerPage,
                    "monochromePageVolume" => $optimization->getPageCounts()->getBlackPageCount()->getMonthly(),
                    "colorCpp"             => $optimization->calculateDealerWeightedAverageMonthlyCostPerPage()->colorCostPerPage,
                    "colorPageVolume"      => $optimization->getPageCounts()->getColorPageCount()->getMonthly(),
                    "totalCost"            => $optimization->calculateDealerMonthlyCost(),
                    "totalRevenue"         => $optimization->calculateDealerMonthlyRevenueUsingTargetCostPerPage(),
                    "marginDollar"         => $optimization->calculateDealerMonthlyProfitUsingTargetCostPerPage(),
                    "marginPercent"        => \Tangent\Accounting::reverseEngineerMargin((float)$optimization->calculateDealerMonthlyCost(), (float)$optimization->calculateDealerMonthlyRevenueUsingTargetCostPerPage()) / 100,
                ],
                "optimized" => [
                    "monochromeCpp"        => $optimization->calculateDealerWeightedAverageMonthlyCostPerPageWithReplacements()->monochromeCostPerPage,
                    "monochromePageVolume" => $optimization->getNewPageCounts()->getBlackPageCount()->getMonthly(),
                    "colorCpp"             => $optimization->calculateDealerWeightedAverageMonthlyCostPerPageWithReplacements()->colorCostPerPage,
                    "colorPageVolume"      => $optimization->getNewPageCounts()->getColorPageCount()->getMonthly(),
                    "totalCost"            => $optimization->calculateDealerMonthlyCostWithReplacements(),
                    "totalRevenue"         => $optimization->calculateOptimizedDealerMonthlyRevenueUsingTargetCostPerPage(),
                    "marginDollar"         => $optimization->calculateDealerMonthlyProfitUsingTargetCostPerPageAndReplacements(),
                    "marginPercent"        => \Tangent\Accounting::reverseEngineerMargin((float)$optimization->calculateDealerMonthlyCostWithReplacements(), (float)$optimization->calculateOptimizedDealerMonthlyRevenueUsingTargetCostPerPage()) / 100,
                ],
            ],
            "costDelta"         => (float)$costDelta,
            "replaceReason"     => ($deviceInstanceReasonElement !== null) ? $deviceInstanceReasonElement->renderViewHelper() : " ",
            "deviceActionCount" => array(
                "keep"    => number_format(count($devicesGroupedByAction[HardwareOptimizationDeviceInstanceModel::ACTION_KEEP])),
                "retire"  => number_format(count($devicesGroupedByAction[HardwareOptimizationDeviceInstanceModel::ACTION_RETIRE])),
                "replace" => number_format(count($devicesGroupedByAction[HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE])),
                "upgrade" => number_format(count($devicesGroupedByAction[HardwareOptimizationDeviceInstanceModel::ACTION_UPGRADE])),
                "dnr"     => number_format(count($devicesGroupedByAction[HardwareOptimizationDeviceInstanceModel::ACTION_DNR])),
                "total"   => $optimization->getDevices()->purchasedDeviceInstances->getCount(),
            ),
        ]);

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
        $jqGridService              = new JQGrid();
        $hardwareoptimizationMapper = HardwareOptimizationMapper::getInstance();

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
        $jqGridService->setRecordCount($hardwareoptimizationMapper->fetchAllForHardwareOptimization($this->_hardwareOptimization->id, null, null, null, null, true));

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
        $rows = $hardwareoptimizationMapper->fetchAllForHardwareOptimization($this->_hardwareOptimization->id, $this->getOptimizationViewModel()->getCostPerPageSettingForDealer(), $this->getOptimizationViewModel()->getCostPerPageSettingForReplacements(), $jqGridService->getRecordsPerPage(), $startRecord);

        /* @var $deviceInstances DeviceInstanceModel [] */
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
        $hardwareOptimizationDeviceInstanceMapper = HardwareOptimizationDeviceInstanceMapper::getInstance();
        $deviceSwapReasonMapper                   = DeviceSwapReasonMapper::getInstance();

        /**
         * Require a device instance
         */
        $deviceInstanceId = $this->_getParam("deviceInstanceId", false);
        if ($deviceInstanceId === false)
        {
            $this->sendJsonError("Invalid data passed.");
        }

        $deviceInstance = DeviceInstanceMapper::getInstance()->find($deviceInstanceId);

        // Check if device belongs to RMS
        if (!$deviceInstance instanceof DeviceInstanceModel)
        {
            $this->sendJsonError("Invalid device selected.");
        }

        if ($this->_hardwareOptimization->rmsUploadId !== $deviceInstance->rmsUploadId)
        {
            $this->sendJsonError("You do not have permission to edit this device instance.");
        }

        $replacementReasonId = $this->_getParam("replacementReasonId");
        $replacementReason   = $deviceSwapReasonMapper->find($replacementReasonId);

        if (!$replacementReason instanceof DeviceSwapReasonModel)
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
            \Tangent\Logger\Logger::logException($e);
            $this->sendJsonError("Error saving device reason. Please try again. #" . \Tangent\Logger\Logger::getUniqueId());
        }

        $this->sendJson(array("success" => "Reason Saved!"));
    }

    /**
     * Getter for _deviceSwapForm
     *
     * @param $devices
     *
     * @return \MPSToolbox\Legacy\Modules\HardwareOptimization\Forms\DeviceSwapChoiceForm
     */
    public function getDeviceSwapForm ($devices)
    {
        if (!isset($this->_deviceSwapForm))
        {
            $this->_deviceSwapForm = new DeviceSwapChoiceForm($devices, $this->_identity->dealerId, $this->_hardwareOptimization->id);
        }

        return $this->_deviceSwapForm;
    }
}