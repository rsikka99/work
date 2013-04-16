<?php
class Proposalgen_HealthcheckController extends Proposalgen_Library_Controller_Healthcheck
{
    /**
     * Users can upload/see uploaded data on this step
     */
    public function indexAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Healthcheck_Step::STEP_FLEETDATA_UPLOAD);

        $report               = $this->getReport();
        $rmsUploadService = new Proposalgen_Service_Rms_Upload(Zend_Auth::getInstance()->getIdentity()->id,$this->getReport()->clientId);
        $this->saveReport(true);
        $rmsUpload = null;
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if($rmsUploadService->getForm()->isValid($values))
            {
                $success = $rmsUploadService->processUpload($values);
                if($success)
                {
                    try{
                        $db       = Zend_Db_Table::getDefaultAdapter();
                        $db->beginTransaction();
                        $report->rmsUploadId = $rmsUploadService->rmsUpload->id;
                        Proposalgen_Model_Mapper_Healthcheck::getInstance()->save($report);
                        $this->_flashMessenger->addMessage(array(
                                                                'success' => 'Upload Complete.'
                                                           ));
                    }catch (Exception $e)
                    {
                        $db->rollBack();
                        throw new Exception("Passing exception up the chain.", 0, $e);
                    }
                    $db->commit();
                }
                else
                {

                }
            }


        }
        if($rmsUpload instanceof Proposalgen_Model_Rms_Upload_Row)
        {
            $this->view->populateGrid = true;
        }
        $this->view->form = $rmsUploadService->getForm();
        $navigationButtons          = ($rmsUpload instanceof Proposalgen_Model_Rms_Upload) ? Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT : Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK;
        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation($navigationButtons);

    }


    /**
     * Allows the user to set the report settings for a report
     */
    public function reportsettingsAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Healthcheck_Step::STEP_REPORTSETTINGS);
//        $dealer                   = Admin_Model_Mapper_Dealer::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->dealerId);
        $healthcheckSettingsService = new Proposalgen_Service_HealthcheckSettings($this->getReport()->id,Zend_Auth::getInstance()->getIdentity()->id,Zend_Auth::getInstance()->getIdentity()->dealerId);

        //$reportSettingsService = new Proposalgen_Service_ReportSettings($this->getReport()->id, $this->_userId, $this->_dealerId);
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();

            if (isset($values ['cancel']))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                if ($healthcheckSettingsService->update($values))
                {
                    $this->saveReport();
                    $this->_flashMessenger->addMessage(array(
                                                            'success' => 'Settings saved.'
                                                       ));


                    if (isset($values ['saveAndContinue']))
                    {
                        $this->gotoNextStep();
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

       //$this->view->form = $healthcheckSettingsService->getForm();
    }
}