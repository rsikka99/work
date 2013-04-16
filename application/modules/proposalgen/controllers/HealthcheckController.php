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
    public function settingsAction ()
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

       $this->view->form = $healthcheckSettingsService->getForm();
    }

    /**
     * The healthcheckAction displays the healthcheck report.
     * Data is retrieved
     * from the database and displayed using HTML, CSS, and javascript.
     */
    public function reportAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Healthcheck_Step::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports->Healthcheck->active = true;

        $this->view->formats = array(
            "/proposalgen/healthcheck/generate/format/docx" => $this->_wordFormat
        );

        $this->view->reportTitle = "Health Check";

        $format = $this->_getParam("format", "html");
        try
        {
            // Clear the cache for the report before proceeding
            $healthcheck = new Proposalgen_Model_Healthcheck_Healthcheck($this->getProposal());
            $this->clearCacheForReport();
            if (false !== ($proposal = $this->getProposal()))
            {
                switch ($format)
                {
                    case "docx" :
                        // Add DOCX Logic here

                        $this->view->phpword = new PHPWord();
                        break;
                    case "pdf" :
                        // Add PDF Logic here
                        break;
                    case "html" :
                    default :
                        // Add HTML Logic here
                        break;
                }
            }
            else
            {
                throw new Exception("The proposal cannot be found.");
            }
            $this->view->healthcheck = $healthcheck;
        }
        catch (Exception $e)
        {
            throw new Exception("Health Check could not be generated.", 0, $e);
        }

    }

    /**
     * The Index action of the solution.
     */
    public function generateAction ()
    {
        $format = $this->_getParam("format", "docx");
        switch ($format)
        {
            case "csv" :
                throw new Exception("CSV Format not available through this page yet!");
                break;
            case "docx" :
                require_once ('PHPWord.php');
                $this->view->phpword     = new PHPWord();
                $healthcheck             = new Proposalgen_Model_Healthcheck_Healthcheck($this->getProposal());
                $this->view->healthcheck = $healthcheck;
                $graphs                  = $this->cachePNGImages($healthcheck->getGraphs(), true);
                $this->view->graphs      = $graphs;
                $this->_helper->layout->disableLayout();
                break;
            case "pdf" :
                throw new Exception("PDF Format not available through this page yet!");
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = "healthcheck.$format";

        $this->initReportVariables($filename);
        // Render early
        try
        {
            $this->render($this->view->App()->theme . '/' . $format  . "/00_render");
        }
        catch (Exception $e)
        {
            throw new Exception("Controller caught the exception!", 0, $e);
        }
    }
}