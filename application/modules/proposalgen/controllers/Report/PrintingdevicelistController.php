<?php

class Proposalgen_Report_PrintingdevicelistController extends Proposalgen_Library_Controller_Proposal
{

    /**
     * The solution Action will be used to display the solution report
     * Data is grabbed from the database, and displayed using HTML, CSS, and
     * javascript.
     */
    public function indexAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_FINISHED);

        $this->initReportList();
        $this->initHtmlReport();

        $this->view->availableReports->PrintingDeviceList->active = true;

        $this->view->formats                                      = array(
            "/proposalgen/report_printingdevicelist/generate/format/csv"  => $this->_csvFormat,
            "/proposalgen/report_printingdevicelist/generate/format/docx" => $this->_wordFormat
        );
        try
        {
            // Clear the cache for the report before proceeding
            $this->clearCacheForReport();
            $proposal             = $this->getProposal();
            $this->view->proposal = $proposal;
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate solution report.");
        }
    }

    /**
     * The Index action of the solution.
     */
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
                require_once ('PHPWord.php');
                $this->view->phpword = new PHPWord();
                $proposal = $this->getProposal();
                $graphs   = $this->cachePNGImages($proposal->getGraphs(), true);
                $proposal->setGraphs($graphs);
                $this->_helper->layout->disableLayout();
                break;
            case "pdf" :
                throw new Exception("PDF Format not available through this page yet!");
                break;
            default :
                throw new Exception("Invalid Format Requested!");
                break;
        }

        $filename = "printingdevicelist.$format";

        $this->initReportVariables($filename);

        // Render early
        try
        {
            $this->render($this->view->App()->theme . '/' . $format  . "/00_render");
        }
        catch (Exception $e)
        {
            throw new Exception("Controller caught the exception!", 0, $e);
        }
    }

    /**
     * Function to hold the old csv code for the printing device list
     *
     * @throws Exception
     */
    public function initCSVPrintingDeviceList ()
    {
        try
        {
            $proposal = $this->getProposal();

            $url = $this->view->serverUrl();
            $this->view->url = $url;
        }
        catch ( Exception $e )
        {
            throw new Exception("Could not generate printing device list report.");
        }
        // Instantiate the proposal and
        // assign to a view variable
        $this->view->proposal = $proposal;
        // Define our field titles
        $jitcompat = ($this->view->App()->theme === 'printiq' ? 'Office Depot ATR Compatible' : 'JIT Compatible');
        $this->view->appendix_titles = "Manufacturer,Model,IP Address,Serial,Purchased or Leased,AMPV," . $jitcompat;

        $appendix_values = "";
        try
        {
            /* @var $device Proposalgen_Model_DeviceInstance */
            foreach ( $proposal->getDevices()->allIncludedDeviceInstances as $device )
            {
                $row = array ();
                $row [] = $device->getMasterDevice()->getFullDeviceName();
                $row [] = $device->getMasterDevice()->modelName;
                $row [] = ($device->ipAddress) ? $device->ipAddress : "Unknown";
                $row [] = ($device->serialNumber) ? $device->serialNumber : "Unknown";
                $row [] = ($device->getMasterDevice()->isLeased) ? "Leased" : "Purchased";
                $row [] = $device->getAverageMonthlyPageCount();
                $row [] = ($device->isCapableOfReportingTonerLevels()) ? "Yes" : "No";
                $appendix_values .= implode(",", $row) . "\n";
            } // end Purchased Devices foreach
        }
        catch ( Exception $e )
        {
            throw new Exception("Error while generating CSV Report.", 0, $e);
        }
        $this->view->appendix_values = $appendix_values;

        // Define our field titles
        $this->view->excluded_titles = "Manufacturer,Model,Serial,IP Address,Exclusion Reason";

        $excluded_values = "";
        try
        {
            /* @var $device Proposalgen_Model_DeviceInstance */
            foreach ( $proposal->getExcludedDevices() as $device )
            {
                $row = array ();
                if($device->getIsMappedToMasterDevice())
                {
                    $row [] = $device->getMasterDevice()->getFullDeviceName();
                    $row [] = $device->getMasterDevice()->modelName;
                }
                else
                {
                    $row [] = $device->getRmsUploadRow()->manufacturer;
                    $row [] = $device->getRmsUploadRow()->modelName;
                }
                $row [] = (strlen($device->serialNumber) > 0) ? $device->serialNumber : "Unknown";
                $row [] = ($device->ipAddress) ? $device->ipAddress : "Unknown IP";
                $row [] = ($device->isExcluded) ? 'Manually excluded' : 'Device not mapped.';
                $excluded_values .= implode(",", $row) . "\n";
            } // end Purchased Devices foreach
        }
        catch ( Exception $e )
        {
            throw new Exception("Error while generating CSV Report.", 0, $e);
        }

        $this->view->excluded_values = $excluded_values;
        // Removes spaces from company name, otherwise CSV filename contains +
        // symbol
        $companyName = str_replace(array (
                                         " ",
                                         "/",
                                         "\\",
                                         ";",
                                         "?",
                                         "\"",
                                         "'",
                                         ",",
                                         "%",
                                         "&",
                                         "#",
                                         "@",
                                         "!",
                                         ">",
                                         "<",
                                         "+",
                                         "=",
                                         "{",
                                         "}",
                                         "[",
                                         "]",
                                         "|",
                                         "~",
                                         "`"
                                   ), "_", $this->view->proposal->Report->CustomerCompanyName);
    }
} // end index controller

