<?php
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;

/**
 * Class Assessment_Report_OldDeviceListController
 */
class Assessment_Report_OldDeviceListController extends Assessment_Library_Controller_Action
{
    /**
     * Makes sure the user has access to this report, otherwise it sends them back
     */
    public function init ()
    {
        if (!My_Feature::canAccess(My_Feature::ASSESSMENT_OLD_DEVICE_LIST))
        {
            $this->_flashMessenger->addMessage(array(
                "error" => "You do not have permission to access this."
            ));

            $this->redirectToRoute('assessment');
        }

        parent::init();
    }

    public function indexAction ()
    {
        $this->_pageTitle = array('Assessment', 'Old Device List');
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['OldDeviceList']['active'] = true;

        $this->view->formats = array(
            "/assessment/report_olddevicelist/generate/format/csv" => $this->_csvFormat
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
            throw new Exception("Could not generate old device report.");
        }
    }

    public function generateAction ()
    {
        $format = $this->_getParam("format", "docx");

        switch ($format)
        {
            case "csv" :
                $this->initCSVOldDeviceList();
                $this->_helper->layout->disableLayout();
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = $this->generateReportFilename($this->getAssessment()->getClient(), 'Old Device List') . ".$format";

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
     * Function to hold the old CSV code for the printing device list
     *
     * @throws Exception
     */
    public function initCSVOldDeviceList ()
    {
        try
        {
            $assessmentViewModel = $this->getAssessmentViewModel();

            $url             = $this->view->serverUrl();
            $this->view->url = $url;
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate printing device list report.");
        }
        // Instantiate the assessmentViewModel and assign to a view variable
        $this->view->assessmentViewModel = $assessmentViewModel;
        // Define our field titles
        $this->view->appendix_titles = "Device,IP Address,Serial,Age";

        $appendix_values = "";
        try
        {
            /* @var $device DeviceInstanceModel */
            foreach ($assessmentViewModel->getIncludedDevicesSortedAscendingByAge() as $device)
            {
                $row    = array();
                $row [] = $device->getMasterDevice()->getFullDeviceName();
                $row [] = ($device->ipAddress) ? $device->ipAddress : "Unknown";
                $row [] = ($device->serialNumber) ? $device->serialNumber : "Unknown";
                $row [] = $device->getAge();
                $appendix_values .= implode(",", $row) . "\n";

            } // end Purchased Devices foreach
        }
        catch (Exception $e)
        {
            throw new Exception("Error while generating CSV Report.", 0, $e);
        }
        $this->view->appendix_values = $appendix_values;
    }
} // end index controller

