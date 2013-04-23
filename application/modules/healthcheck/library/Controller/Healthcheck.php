<?php
class Healthcheck_Library_Controller_Healthcheck extends Proposalgen_Library_Controller_Proposal
{
    /**
     * @var Healthcheck_Model_Healthcheck
     */
    protected $_healthcheck;

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

        if ($this->_mpsSession->healthcheckId < 1)
        {
            $this->_flashMessenger->addMessage(array(
                                                    "error" => "Please select a report first."
                                               ));
            // Send user to the index
            $this->_helper->redirector('index', 'index', 'index');
        }

        $this->_healthcheck = Healthcheck_Model_Mapper_Healthcheck::getInstance()->find($this->_mpsSession->healthcheckId);
        if ($this->_healthcheck === null)
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
        $stage = ($this->getHealthcheck()->stepName) ? : Healthcheck_Model_Healthcheck_Steps::STEP_SELECTUPLOAD;
        $this->_navigation->updateAccessibleSteps($stage);
        $this->view->placeholder('ProgressionNav')->set($this->view->NavigationMenu($this->_navigation->steps));
        //Healthcheck_Model_Healthcheck_Steps::getInstance()->updateAccessibleSteps($this->getReportSteps(), $stage);
//
//        $this->view->placeholder('ProgressionNav')->set($this->view->ProposalMenu($this->getReportSteps()));
    }

    /**
     * Gets the report object that we are working with
     *
     * @throws Exception
     * @return Healthcheck_Model_Healthcheck
     */
    protected function getHealthcheck ()
    {
        if (!isset($this->_healthcheck))
        {
            // Fetch the existing report, or create a new one if the session id isn't set
            if (isset($this->_mpsSession->healthcheckId) && $this->_mpsSession->healthcheckId > 0)
            {
                $this->_healthcheck = Healthcheck_Model_Mapper_Healthcheck::getInstance()->find((int)$this->_mpsSession->healthcheckId);
                if ($this->_healthcheck === null)
                {
                    throw new Exception("Error selecting the report with an id of '{$this->_reportSession->reportId}'.");
                }
            }
            else
            {
                $identity                   = Zend_Auth::getInstance()->getIdentity();
                $this->_healthcheck              = new Healthcheck_Model_Healthcheck();
                $this->_healthcheck->userId      = $identity->id;
                $this->_healthcheck->clientId    = $this->_mpsSession->selectedClientId;
                $this->_healthcheck->dealerId    = Zend_Auth::getInstance()->getIdentity()->dealerId;
                $this->_healthcheck->stepName    = Healthcheck_Model_Healthcheck_Steps::STEP_SELECTUPLOAD;
                $this->_healthcheck->dateCreated = date('Y-m-d H:i:s');
                $this->_healthcheck->reportDate  = date('Y-m-d H:i:s');
            }
        }

        return $this->_healthcheck;
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
                $this->_proposal = new Proposalgen_Model_Proposal_OfficeDepot($this->_healthcheck);

                if ($this->_healthcheck->devicesModified)
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
    protected function saveHealthcheck ()
    {
        if (isset($this->_mpsSession->healthcheckId) && $this->_mpsSession->healthcheckId > 0)
        {
            // Update the last modified date
            $this->_healthcheck->lastModified = date('Y-m-d H:i:s');
            Healthcheck_Model_Mapper_Healthcheck::getInstance()->save($this->_healthcheck);
        }
        else
        {
            $this->_healthcheck->healthcheckSettingId = $this->_healthcheck->getHealthcheckSettings()->id;
            $this->_healthcheck->lastModified = date('Y-m-d H:i:s');
            Healthcheck_Model_Mapper_Healthcheck::getInstance()->insert($this->_healthcheck);
            $this->_mpsSession->healthcheckId = $this->_healthcheck->id;
        }
    }

    /**
     * Gets an array of report steps.
     *
     * @return Healthcheck_Model_Healthcheck_Steps[]
     */
    protected function getHealthcheckSteps ()
    {
        $report      = $this->getHealthcheck();
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
     * Redirects the user to the very last available step
     */
    public function redirectToLatestStep ()
    {
        $stage = ($this->getHealthcheck()->stepName) ? : Healthcheck_Model_Healthcheck_Steps::STEP_SELECTUPLOAD;
        $this->_navigation->updateAccessibleSteps($stage);


        $firstStep  = false;
        $latestStep = false;
        foreach ($this->_navigation->steps as $step)
        {
            if ($firstStep === false)
            {
                $firstStep = $step;
            }

            if (!$step->canAccess)
            {
                break;
            }

            $latestStep = $step;
        }

        if ($latestStep)
        {
            $this->redirector($latestStep->action, $latestStep->controller, $latestStep->module);
        }
        else
        {
            $this->redirector($firstStep->action, $firstStep->controller, $firstStep->module);
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
        foreach ($this->getHealthcheckSteps() as $step)
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
        foreach ($this->getHealthcheckSteps() as $step)
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

    /**
    * Updates a healthcheck to be at the next available step
    *
    * @param bool $force Whether or not to force the update
    */
    public function updateHealthcheckStepName ($force = false)
    {
        // We can only do this when we have an active step
        if ($this->_navigation->activeStep instanceof My_Navigation_Step)
        {
            // That step also needs a next step for this to work
            if ($this->_navigation->activeStep->nextStep instanceof My_Navigation_Step)
            {
                $update = true;
                // We only want to update
                if ($force)
                {
                    $update = true;
                }
                else
                {
                    $newStepName = $this->_navigation->activeStep->nextStep->enumValue;

                    foreach ($this->_navigation->steps as $step)
                    {
                        // No need to update the step if we were going back in time.
                        if ($step->enumValue == $newStepName && $step->canAccess)
                        {
                            $update = false;
                            break;
                        }
                    }
                }

                if ($update)
                {
                    $this->getHealthcheck()->stepName = $this->_navigation->activeStep->nextStep->enumValue;
                }
            }
        }
    }
}
