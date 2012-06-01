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
    protected $_report;
    protected $_reportId;
    protected $_reportSteps;

    /**
     * Called from the constructor as the final step of initialization
     * (non-PHPdoc)
     *
     * @see Zend_Controller_Action::init()
     */
    public function init ()
    {
        $this->view->reportSteps = $this->getReportSteps();
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
            $this->_report = Proposalgen_Model_Mapper_Report::getInstance()->find($this->_reportId);
        }
        return $this->_report;
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
     * Sets a report step as active
     *
     * @param String $activeStepName
     *            The name of the step that is active
     */
    protected function setActiveReportStep ($activeStepName)
    {
        /* @var $step Proposalgen_Model_Report_Step */
        foreach ( $this->getReportSteps() as $step )
        {
            $step->setActive(false);
            if (strcasecmp($step->getName(), $activeStepName) === 0)
            {
                $step->setActive(true);
                break;
            }
        }
    }
}
