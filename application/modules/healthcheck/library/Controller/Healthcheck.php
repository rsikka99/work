<?php
class Healthcheck_Library_Controller_Healthcheck extends Proposalgen_Library_Controller_Proposal
{
    /**
     * @var Healthcheck_Model_Healthcheck
     */
    protected $_report;

    /**
     * The current step that the user is viewing.
     *
     * @var Healthcheck_Model_Healthcheck_Steps
     */
    protected $_activeStep;

    public function initReportList ()
    {
        // This is a list of reports that we can view.
        $this->view->availableReports = (object)array(
            "Reports"     => (object)array(
                "pagetitle" => "Select a report...",
                "active"    => false,
                "url"       => $this->view->baseUrl('/proposalgen/report_index/index')
            ),

            "Healthcheck" => (object)array(
                "pagetitle" => "Health Check",
                "active"    => false,
                "url"       => $this->view->baseUrl('/proposalgen/report_healthcheck/index')
            )
        );
    }

    /**
     * Init function for html reports
     *
     * @throws Exception
     */
    public function initHtmlReport ()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/htmlReport.js'));

        $this->_user = Application_Model_Mapper_User::getInstance()->find(Zend_Auth::getInstance()->getIdentity()->id);

        if ($this->_reportSession->healthcheckId < 1)
        {
            $this->_flashMessenger->addMessage(array(
                                                    "error" => "Please select a report first."
                                               ));
            // Send user to the index
            $this->_helper->redirector('index', 'index', 'index');
        }

        $this->_report = Healthcheck_Model_Mapper_Healthcheck::getInstance()->find($this->_reportSession->healthcheckId);
        if ($this->_report === null)
        {
            $this->_flashMessenger->addMessage(array(
                                                    "error" => "Please select a report first."
                                               ));
            // Send user to the index
            $this->_helper->redirector('index', 'index', 'index');
        }


