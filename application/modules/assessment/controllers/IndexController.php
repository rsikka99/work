<?php
use MPSToolbox\Legacy\Entities\SurveyEntity;
use MPSToolbox\Legacy\Mappers\UserMapper;
use MPSToolbox\Legacy\Modules\Assessment\Forms\AssessmentNavigationForm;
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentSurveyModel;
use MPSToolbox\Legacy\Modules\Assessment\Services\AssessmentSurveyService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\SurveySettingMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\SelectRmsUploadService;

/**
 * Class Assessment_IndexController
 */
class Assessment_IndexController extends Assessment_Library_Controller_Action
{
    /**
     * This action will redirect us to the latest available step
     */
    public function indexAction ()
    {
        $this->redirectToLatestStep($this->getAssessment()->stepName);
    }

    /**
     * Handles selecting an RMS upload
     */
    public function selectUploadAction ()
    {
        $this->_pageTitle = ['Assessment', 'Select Upload'];
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_FLEET_UPLOAD);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (isset($postData['selectRmsUploadId']))
            {
                $selectRmsUploadService = new SelectRmsUploadService($this->_mpsSession->selectedClientId);
                $rmsUpload              = $selectRmsUploadService->validateRmsUploadId($postData['selectRmsUploadId']);
                if ($rmsUpload instanceof RmsUploadModel)
                {
                    $this->getAssessment()->rmsUploadId = $rmsUpload->id;
                    $this->updateAssessmentStepName();
                    $this->saveAssessment();
                    $this->gotoNextNavigationStep($this->_navigation);
                }
                else
                {
                    $this->_flashMessenger->addMessage(['danger' => 'The Upload you selected is not valid.']);
                }
            }
            else if (isset($postData['noUploads']))
            {
                $this->redirectToRoute('rms-upload.upload-file');
            }

            if ($this->getAssessment()->rmsUploadId > 0)
            {
                if (isset($postData['saveAndContinue']))
                {
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
        }
        $this->view->numberOfUploads = count(RmsUploadMapper::getInstance()->fetchAllForClient($this->getAssessment()->clientId));
        $this->view->rmsUpload       = $this->getAssessment()->getRmsUpload();
        $this->view->navigationForm  = new AssessmentNavigationForm(AssessmentNavigationForm::BUTTONS_NEXT);
    }

    /**
     * This is our survey page. Everything we need to fill out is here.
     */
    public function surveyAction ()
    {
        $this->_pageTitle = ['Assessment', 'Survey'];
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_SURVEY);

        /**
         * Get data to populate
         */
        $survey = $this->getAssessment()->getClient()->getSurvey();

        if (!$survey instanceof SurveyEntity)
        {
            $survey = new SurveyEntity();
        }


        $assessmentSurveyService = new AssessmentSurveyService($survey);
        $form                    = $assessmentSurveyService->getForm();


        /**
         * Handle our post
         */
        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData ["goBack"]))
            {
                $this->gotoPreviousNavigationStep($this->_navigation);
            }
            else
            {

                $db = Zend_Db_Table::getDefaultAdapter();
                try
                {
                    $db->beginTransaction();
                    $postData = $this->getRequest()->getPost();

                    // Every time we save anything related to a report, we should save it (updates the modification date)
                    $this->updateAssessmentStepName();
                    $this->saveAssessment();

                    if ($assessmentSurveyService->save($postData, $this->getAssessment()->clientId))
                    {
                        $db->commit();

                        if (isset($postData ["saveAndContinue"]))
                        {
                            // Call the base controller to send us to the next logical step in the proposal.
                            $this->gotoNextNavigationStep($this->_navigation);
                        }
                        else
                        {
                            $this->_flashMessenger->addMessage(['success' => "Your changes were saved successfully."]);
                        }
                    }
                    else
                    {
                        $db->rollBack();
                        $this->_flashMessenger->addMessage(['danger' => 'Please correct the errors below before continuing.']);
                    }
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    \Tangent\Logger\Logger::logException($e);
                }
            }
        }


        $this->view->form = $form;
    }

    /**
     * The user can set various settings here
     */
    public function settingsAction ()
    {
        $this->_pageTitle = ['Assessment', 'Settings'];
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_SETTINGS);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (!isset($postData['goBack']))
            {
                $this->saveClientSettingsForm($postData);
                $this->saveAssessment();

                if (isset($postData['saveAndContinue']))
                {
                    $this->updateAssessmentStepName();
                    $this->saveAssessment();
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