<?php
use MPSToolbox\Legacy\Modules\HealthCheck\Models\HealthCheckStepsModel;

/**
 * Class Healthcheck_Report_IndexController
 */
class Healthcheck_Report_IndexController extends Healthcheck_Library_Controller_Action
{
    public function preDispatch ()
    {
        parent::preDispatch();
        $this->view->ErrorMessages = array();
    }

    public function postDispatch ()
    {
        parent::postDispatch();
        // If we have error messages, send them to the error page
        if (count($this->view->ErrorMessages) > 0)
        {
            if (!isset($this->view->formTitle))
            {
                $this->view->formTitle = "Error";
            }
            $this->_helper->viewRenderer->setRender('report.error');
        }
    }

    /**
     * * The default action
     */
    public function indexAction ()
    {
        $this->_pageTitle = array('Healthcheck', 'Report');
        $this->_navigation->setActiveStep(HealthCheckStepsModel::STEP_FINISHED);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['goBack']))
            {
                $this->gotoPreviousNavigationStep($this->_navigation);
            }
        }

        $this->initReportList();

        $this->view->headScript()->appendFile($this->view->baseUrl("/js/app/legacy/HtmlReport.js"));
        $this->view->formTitle = "Report Summary";

        $healthcheck             = $this->getHealthcheck();
        $this->view->companyName = $healthcheck->getClient()->companyName;
        $this->view->reportName  = $healthcheck->getClient()->companyName;

        $this->view->navigationForm = $this->view->navigationForm  = new \MPSToolbox\Legacy\Modules\Assessment\Forms\AssessmentNavigationForm(\MPSToolbox\Legacy\Modules\Assessment\Forms\AssessmentNavigationForm::BUTTONS_BACK);
    }
}