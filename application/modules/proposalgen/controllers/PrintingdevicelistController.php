<?php

class Proposalgen_PrintingdevicelistController extends My_Controller_Report
{

    public function indexAction ()
    {
        $this->_redirect("/proposal/printingdevicelist/generate");
    }

    /**
     * The Index action of the solution.
     */
    public function generateAction ()
    {
        $format = $this->_getParam("format", "csv");
        
        switch ($format)
        {
            case "csv" :
                $this->_helper->layout->disableLayout();
                $this->initCSVPrintingDeviceList();
                break;
            case "docx" :
                $this->initDocx();
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
            $this->render($format . "/00_render");
        }
        catch ( Exception $e )
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
            
            $url = $this->view->FullUrl();
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
        $this->view->appendix_titles = "Manufacturer,Model,IP Address,Serial,Purchased or Leased,AMPV,JIT Compatible";
        
        $appendix_values = "";
        try
        {
            foreach ( $this->view->proposal->getDevices() as $device )
            {
                $row = array ();
                $row [] = $device->getMasterDevice()
                    ->getManufacturer()
                    ->getManufacturerName();
                $row [] = $device->getMasterDevice()->getPrinterModel();
                $row [] = ($device->getIpAddress()) ? $device->getIpAddress() : "Unknown";
                $row [] = ($device->getSerialNumber()) ? $device->getSerialNumber() : "Unknown";
                $row [] = ($device->MasterDevice->IsLeased) ? "Leased" : "Purchased";
                $row [] = $device->AverageMonthlyPageCount;
                $row [] = ($device->getJITSuppliesSupported()) ? "Yes" : "No";
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
            foreach ( $this->view->proposal->getExcludedDevices() as $device )
            {
                $row = array ();
                $row [] = $device->getMasterDevice()
                    ->getManufacturer()
                    ->getManufacturerName();
                $row [] = $device->getMasterDevice()->getPrinterModel();
                $row [] = (strlen($device->getSerialNumber()) > 0) ? $device->getSerialNumber() : "Unknown";
                $row [] = ($device->IpAddress) ? $device->IpAddress : "Unknown IP";
                $row [] = $device->ExclusionReason;
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

