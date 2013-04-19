<?php
class Healthcheck_IndexController extends Healthcheck_Library_Controller_Healthcheck
{
    /**
     * The navigation steps
     *
     * @var Healthcheck_Model_Healthcheck_Steps
     */
    protected $_navigation;

    public function init ()
    {
        $this->_identity   = Zend_Auth::getInstance()->getIdentity();
        $this->_mpsSession = new Zend_Session_Namespace('mps-tools');
        $this->_navigation = Healthcheck_Model_Healthcheck_Steps::getInstance();
    }

    /**
     * Selects a upload to use for the healthcheck
     */
    public function indexAction ()
    {
        //      Mark the step we're on as active
        $navigationButtons          = Proposalgen_Form_Assessment_Navigation::BUTTONS_NEXT;

        $this->_navigation->setActiveStep(Healthcheck_Model_Healthcheck_Steps::STEP_SELECTUPLOAD);
        $report = $this->getReport();
        if (isset($report->getRmsUpload()->id))
        {
            $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation($navigationButtons);
            $this->view->selectedUpload = $report->getRmsUpload();
        }
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values["selectIds"]))
            {
                if (is_numeric($values["selectIds"]))
                {
                    $this->getReport()->rmsUploadId = $values["selectIds"];
                    $this->saveReport();
                    $this->view->selectedUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($values["selectIds"]);
                    $this->gotoNextNavigationStep($this->_navigation);
                }
            }
            else if(isset($values["saveAndContinue"]))
            {
                $this->gotoNextNavigationStep($this->_navigation);
            }
        }
    }

    /**
     * Users can upload/see uploaded data on this step
     */
    public function settingsAction ()
    {
//      Mark the step we're on as active
        $this->_navigation->setActiveStep(Healthcheck_Model_Healthcheck_Steps::STEP_REPORTSETTINGS);
        $this->getReport();
        $this->saveReport(true);
        $healthcheckSettingsService = new Healthcheck_Service_HealthcheckSettings($this->getReport()->id, Zend_Auth::getInstance()->getIdentity()->id, Zend_Auth::getInstance()->getIdentity()->dealerId);
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
                    $this->saveReport();
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


    /**
     * The healthcheckAction displays the healthcheck report.
     * Data is retrieved
     * from the database and displayed using HTML, CSS, and javascript.
     */
    public function reportAction ()
    {
        // Mark the step we're on as active
        $this->_navigation->setActiveStep(Healthcheck_Model_Healthcheck_Steps::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports->Healthcheck->active = true;

        $this->view->formats     = array(
            "/proposalgen/healthcheck/generate/format/docx" => $this->_wordFormat
        );
        $this->view->reportTitle = "Health Check";

        $format = $this->_getParam("format", "html");
        try
        {
            // Clear the cache for the report before proceeding
            $healthcheck = new Healthcheck_Model_Healthcheck_Healthcheck($this->getProposal());
            die();
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
                require_once('PHPWord.php');
                $this->view->phpword     = new PHPWord();
                $healthcheck             = new Healthcheck_Model_Healthcheck_Healthcheck($this->getProposal());
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
            $this->render($this->view->App()->theme . '/' . $format . "/00_render");
        }
        catch (Exception $e)
        {
            throw new Exception("Controller caught the exception!", 0, $e);
        }
    }
}