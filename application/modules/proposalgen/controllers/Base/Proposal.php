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
class Proposalgen_Controller_Proposal extends Zend_Controller_Action
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
     * (non-PHPdoc)
     * 
     * @see Zend_Controller_Action::preDispatch()
     */
    public function preDispatch ()
    {
        $controllerName = $this->getRequest()->getControllerName();
        $actionName = $this->getRequest()->getActionName();
        
        /* @var $step Proposalgen_Model_Report_Step */
        foreach ( $this->getReportSteps() as $step )
        {
            if (strcasecmp($step->getController(), $controllerName) === 0 && strcasecmp($step->getAction(), $actionName) === 0)
            {
                if (! $step->getCanAccess())
                {
                    // TODO: Do some redirection here
                }
            }
        }
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
            $this->_proposal = Proposalgen_Model_Mapper_Report::getInstance()->find($this->_reportId);
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
        return $this->getReport()->getReportSteps();
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
            if (strcasecmp($step->getName(), $activeStepName) === 1)
            {
                $step->setActive(true);
            }
        }
    }
}
