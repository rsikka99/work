<?php
class Assessment_Report_IndexController extends Assessment_Library_Controller_Action
{
    function init ()
    {
        parent::init();

    }

    function preDispatch ()
    {
        parent::preDispatch();
        $this->view->ErrorMessages = array();
    }

    function postDispatch ()
    {
        parent::postDispatch();
        // $this->verifyReplacementDevices();
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
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_FINISHED);

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

        $assessment              = $this->getAssessment();
        $this->view->companyName = $assessment->getClient()->companyName;
        $this->view->reportName  = $assessment->getClient()->companyName;

        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation(Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK);
    }
}