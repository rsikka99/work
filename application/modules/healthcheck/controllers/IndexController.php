<?php
use MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckStepsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\SelectRmsUploadService;

/**
 * Class Healthcheck_IndexController
 */
class Healthcheck_IndexController extends Healthcheck_Library_Controller_Action
{
    /**
     * This action will redirect us to the latest available step
     */
    public function indexAction ()
    {
        $this->redirectToLatestStep($this->getHealthcheck()->stepName);
    }

    /**
     * Selects a upload to use for the Healthcheck
     */
    public function selectUploadAction ()
    {
        $this->_pageTitle = array('Healthcheck', 'Select Upload');
        // Mark the step we're on as active
        $this->_navigation->setActiveStep(HealthCheckStepsModel::STEP_SELECT_UPLOAD);
        $healthcheck = $this->getHealthcheck();


        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (isset($postData['selectRmsUploadId']))
            {
                $selectRmsUploadService = new SelectRmsUploadService($this->_mpsSession->selectedClientId);
                $rmsUpload              = $selectRmsUploadService->validateRmsUploadId($postData['selectRmsUploadId']);
                if ($rmsUpload instanceof RmsUploadModel)
                {
                    $this->getHealthcheck()->rmsUploadId = $postData["selectRmsUploadId"];
                    $this->updateHealthcheckStepName();
                    $this->saveHealthcheck();
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
            if ($this->getHealthcheck()->rmsUploadId > 0)
            {
                if (isset($postData['saveAndContinue']))
                {
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
        }
        $this->view->numberOfUploads = count(RmsUploadMapper::getInstance()->fetchAllForClient($this->getHealthcheck()->clientId));
        $this->view->navigationForm  = new \MPSToolbox\Legacy\Modules\Assessment\Forms\AssessmentNavigationForm(\MPSToolbox\Legacy\Modules\Assessment\Forms\AssessmentNavigationForm::BUTTONS_NEXT);
        $this->view->rmsUpload       = $healthcheck->getRmsUpload();
    }

    /**
     * Users can upload/see uploaded data on this step
     */
    public function settingsAction ()
    {
        $this->_pageTitle = array('Healthcheck', 'Settings');
        $this->_navigation->setActiveStep(HealthCheckStepsModel::STEP_SETTINGS);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (!isset($postData['goBack']))
            {
                $this->saveClientSettingsForm($postData);
                $this->saveHealthcheck();

                if (isset($postData['saveAndContinue']))
                {
                    $this->updateHealthcheckStepName();
                    $this->saveHealthcheck();
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
}