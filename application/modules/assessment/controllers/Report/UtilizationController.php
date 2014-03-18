<?php

/**
 * Class Assessment_Report_UtilizationController
 */
class Assessment_Report_UtilizationController extends Assessment_Library_Controller_Action
{
    /**
     * Makes sure the user has access to this report, otherwise it sends them back
     */
    public function init ()
    {
        if (!My_Feature::canAccess(My_Feature::ASSESSMENT_UTILIZATION))
        {
            $this->_flashMessenger->addMessage(array(
                "error" => "You do not have permission to access this."
            ));

            $this->redirector('index', 'index', 'index');
        }

        parent::init();
    }

    public function indexAction ()
    {
        $this->view->headTitle('Assessment');
        $this->view->headTitle('Utilization');
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['Utilization']['active'] = true;
        $this->view->formats                                   = array(
            "/assessment/report_utilization/generate/format/excel" => $this->_excelFormat,
        );

        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            $assessmentViewModel             = $this->getAssessmentViewModel();
            $this->view->assessmentViewModel = $assessmentViewModel;
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate the Utilization report.");
        }
    }

    /**
     * The Generate Action
     */
    public function generateAction ()
    {
        $this->view->headTitle('Generate Utilization Report');
        $format = $this->_getParam("format", "excel");

        switch ($format)
        {
            case "excel" :
                $this->_helper->layout->disableLayout();
                $this->view->phpexcel = new PHPExcel();
                $this->initExcelUtilization();
                break;
            default :
                throw new Exception("Invalid Format Requested! ($format)");
                break;
        }

        $filename = $this->generateReportFilename($this->getAssessment()->getClient(), 'Utilization_Report') . ".$format";

        $this->initReportVariables($filename);

        // Render early
        try
        {
            $this->render($format . "/00_render");
        }
        catch (Exception $e)
        {
            throw new Exception("Controller caught the exception!", 0, $e);
        }
    }

    /**
     * Sets up the excel data array
     *
     * @throws Exception
     */
    public function initExcelUtilization ()
    {
        try
        {
            $assessmentViewModel = $this->getAssessmentViewModel();
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate Utilization excel report.");
        }

        $utilizationData = array();
        $deviceCounter   = 0;

        /**
         * @var $deviceInstance Proposalgen_Model_DeviceInstance
         */
        foreach ($assessmentViewModel->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
        {
            $utilizationData[$deviceCounter]['Manufacturer']                       = $deviceInstance->getMasterDevice()->getManufacturer()->displayname;
            $utilizationData[$deviceCounter]['Model']                              = $deviceInstance->getMasterDevice()->modelName;
            $utilizationData[$deviceCounter]['IP Address']                         = $deviceInstance->ipAddress;
            $utilizationData[$deviceCounter]['Serial Number']                      = $deviceInstance->serialNumber;
            $utilizationData[$deviceCounter]['Monthly Page Volume']                = $this->view->formatPageVolume($deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly());
            $utilizationData[$deviceCounter]['Maximum Monthly Recommended Volume'] = $this->view->formatPageVolume($deviceInstance->getMasterDevice()->getMaximumMonthlyPageVolume($assessmentViewModel->getCostPerPageSettingForCustomer()));
            $utilizationData[$deviceCounter]['Utilization Percent']                = $deviceInstance->calculatePercentOfMaximumRecommendedMaxVolume($assessmentViewModel->getCostPerPageSettingForDealer()) / 100;
            $deviceCounter++;
        }

        $this->view->utilizationData = $utilizationData;
    }
}