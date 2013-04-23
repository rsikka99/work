<?php
class Hardwareoptimization_IndexController extends Hardwareoptimization_Library_Controller_Action
{

    /**
     * This action takes care of selecting an upload
     */
    public function indexAction ()
    {
        $this->redirectToLatestStep();
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
        $user = Application_Model_Mapper_User::getInstance()->find($this->_identity->id);

        $hardwareOptimizationService = new Preferences_Service_HardwareoptimizationSetting($this->_hardwareOptimization->getHardwareOptimizationSetting()->toArray());

        $defaultHardwareOptimizationSettings = $user->getDealer()->getDealerSettings()->getHardwareOptimizationSettings();
        $defaultHardwareOptimizationSettings->populate($user->getUserSettings()->getHardwareOptimizationSettings()->toArray());
        $this->view->form = $hardwareOptimizationService->getFormWithDefaults($defaultHardwareOptimizationSettings->toArray());
    }
}