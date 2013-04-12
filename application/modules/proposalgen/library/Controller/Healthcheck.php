<?php
class Proposalgen_Library_Controller_Healthcheck extends Proposalgen_Library_Controller_Proposal
{

    public function initReportList ()
    {
        // This is a list of reports that we can view.
        $this->view->availableReports = (object)array(
            "Reports"                      => (object)array(
                "pagetitle" => "Select a report...",
                "active"    => false,
                "url"       => $this->view->baseUrl('/proposalgen/report_index/index')
            ),

            "HealthCheck"                  => (object)array(
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

        if ($this->_reportId < 1)
        {
            $this->_flashMessenger->addMessage(array(
                                                    "error" => "Please select a report first."
                                               ));
            // Send user to the index
            $this->_helper->redirector('index', 'index', 'index');
        }

        $this->_report = Proposalgen_Model_Mapper_HealthCheck::getInstance()->find($this->_reportId);
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
        $stage = ($this->getReport()->stepName) ? : Proposalgen_Model_HealthCheck_Step::STEP_SURVEY;
        Proposalgen_Model_HealthCheck_Step::updateAccessibleSteps($this->getReportSteps(), $stage);

        $this->view->placeholder('ProgressionNav')->set($this->view->ProposalMenu($this->getReportSteps()));
    }

    /**
     * Gets the report object that we are working with
     *
     * @throws Exception
     * @return Proposalgen_Model_HealthCheck
     */
    protected function getReport ()
    {
        if (!isset($this->_report))
        {
            // Fetch the existing report, or create a new one if the session id isn't set
            if (isset($this->_reportSession->reportId) && $this->_reportSession->reportId > 0)
            {
                $this->_report = Proposalgen_Model_Mapper_HealthCheck::getInstance()->find((int)$this->_reportSession->reportId);
                if ($this->_report === null)
                {
                    throw new Exception("Error selecting the report with an id of '{$this->_reportSession->reportId}'.");
                }
            }
            else
            {
                $identity                   = Zend_Auth::getInstance()->getIdentity();
                $this->_report              = new Proposalgen_Model_HealthCheck();
                $this->_report->userId      = $identity->id;
                $this->_report->clientId    = $this->_clientId;
                $this->_report->dealerId    = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $this->_report->stepName    = Proposalgen_Model_HealthCheck_Step::STEP_FLEETDATA_UPLOAD;
                $this->_report->dateCreated = date('Y-m-d H:i:s');
                $this->_report->reportDate  = date('Y-m-d H:i:s');
            }
        }

        return $this->_report;
    }

    /**
     * Saves the current report.
     * This keeps the updated modification date in the same location at all times.
     */
    protected function saveReport ($updateReportStage = true)
    {
        $reportMapper                = Proposalgen_Model_Mapper_HealthCheck::getInstance();
        $this->_report->lastModified = date('Y-m-d H:i:s');

        if ($updateReportStage)
        {
            // This updates the reports progress
            $newStep = $this->checkIfNextStepIsNew($this->_activeStep);
            if ($newStep !== false)
            {
                $this->_report->stepName = $newStep->enumValue;

                // We need to adjust the menu just in case we're not redirecting
                Proposalgen_Model_HealthCheck_Step::updateAccessibleSteps($this->getReportSteps(), $newStep->enumValue);
            }
        }

        if ($this->_report->id === null || $this->_report->id < 1)
        {
            $id                = $reportMapper->insert($this->_report);
            $this->_report->id = $id;
        }
        else
        {
            $id = $reportMapper->save($this->_report);
        }

        $this->_reportSession->reportId = $this->_report->id;
    }

    /**
     * Gets an array of report steps.
     *
     * @return Proposalgen_Model_HealthCheck_Step[]
     */
    protected function getReportSteps ()
    {
        $report      = $this->getReport();
        $reportSteps = null;
        if ($report instanceof Proposalgen_Model_HealthCheck)
        {
            $reportSteps = $report->getReportSteps();
        }
        else
        {
            $reportSteps = Proposalgen_Model_HealthCheck_Step::getSteps();
        }

        return $reportSteps;
    }

    /**
     * Checks to see if the next step is a new step.
     *
     * @param Proposalgen_Model_HealthCheck_Step $step
     *
     * @return Proposalgen_Model_HealthCheck_Step Step Name. Returns FALSE if the step is not new.
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
     * Gets the last available step for a report
     *
     * @return Proposalgen_Model_HealthCheck_Step
     */
    protected function getLatestAvailableReportStep ()
    {
        $latestStep = null;

        /* @var $step Proposalgen_Model_HealthCheck_Step */
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
