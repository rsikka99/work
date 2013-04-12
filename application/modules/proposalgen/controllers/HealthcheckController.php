<?php
class Proposalgen_HealthcheckController extends Proposalgen_Library_Controller_Healthcheck
{
    /**
     * Users can upload/see uploaded data on this step
     */
    public function indexAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_HealthCheck_Step::STEP_FLEETDATA_UPLOAD);

        $report               = $this->getReport();
        $form                 = new Proposalgen_Form_ImportRmsCsv(array('csv'), "1B", "8MB");
        $deviceInstanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
        $rmsExcludedRowMapper = Proposalgen_Model_Mapper_Rms_Excluded_Row::getInstance();
        $this->saveReport(true);

        $rmsUpload        = $report->getRmsUpload();
        $this->view->form = $form;

        $this->view->rmsUpload = $rmsUpload;
//        if($rmsUpload instanceof Proposalgen_Model_Rms_Upload_Row)
//        {
//            $this->view->populateGrid = true;
//        }

        $navigationButtons          = ($rmsUpload instanceof Proposalgen_Model_Rms_Upload) ? Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT : Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK;
        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation($navigationButtons);

    }


    /**
     * Allows the user to set the report settings for a report
     */
    public function reportsettingsAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_HealthCheck_Step::STEP_REPORTSETTINGS);

        //$reportSettingsService = new Proposalgen_Service_ReportSettings($this->getReport()->id, $this->_userId, $this->_dealerId);
        Proposalgen_Model_Mapper_Report_Setting::getInstance()->find($this->_report->getReportSettings());
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();

            if (isset($values ['cancel']))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                $reportSettings = new Proposalgen_Model_Report_Setting();
                $reportSettings->populate($values);
                if (Proposalgen_Model_Mapper_Report_Setting::getInstance()->save($reportSettings))
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

        //$this->view->form = $reportSettingsService->getForm();
    }
}