<?php
use MPSToolbox\Legacy\Modules\Assessment\Models\AssessmentStepsModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;

/**
 * Class Assessment_Report_PrintingdevicelistController
 */
class Assessment_Report_PrintingdevicelistController extends Assessment_Library_Controller_Action
{
    /**
     * Makes sure the user has access to this report, otherwise it sends them back
     */
    public function init ()
    {
        if (!My_Feature::canAccess(My_Feature::ASSESSMENT_PRINTING_DEVICE_LIST))
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
        $this->_pageTitle = ['Assessment', 'Printing Device List'];
        $this->_navigation->setActiveStep(AssessmentStepsModel::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports['PrintingDeviceList']['active'] = true;
        $this->view->formats                                          = [
            "/assessment/report_printingdevicelist/generate/format/csv"  => $this->_csvFormat,
            "/assessment/report_printingdevicelist/generate/format/docx" => $this->_wordFormat
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
            throw new Exception("Could not generate the printing device list.");
        }
    }

    public function generateAction ()
    {
        $format = $this->_getParam("format", "docx");

        switch ($format)
        {
            case "csv" :
                $this->initCSVPrintingDeviceList();
                $this->_helper->layout->disableLayout();
                break;
            case "docx" :
                $this->view->phpword = new \PhpOffice\PhpWord\PhpWord();
                $this->_helper->layout->disableLayout();
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = $this->generateReportFilename($this->getAssessment()->getClient(), 'Printing Device List') . ".$format";

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
    public function initCSVPrintingDeviceList ()
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
        // Instantiate the assessmentViewModel and
        // assign to a view variable
        $this->view->assessmentViewModel = $assessmentViewModel;
        // Define our field titles

        $justInTimeCompatibleTitle = My_Brand::$jit . ' Compatible';

        $this->view->appendix_titles = ["Manufacturer", "Model", "IP Address", "Serial", "Age (Years)", "Purchased or Leased", "AMPV", $justInTimeCompatibleTitle];

        $appendix_values = "";
        try
        {
            /* @var $device DeviceInstanceModel */
            foreach ($assessmentViewModel->getDevices()->allIncludedDeviceInstances->getDeviceInstances() as $device)
            {
                $row               = [];
                $row []            = $device->getMasterDevice()->getManufacturer()->displayname;
                $row []            = $device->getMasterDevice()->modelName;
                $row []            = ($device->ipAddress) ? $device->ipAddress : "Unknown";
                $row []            = ($device->serialNumber) ? $device->serialNumber : "Unknown";
                $row []            = $device->getAge();
                $row []            = ($device->isLeased) ? "Leased" : "Purchased";
                $row []            = $this->view->formatPageVolume($device->getPageCounts()->getCombinedPageCount()->getMonthly());
                $row []            = ($device->isCapableOfReportingTonerLevels) ? "Yes" : "No";
                $appendix_values[] = $row;
            } // end Purchased Devices foreach
        }
        catch (Exception $e)
        {
            throw new Exception("Error while generating CSV Report.", 0, $e);
        }
        $this->view->appendix_values = $appendix_values;

        // Define our field titles
        $this->view->excluded_titles = ["Manufacturer", "Model", "Serial", "IP Address", "Exclusion Reason"];

        $excluded_values = "";
        try
        {
            /* @var $device DeviceInstanceModel */
            foreach ($assessmentViewModel->getExcludedDevices() as $device)
            {
                $row = [];
                if ($device->getIsMappedToMasterDevice())
                {
                    $row [] = $device->getMasterDevice()->getFullDeviceName();
                    $row [] = $device->getMasterDevice()->modelName;
                }
                else
                {
                    $row [] = $device->getRmsUploadRow()->manufacturer;
                    $row [] = $device->getRmsUploadRow()->modelName;
                }
                $row []            = (strlen($device->serialNumber) > 0) ? $device->serialNumber : "Unknown";
                $row []            = ($device->ipAddress) ? $device->ipAddress : "Unknown IP";
                $row []            = ($device->isExcluded) ? 'Manually excluded' : 'Device not mapped.';
                $excluded_values[] = $row;
            } // end Purchased Devices foreach
        }
        catch (Exception $e)
        {
            throw new Exception("Error while generating CSV Report.", 0, $e);
        }

        $this->view->excluded_values = $excluded_values;
    }
} // end index controller

