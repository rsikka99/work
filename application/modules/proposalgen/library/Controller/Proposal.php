<?php

/**
 * Class Propo
 * Description of SurveyController:
 * This controller handles the survey/questionaire.
 * User should
 * be quided through a series of forms where they are asked to answer
 * questions about their existing fleet of printers.
 *
 * @author Chris Garrah
 */
class Proposalgen_Library_Controller_Proposal extends Zend_Controller_Action
{
    /**
     * The questionset id to use.
     *
     * @var number
     */
    const QUESTIONSET_ID = 1;
    protected $_report;
    protected $_reportSession;
    protected $_reportSteps;
    
    /**
     * The current step that the user is viewing.
     *
     * @var Proposalgen_Model_Report_Step
     */
    protected $_activeStep;

    /**
     * Called from the constructor as the final step of initialization
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        $this->_reportSession = new Zend_Session_Namespace('proposalgenerator_report');
        $this->view->reportSteps = $this->getReportSteps();
    }

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch ()
    {
        // Render our survey menu
        $this->view->placeholder('ProgressionNav')->set($this->view->ProposalMenu($this->getReportSteps()));
    }

    /**
     * Gets the report object that we are working with
     *
     * @return Proposalgen_Model_Report
     */
    protected function getReport ()
    {
        if (! isset($this->_report))
        {
            // Fetch the existing report, or create a new one if the session id isnt set
            if (isset($this->_reportSession->reportId) && $this->_reportSession->reportId > 0)
            {
                $this->_report = Proposalgen_Model_Mapper_Report::getInstance()->find((int)$this->_reportSession->reportId);
                if ($this->_report === null)
                {
                    throw new Exception("Error selecting the report with an id of '{$this->_reportSession->reportId}'.");
                }
            }
            else
            {
                $identity = Zend_Auth::getInstance()->getIdentity();
                $this->_report = new Proposalgen_Model_Report();
                $this->_report->setUserId($identity->id);
                $this->_report->setQuestionsetId(1);
                $this->_report->setReportStage(Proposalgen_Model_Report_Step::STEP_SURVEY_COMPANY);
                $this->_report->setDateCreated(date('Y-m-d H:i:s'));
                $this->_report->setReportDate(date('Y-m-d H:i:s'));
            }
        }
        return $this->_report;
    }

    /**
     * Saves the current report.
     * This keeps the updated modification date in the same location at all times.
     */
    protected function saveReport ()
    {
        $reportMapper = Proposalgen_Model_Mapper_Report::getInstance();
        $this->_report->setLastModified(date('Y-m-d H:i:s'));
        
        // This updates the reports progress
        $newStep = $this->checkIfNextStepIsNew($this->_activeStep->getName());
        if ($newStep !== FALSE)
        {
            $this->_report->setReportStage($newStep->getEnumValue());
        }
        
        $id = $reportMapper->save($this->_report);
        if ($this->_report->getReportId() === null || $this->_report->getReportId() < 1)
        {
            $this->_report->setReportId($id);
            $this->_reportSession->reportId = $id;
        }
    }

    /**
     * Gets an array of report steps.
     *
     * @return Proposalgen_Model_Report_Step[]
     */
    protected function getReportSteps ()
    {
        $report = $this->getReport();
        $reportSteps = null;
        if ($report === null)
        {
            $reportSteps = Proposalgen_Model_Report_Step::getSteps();
        }
        else
        {
            $reportSteps = $report->getReportSteps();
        }
        return $reportSteps;
    }

    /**
     * Checks to see if the next step is a new step.
     *
     * @param string $currentStepName            
     * @return Proposalgen_Model_Report_Step Step Name. Returns FALSE if the step is not new.
     */
    protected function checkIfNextStepIsNew ($currentStepName)
    {
        $isNew = false;
        $latestStep = $this->getLatestAvailableReportStep();
        
        /* @var $step Proposalgen_Model_Report_Step */
        foreach ( $this->getReportSteps() as $step )
        {
            if (strcasecmp($step->getName(), $currentStepName) === 0)
            {
                if ($step->getNextStep() !== null && ! $step->getNextStep()->getCanAccess())
                {
                    $isNew = $step;
                }
            }
        }
        
        return $isNew;
    }

    /**
     * Gets the last available step for a report
     *
     * @return Proposalgen_Model_Report_Step
     */
    protected function getLatestAvailableReportStep ()
    {
        $latestStep = null;
        
        /* @var $step Proposalgen_Model_Report_Step */
        foreach ( $this->getReportSteps() as $step )
        {
            /*
             * Just in case we don't find anything, lets set the step to the very first step.
             */
            if ($latestStep === null)
                $latestStep = $step;
                
                /*
             * If we can access the current step, and the next step either doesn't exist or is inaccessable.
             */
            if ($step->getCanAccess() && ($step->getNextStep() === null || ! $step->getNextStep()->getCanAccess()))
            {
                $latestStep = $step;
                break;
            }
        }
        
        return $latestStep;
    }

    /**
     * Sets a report step as active
     *
     * @param String $activeStepName
     *            The name of the step that is active
     */
    protected function setActiveReportStep ($activeStepName)
    {
        $this->_activeStep = null;
        /* @var $step Proposalgen_Model_Report_Step */
        foreach ( $this->getReportSteps() as $step )
        {
            $step->setActive(false);
            if (strcasecmp($step->getName(), $activeStepName) === 0)
            {
                $this->_activeStep = $step;
                $step->setActive(true);
                break;
            }
        }
    }

    /**
     * Takes the user to the next step in the survey in order.
     * So company always goes to general
     */
    protected function gotoNextStep ()
    {
        if (isset($this->_activeStep))
        {
            $nextStep = $this->_activeStep->getNextStep();
            if ($nextStep)
                $this->_helper->redirector($nextStep->getAction(), $nextStep->getController());
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
            $prevStep = $this->_activeStep->getPreviousStep();
            if ($prevStep)
                $this->_helper->redirector($prevStep->getAction(), $prevStep->getController());
        }
    }
}
