<?php

class Assessment_Report_OldDeviceListController extends Assessment_Library_Controller_Action
{

    public function indexAction ()
    {
        $this->_navigation->setActiveStep(Assessment_Model_Assessment_Steps::STEP_FINISHED);

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
            $proposal             = $this->getAssessmentViewModel();
            $this->view->proposal = $proposal;
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

        $filename = "olddevicelist.$format";

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
     * Function to hold the old csv code for the printing device list
     *
     * @throws Exception
     */
    public function initCSVOldDeviceList ()
    {
        try
        {
            $proposal = $this->getAssessmentViewModel();

            $url             = $this->view->serverUrl();
            $this->view->url = $url;
        }
        catch (Exception $e)
        {
            throw new Exception("Could not generate printing device list report.");
        }
        // Instantiate the proposal and assign to a view variable
        $this->view->proposal = $proposal;
        // Define our field titles
        $this->view->appendix_titles = "Device,IP Address,Serial,Age";

        $appendix_values = "";
        try
        {
            /* @var $device Proposalgen_Model_DeviceInstance */
            foreach ($proposal->getIncludedDevicesSortedAscendingByAge() as $device)
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

        // Removes spaces from company name, otherwise CSV filename contains + symbol
        $companyName = str_replace(array(
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

