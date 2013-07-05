<?php
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
     * Handles selecting an rms upload
     */
    public function selectUploadAction ()
    {
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_FLEET_UPLOAD);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();

            if (isset($postData['selectRmsUploadId']))
            {
                $selectRmsUploadService = new Proposalgen_Service_SelectRmsUpload($this->_mpsSession->selectedClientId);
                $rmsUpload              = $selectRmsUploadService->validateRmsUploadId($postData['selectRmsUploadId']);
                if ($rmsUpload instanceof Proposalgen_Model_Rms_Upload)
                {
                    $this->getAssessment()->rmsUploadId = $rmsUpload->id;
                    $this->updateAssessmentStepName();
                    $this->saveAssessment();
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

            if ($this->getAssessment()->rmsUploadId > 0)
            {
                if (isset($postData['saveAndContinue']))
                {
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
        }
        $this->view->numberOfUploads = count(Proposalgen_Model_Mapper_Rms_Upload::getInstance()->fetchAllForClient($this->getAssessment()->clientId));
        $this->view->rmsUpload       = $this->getAssessment()->getRmsUpload();
        $this->view->navigationForm  = new Assessment_Form_Assessment_Navigation(Assessment_Form_Assessment_Navigation::BUTTONS_NEXT);
    }

    /**
     * This is our survey page. Everything we need to fill out is here.
     */
    public function surveyAction ()
    {
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_SURVEY);

        /**
         * Fetch Survey Settings
         */
        $surveySetting = Proposalgen_Model_Mapper_Survey_Setting::getInstance()->fetchSystemSurveySettings();
        $user          = Application_Model_Mapper_User::getInstance()->find($this->_identity->id);;
        $surveySetting->populate($user->getUserSettings()->getSurveySettings()->toArray());


        /**
         * Get data to populate
         */
        $survey = $this->getAssessment()->getSurvey();

        if (!$survey instanceof Assessment_Model_Assessment_Survey)
        {
            $survey = new Assessment_Model_Assessment_Survey();
        }


        $assessmentSurveyService = new Assessment_Service_Assessment_Survey($survey, $surveySetting);
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

                    if ($assessmentSurveyService->save($postData, $this->getAssessment()->id))
                    {
                        $db->commit();

                        if (isset($postData ["saveAndContinue"]))
                        {
                            // Call the base controller to send us to the next logical step in the proposal.
                            $this->gotoNextNavigationStep($this->_navigation);
                        }
                        else
                        {
                            $this->_flashMessenger->addMessage(array('success' => "Your changes were saved successfully."));
                        }
                    }
                    else
                    {
                        $db->rollBack();
                        $this->_flashMessenger->addMessage(array('danger' => 'Please correct the errors below before continuing.'));
                    }
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    Tangent_Log::logException($e);
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
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_SETTINGS);

        $assessment                = $this->getAssessment();
        $assessmentSettingsService = new Assessment_Service_Assessment_Settings($assessment->id, $this->_identity->id, $this->_identity->dealerId);

        if ($this->getRequest()->isPost())
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            try
            {
                $db->beginTransaction();
                $postData = $this->getRequest()->getPost();
                if ($assessmentSettingsService->update($postData))
                {
                    $this->updateAssessmentStepName();
                    $this->saveAssessment();

                    $db->commit();

                    if (isset($postData['saveAndContinue']))
                    {
                        $this->gotoNextNavigationStep($this->_navigation);
                    }
                    else if (isset($postData['goBack']))
                    {
                        $this->gotoPreviousNavigationStep($this->_navigation);
                    }
                }
                else
                {
                    $db->rollBack();
                    $this->_flashMessenger->addMessage(array('danger' => 'There was an error saving your settings.'));
                }
            }
            catch (Exception $e)
            {
                $db->rollBack();
                $this->_flashMessenger->addMessage(array('danger' => 'There was an error saving your settings.'));
                Tangent_Log::logException($e);
            }
        }

        $this->view->form           = $assessmentSettingsService->getForm();
        $this->view->navigationForm = new Assessment_Form_Assessment_Navigation(Assessment_Form_Assessment_Navigation::BUTTONS_BACK_NEXT);
    }
}