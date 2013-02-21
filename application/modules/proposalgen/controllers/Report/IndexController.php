<?php
class Proposalgen_Report_IndexController extends Proposalgen_Library_Controller_Proposal
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
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_FINISHED);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['goBack']))
            {
                $this->gotoPreviousStep();
            }
        }
        $this->initReportList();

        $this->view->headScript()->prependFile($this->view->baseUrl("/js/htmlReport.js"));
        $this->view->formTitle = "Report Summary";

        // proposal
        // $proposal                = $this->getProposal();
        // $this->view->proposal    = $proposal;
        $report                  = $this->getReport();
        $this->view->companyName = $report->getClient()->companyName; // Set company
        $this->view->reportName  = $report->getClient()->companyName;

        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation(Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK);
    }

    /**
     * Shows specific details of a device or unknown device
     */
    public function devicedetailsAction ()
    {
        $device   = null;
        $deviceId = $this->_request->getParam("id");
        $device   = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceId);
        if (is_null($device))
        {
            $this->_redirect("/report/showdevices");
        }

        $report        = $this->Report;
        $dealerCompany = Proposalgen_Model_DealerCompany::getCurrentUserCompany();
        $user          = Proposalgen_Model_User::getCurrentUser();

        $proposal = new Proposalgen_Model_Proposal_OfficeDepot($user, $dealerCompany, $report);
        // In order to be able to get IT CPP we need all devices to be loaded
        $proposal->getDevices();

        $reportMargin  = 1 - ((((int)$report->getReportPricingMargin())) / 100);
        $companyMargin = 1 - (((int)$dealerCompany->getDcPricingMargin()) / 100);

        Proposalgen_Model_DeviceInstance::processOverrides($device, $report, $reportMargin, $companyMargin);

        $this->view->device = $device;
        $this->_helper->layout->setLayout('blueprint');
    }

    /**
     * This action shows all the devices associated with a report
     */
    public function showdevicesAction ()
    {
        $deviceInstances     = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchAll(array("report_id = ?" => $this->ReportId));
        $this->view->devices = $deviceInstances;
        $this->_helper->layout->setLayout('blueprint');
    }
}
