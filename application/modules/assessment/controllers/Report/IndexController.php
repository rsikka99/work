<?php
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use MPSToolbox\Legacy\Modules\Assessment\Forms\AssessmentNavigationForm;

/**
 * Class Assessment_Report_IndexController
 */
class Assessment_Report_IndexController extends Assessment_Library_Controller_Action
{
    function init ()
    {
        parent::init();

    }

    function preDispatch ()
    {
        parent::preDispatch();
        $this->view->ErrorMessages = [];
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
        $this->_pageTitle = ['Assessment', 'Report'];
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_FINISHED);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['goBack']))
            {
                $this->gotoPreviousNavigationStep($this->_navigation);
            }
        }
        $this->initReportList();

        $this->view->headScript()->appendFile($this->view->baseUrl("/js/app/legacy/HtmlReport.js?".date('Ymd')));
        $this->view->formTitle = "Report Summary";

        $assessment              = $this->getAssessment();
        $this->view->companyName = $assessment->getClient()->companyName;
        $this->view->reportName  = $assessment->getClient()->companyName;

        $this->view->navigationForm = new AssessmentNavigationForm(AssessmentNavigationForm::BUTTONS_BACK);
    }
}