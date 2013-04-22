<?php
class Assessment_IndexController extends Tangent_Controller_Action
{
    /**
     * The Zend_Auth identity
     *
     * @var stdClass
     */
    protected $_identity;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_mpsSession;

    /**
     * @var Assessment_Model_Assessment
     */
    protected $_assessment;

    /**
     * The navigation steps
     *
     * @var Assessment_Model_Assessment_Steps
     */
    protected $_navigation;

    public function init ()
    {
        $this->_identity   = Zend_Auth::getInstance()->getIdentity();
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
        $this->_navigation = Assessment_Model_Assessment_Steps::getInstance();
    }

    /**
     * Gets the assessment we're working on
     *
     * @return Assessment_Model_Assessment
     */
    public function getAssessment ()
    {
        if (!isset($this->_assessment))
        {
            if (isset($this->_mpsSession->assessmentId) && $this->_mpsSession->assessmentId > 0)
            {
                $this->_assessment = Assessment_Model_Mapper_Assessment::getInstance()->find($this->_mpsSession->assessmentId);
            }
            else
            {
                $this->_assessment               = new Assessment_Model_Assessment();
                $this->_assessment->dateCreated  = date('Y-m-d H:i:s');
                $this->_assessment->lastModified = date('Y-m-d H:i:s');
                $this->_assessment->dealerId     = $this->_identity->dealerId;
                $this->_assessment->clientId     = $this->_mpsSession->selectedClientId;
            }
        }

        return $this->_assessment;
    }

    /**
     * Saves an assessment
     */
    public function saveAssessment ()
    {
        if (isset($this->_mpsSession->assessmentId) && $this->_mpsSession->assessmentId > 0)
        {
            Assessment_Model_Mapper_Assessment::getInstance()->save($this->_assessment);
        }
        else
        {
            Assessment_Model_Mapper_Assessment::getInstance()->insert($this->_assessment);
            $this->_mpsSession->assessmentId = $this->_assessment->id;
        }
    }

    /**
     * This action takes care of selecting an upload
     */
    public function indexAction ()
    {
        $this->redirector("select-upload");
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
                    $this->_flashMessenger->addMessage(array('success' => 'The Upload you selected is valid.'));
                    $this->getAssessment()->rmsUploadId = $rmsUpload->id;
                    $this->saveAssessment();
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'The Upload you selected is not valid.'));
                }
            }

            if ($this->getAssessment()->rmsUploadId > 0)
            {
                if (isset($postData['saveAndContinue']))
                {
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
        }

        $this->view->rmsUpload      = $this->getAssessment()->getRmsUpload();
        $this->view->navigationForm = new Assessment_Form_Assessment_Navigation(Assessment_Form_Assessment_Navigation::BUTTONS_NEXT);
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
                            $this->_flashMessenger->addMessage(array(
                                                                    'success' => "Your changes were saved successfully."
                                                               ));
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
                    My_Log::logException($e);
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

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['saveAndContinue']))
            {
                $this->gotoNextNavigationStep($this->_navigation);
            }
            else if (isset($postData['goBack']))
            {
                $this->gotoPreviousNavigationStep($this->_navigation);
            }
        }

        $this->view->navigationForm = new Assessment_Form_Assessment_Navigation(Assessment_Form_Assessment_Navigation::BUTTONS_BACK_NEXT);
    }

    /**
     * The user can see various reports from here
     */
    public function reportAction ()
    {
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_FINISHED);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['goBack']))
            {
                $this->gotoPreviousNavigationStep($this->_navigation);
            }
        }
        $this->view->navigationForm = new Assessment_Form_Assessment_Navigation(Assessment_Form_Assessment_Navigation::BUTTONS_BACK);
    }
}