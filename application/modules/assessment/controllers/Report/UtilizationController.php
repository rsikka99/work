<?php
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;

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
            $this->_flashMessenger->addMessage([
                "error" => "You do not have permission to access this."
            ]);

            $this->redirectToRoute('assessment');
        }

        parent::init();
    }

    public function indexAction ()
    {
        $this->_pageTitle = ['Assessment', 'Utilization'];
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['Utilization']['active'] = true;
        $this->view->formats                                   = [
            "/assessment/report_utilization/generate/format/excel" => $this->_excelFormat,
        ];

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
        $this->_pageTitle = ['Generate Utilization Report'];
        $format           = $this->_getParam("format", "excel");

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

        $filename = $this->generateReportFilename($this->getAssessment()->getClient(), 'Utilization Report') . '.xlsx';

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

        $utilizationData = [];
        $deviceCounter   = 0;

        /**
         * @var $deviceInstance DeviceInstanceModel
         */
        foreach ($assessmentViewModel->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $deviceInstance)
        {
            $utilizationData[$deviceCounter]['Device']                             = $deviceInstance->getMasterDevice()->getFullDeviceName();
            $utilizationData[$deviceCounter]['IP Address']                         = $deviceInstance->ipAddress;
            $utilizationData[$deviceCounter]['Serial Number']                      = $deviceInstance->serialNumber;
            $utilizationData[$deviceCounter]['Monthly Page Volume']                = $deviceInstance->getPageCounts()->getCombinedPageCount()->getMonthly();
            $utilizationData[$deviceCounter]['Maximum Monthly Recommended Volume'] = $deviceInstance->getMasterDevice()->maximumRecommendedMonthlyPageVolume;
            $utilizationData[$deviceCounter]['Utilization Percent']                = $deviceInstance->calculatePercentOfMaximumRecommendedMaxVolume($assessmentViewModel->getCostPerPageSettingForDealer()) / 100;
            $deviceCounter++;
        }

        $this->view->utilizationData = $utilizationData;
    }
}