        // Setup the different file formats
        $this->_csvFormat           = (object)array(
            'extension'      => 'csv',
            'name'           => 'Excel (CSV)',
            'loadingmessage' => '',
            'btnstyle'       => 'success'
        );
        $this->_pdfFormat           = (object)array(
            'extension'      => 'pdf',
            'name'           => 'Adobe PDF',
            'loadingmessage' => 'Please be patient as PDF\'s take a few minutes to generate',
            'btnstyle'       => 'danger'
        );
        $this->_wordFormat          = (object)array(
            'extension'      => 'docx',
            'name'           => 'Word (DOCX)',
            'loadingmessage' => 'Please wait a moment while we generate your report',
            'btnstyle'       => 'primary'
        );
        $this->view->dealerLogoFile = $this->getDealerLogoFile();
    }

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch ()
    {
        // Render our survey menu
        $stage = ($this->getReport()->stepName) ? : Healthcheck_Model_Healthcheck_Steps::STEP_SELECTUPLOAD;
        Healthcheck_Model_Healthcheck_Steps::getInstance()->updateAccessibleSteps($this->getReportSteps(), $stage);
//
//        $this->view->placeholder('ProgressionNav')->set($this->view->ProposalMenu($this->getReportSteps()));
    }

    /**
     * Gets the report object that we are working with
     *
     * @throws Exception
     * @return Healthcheck_Model_Healthcheck
     */
    protected function getReport ()
    {
        if (!isset($this->_report))
        {
            // Fetch the existing report, or create a new one if the session id isn't set
            if (isset($this->_reportSession->healthcheckId) && $this->_reportSession->healthcheckId > 0)
            {
                $this->_report = Healthcheck_Model_Mapper_Healthcheck::getInstance()->find((int)$this->_reportSession->healthcheckId);
                if ($this->_report === null)
                {
                    throw new Exception("Error selecting the report with an id of '{$this->_reportSession->reportId}'.");
                }
            }
            else
            {
                $identity                   = Zend_Auth::getInstance()->getIdentity();
                $this->_report              = new Healthcheck_Model_Healthcheck();
                $this->_report->userId      = $identity->id;
                $this->_report->clientId    = $this->_clientId;
                $this->_report->dealerId    = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $this->_report->stepName    = Healthcheck_Model_Healthcheck_Steps::STEP_REPORTSETTINGS;
                $this->_report->dateCreated = date('Y-m-d H:i:s');
                $this->_report->reportDate  = date('Y-m-d H:i:s');
            }
        }

        return $this->_report;
    }

    /**
     * Gets the proposal object for reports to use
     *
     * @throws Zend_Exception
     * @return Proposalgen_Model_Proposal_OfficeDepot
     */
    public function getProposal ()
    {
        if (!$this->_proposal)
        {
            $this->_proposal = false;
            $hasError        = false;
            try
            {
                $this->_proposal = new Proposalgen_Model_Proposal_OfficeDepot($this->_report);

                if ($this->_report->devicesModified)
                {
                    $this->_redirect('/data/modificationwarning');
                }

                if (count($this->_proposal->getDeviceCount()) < 1)
                {
                    $this->view->ErrorMessages [] = "All uploaded printers were excluded from your report. Reports can not be generated until at least 1 printer is added.";
                    $hasError                     = true;
                }
            }
            catch (Exception $e)
            {
                $this->view->ErrorMessages [] = "There was an error getting the reports.";
                throw new Zend_Exception("Error Getting Proposal Object.", 0, $e);
                $hasError = true;
            }

            if ($hasError)
            {
                $this->_proposal = false;
            }
        }

        return $this->_proposal;
    }

    /**
     * Saves the current report.
     * This keeps the updated modification date in the same location at all times.
     */
    protected function saveReport ($updateReportStage = true)
    {

        $reportMapper                = Healthcheck_Model_Mapper_Healthcheck::getInstance();
        $this->_report->lastModified = date('Y-m-d H:i:s');

        if ($updateReportStage)
        {
            // This updates the reports progress
            $newStep = $this->checkIfNextStepIsNew($this->_activeStep);
            if ($newStep !== false)
            {
                $this->_report->stepName = $newStep->enumValue;

                // We need to adjust the menu just in case we're not redirecting
                Healthcheck_Model_Healthcheck_Steps::getInstance()->updateAccessibleSteps($this->getReportSteps(), $newStep->enumValue);
            }
        }

        if ($this->_report->id === null || $this->_report->id < 1)
        {
//            $reportSettingService = new Healthcheck_Service_ReportSettings(,,Zend_Auth::getInstance()->getIdentity()->dealerId)
            $this->_report->healthcheckSettingId = $this->_report->getReportSettings()->id;
            $id                = $reportMapper->insert($this->_report);
            $this->_report->id = $id;
        }
        else
        {
            $id = $reportMapper->save($this->_report);
        }

        $this->_reportSession->healthcheckId = $this->_report->id;
    }

    /**
     * Gets an array of report steps.
     *
     * @return Healthcheck_Model_Healthcheck_Steps[]
     */
    protected function getReportSteps ()
    {
        $report      = $this->getReport();
        $reportSteps = null;
        if ($report instanceof Healthcheck_Model_Healthcheck)
        {
            $reportSteps = $report->getReportSteps();
        }
        else
        {
            $reportSteps = Healthcheck_Model_Healthcheck_Steps::getInstance()->steps;
        }

        return $reportSteps;
    }

    /**
     * Checks to see if the next step is a new step.
     *
     * @param Healthcheck_Model_Healthcheck_Steps $step
     *
     * @return Healthcheck_Model_Healthcheck_Steps Step Name. Returns FALSE if the step is not new.
     */
    protected function checkIfNextStepIsNew ($step)
    {
        if ($step !== null)
        {
            $nextStep = $step->nextStep;
            if ($nextStep !== null)
            {
                if (!$nextStep->canAccess)
                {
                    return $nextStep;
                }
            }
        }

        return false;
    }

    /**
     * Takes the user to the next step in the survey in order.
     * So company always goes to general
     */
    protected function gotoNextStep ()
    {
        if (isset($this->_activeStep))
        {
            $nextStep = $this->_activeStep->nextStep;
            if ($nextStep)
            {
                $this->redirector($nextStep->action, $nextStep->controller);
            }
        }
    }

    /**
     * Takes the user to the previous step in the survey in order.
     * So company always goes to general
     */
    protected function gotoPreviousStep ()
    {
        if (isset($this->_activeStep))
        {
            $prevStep = $this->_activeStep->previousStep;
            if ($prevStep)
            {
                $this->redirector($prevStep->action, $prevStep->controller);
            }
        }
    }

    /**
     * Sets a healthcheck step as active
     *
     * @param string $activeStepName
     *            The name of the step that is active
     */
    protected function setActiveReportStep ($activeStepName)
    {
        $this->_activeStep = null;
        foreach ($this->getReportSteps() as $step)
        {
            $step->active = false;
            if (strcasecmp($step->enumValue, $activeStepName) === 0)
            {
                $this->_activeStep = $step;
                $step->active      = true;
                break;
            }
        }
    }

    /**
     * Gets the last available step for a report
     *
     * @return Healthcheck_Model_Healthcheck_Steps
     */
    protected function getLatestAvailableReportStep ()
    {
        $latestStep = null;

        /* @var $step Healthcheck_Model_Healthcheck_Steps */
        foreach ($this->getReportSteps() as $step)
        {
            /*
             * Just in case we don't find anything, lets set the step to the very first step.
             */
            if ($latestStep === null)
            {
                $latestStep = $step;
            }

            /*
         * If we can access the current step, and the next step either doesn't exist or is inaccessable.
         */
            if ($step->canAccess && ($step->nextStep === null || !$step->nextStep->canAccess))
            {
                $latestStep = $step;
                break;
            }
        }

        return $latestStep;
    }
}
