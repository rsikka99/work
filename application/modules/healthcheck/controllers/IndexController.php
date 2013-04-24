<?php
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
     * Selects a upload to use for the healthcheck
     */
    public function selectUploadAction ()
    {
        // Mark the step we're on as active
        $this->_navigation->setActiveStep(Healthcheck_Model_Healthcheck_Steps::STEP_SELECT_UPLOAD);
        $healthcheck = $this->getHealthcheck();


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
                    $this->getHealthcheck()->rmsUploadId = $postData["selectRmsUploadId"];
                    $this->updateHealthcheckStepName();
                    $this->saveHealthcheck();
                    $this->gotoNextNavigationStep($this->_navigation);
                }
                else
                {
                    $this->_flashMessenger->addMessage(array('danger' => 'The Upload you selected is not valid.'));
                }
            }

            if ($this->getHealthcheck()->rmsUploadId > 0)
            {
                if (isset($postData['saveAndContinue']))
                {
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
        }
        $this->view->navigationForm = new Healthcheck_Form_Healthcheck_Navigation(Healthcheck_Form_Healthcheck_Navigation::BUTTONS_NEXT);
        $this->view->rmsUpload      = $healthcheck->getRmsUpload();
    }

    /**
     * Users can upload/see uploaded data on this step
     */
    public function settingsAction ()
    {
//      Mark the step we're on as active
        $this->_navigation->setActiveStep(Healthcheck_Model_Healthcheck_Steps::STEP_SETTINGS);

        $healthcheckSettingsService = new Healthcheck_Service_HealthcheckSettings($this->getHealthcheck()->id, Zend_Auth::getInstance()->getIdentity()->id, Zend_Auth::getInstance()->getIdentity()->dealerId);
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();

            if (isset($values ['cancel']))
            {
                $this->gotoPreviousNavigationStep($this->_navigation);
            }
            else
            {

                if ($healthcheckSettingsService->update($values))
                {
                    $this->updateHealthcheckStepName();
                    $this->saveHealthcheck();
                    $this->_flashMessenger->addMessage(array(
                                                            'success' => 'Settings saved.'
                                                       ));


                    if (isset($values ['saveAndContinue']))
                    {
                        $this->gotoNextNavigationStep($this->_navigation);
                    }
                }
                else
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => 'Please correct the errors below.'
                                                       ));
                }
            }
        }
        $this->view->form = $healthcheckSettingsService->getForm();
    }
}