<?php
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
        $this->view->headTitle('Healthcheck');
        $this->view->headTitle('Report');
        $this->_navigation->setActiveStep(Healthcheck_Model_Healthcheck_Steps::STEP_FINISHED);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['goBack']))
            {
                $this->gotoPreviousNavigationStep($this->_navigation);
            }
        }

        $this->initReportList();

        $this->view->headScript()->prependFile($this->view->baseUrl("/js/htmlReport.js"));
        $this->view->formTitle = "Report Summary";

        $healthcheck             = $this->getHealthcheck();
        $this->view->companyName = $healthcheck->getClient()->companyName;
        $this->view->reportName  = $healthcheck->getClient()->companyName;

        $this->view->navigationForm = new Healthcheck_Form_Healthcheck_Navigation(Healthcheck_Form_Healthcheck_Navigation::BUTTONS_BACK);
    }
}