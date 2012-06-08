<?php

class Proposalgen_FleetController extends Proposalgen_Library_Controller_Proposal
{

    /**
     * Users can upload/see uploaded data on this step
     */
    public function indexAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_FLEETDATA_UPLOAD);
        
        $report = $this->getReport();
        $reportId = $report->getReportId();
        
        $request = $this->getRequest();
        
        if ($request->isPost())
        {
            $values = $request->getPost();
            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else if (isset($values ["uploadData"]))
            {
                // Validate and recieve the file.
                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->setDestination($this->config->app->uploadPath);
                
                // Limit the extensions to csv files
                $upload->addValidator('Extension', false, array (
                        'csv' 
                ));
                $upload->getValidator('Extension')->setMessage('File must have the csv extension.');
                $upload->addValidator('Count', false, 1);
                $upload->getValidator('Count')->setMessage('You are only allowed to upload 1 file at a time.');
                
                // Limit the size of all files to be uploaded to maximum 4MB and
                // mimimum 500B
                $upload->addValidator('FilesSize', false, array (
                        'min' => '500B', 
                        'max' => '4MB' 
                ));
                $upload->getValidator('FilesSize')->setMessage('File size must be between 500B and 4MB.');
                
                // Try to get the file
                if ($upload->receive())
                {
                    $isValid = true;
                    
                    try
                    {
                        // Get the text from the file
                        $lines = file($upload->getFileName(), FILE_IGNORE_NEW_LINES);
                        
                        $this->parseCSVUpload($lines);
                    }
                    catch ( Exception $e )
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => "Your file was not saved. Please double check the file and try again. If you continue to experience problems saving, contact your administrator." 
                        ));
                    }
                }
                else
                {
                    $this->_helper->flashMessenger(array (
                            'danger' => implode("<br>", $upload->getMessages()) 
                    ));
                }
                
                // Reset the report stage here since we've cleared our mappings and such.
                $report->setReportStage(Proposalgen_Model_Report_Step::STEP_FLEETDATA_UPLOAD);
                
                $this->_helper->flashMessenger(array (
                        'success' => "Fleet Data Imported Successfully." 
                ));
                
                // Everytime we save anything related to a report, we should save it (updates the modification date)
                $this->saveReport(false);
            }
            else if (isset($values ["saveAndContinue"]))
            {
                // This is where the user has confirmed that the uploaded data is correct.
                

                // Everytime we save anything related to a report, we should save it (updates the modification date)
                $this->saveReport();
                
                // Call the base controller to send us to the next logical step in the proposal.
                $this->gotoNextStep();
            }
        }
        
        /*
         * Prepare the page after we've handled posting. This is because if we're redirecting the user there is no point
         * in hitting the database again.
         */
        
        // Check to see if we already have uploaded data
        $uploadDataCollectorRowMapper = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance();
        $count = $uploadDataCollectorRowMapper->countUploadDataCollectorRowsForReport($reportId);
        $this->view->has_data = ($count > 0);
        
        // Check to see if we have bad data
        $this->view->has_bad_data = false;
        if ($this->view->has_data)
        {
            $count = $uploadDataCollectorRowMapper->countUploadDataCollectorRowsForReport($reportId, true);
            $this->view->has_bad_data = ($count > 0);
        }
    }

    /**
     * Parses the uploaded csv data and saves it to the database if needed.
     *
     * @param string $lines
     *            (All the lines from the file)
     */
    public function parseCSVUpload ($lines)
    {
        // Variables
        $maxUploadLines = 1000;
        
        // Remove the headers from the start
        $headers = str_getcsv(strtolower(array_shift($lines)));
        
        // Count the number of valid rows in the file
        $devices = array ();
        foreach ( $lines as $key => $value )
        {
            // Parse the line. If the first two values aren't empty then add it to the list
            $line = str_getcsv($value);
            if (strlen($line [0]) > 0 && strlen($line [1]) > 0)
            {
                $devices [] = new ArrayObject(array_combine($headers, $value), ArrayObject::ARRAY_AS_PROPS);
            }
        }
        
        $validLineCount = count($devices);
        if (count($devices) > $maxUploadLines)
        {
            $isValid = false;
            $this->_helper->flashMessenger(array (
                    'danger' => "The uploaded file contains {$validLineCount} printers. The maximum number of printers supported in a single report is {$$maxUploadLines}. Please modify your file and try again." 
            ));
        }
        else
        {
            // required fields list
            $requiredHeaders = array (
                    'startdate', 
                    'enddate', 
                    'printermodelid', 
                    'ipaddress', 
                    'serialnumber', 
                    'modelname', 
                    'manufacturer', 
                    'is_color', 
                    'is_copier', 
                    'is_scanner', 
                    'is_fax', 
                    'ppm_black', 
                    'ppm_color', 
                    'dateintroduction', 
                    'dateadoption', 
                    'discoverydate', 
                    'black_prodcodeoem', 
                    'black_yield', 
                    'black_prodcostoem', 
                    'cyan_prodcodeoem', 
                    'cyan_yield', 
                    'cyan_prodcostoem', 
                    'magenta_prodcodeoem', 
                    'magenta_yield', 
                    'magenta_prodcostoem', 
                    'yellow_prodcodeoem', 
                    'yellow_yield', 
                    'yellow_prodcostoem', 
                    'duty_cycle', 
                    'wattspowernormal', 
                    'wattspoweridle', 
                    'startmeterlife', 
                    'endmeterlife', 
                    'startmeterblack', 
                    'endmeterblack', 
                    'startmetercolor', 
                    'endmetercolor', 
                    'startmeterprintblack', 
                    'endmeterprintblack', 
                    'startmeterprintcolor', 
                    'endmeterprintcolor', 
                    'startmetercopyblack', 
                    'endmetercopyblack', 
                    'startmetercopycolor', 
                    'endmetercopycolor', 
                    'startmeterscan', 
                    'endmeterscan', 
                    'startmeterfax', 
                    'endmeterfax', 
                    'tonerlevel_black', 
                    'tonerlevel_cyan', 
                    'tonerlevel_magenta', 
                    'tonerlevel_yellow' 
            );
            
            // Make sure we have all our required headers
            foreach ( $requiredHeaders as $key => $value )
            {
                if (! in_array(strtolower($value), $headers))
                {
                    $isValid = false;
                    $this->_helper->flashMessenger(array (
                            'danger' => "This file is missing required column: {$value}" 
                    ));
                }
            }
            
            // Finally if we're still valid, begin parsing.
            if ($isValid)
            {
                // Loop through all the lines.
                foreach ( $devices as $deviceRow )
                {
                    $manufacturerName = strtolower(trim($deviceRow->manufacturer));
                    
                    // In case the manufacturer is HP instead of hewlett-packard, change it to hewlett packard
                    if (strcmp('hp', $manufacturerName) === 0)
                    {
                        $manufacturerName = 'hewlett-packard';
                    }
                    
                    // The model name coming in often has the manufacturer attached to it. Remove it.
                    $deviceName = str_replace("{$manufacturerName} ", '', strtolower($deviceRow->modelname));
                    
                    // Hotfix for HP Printers
                    if (strcmp('hewlett-packard', $manufacturerName) === 0)
                    {
                        $deviceName = str_replace('hp ', '', $deviceName);
                    }
                    
                    // Now we make the manufacturer name pretty
                    $manufacturerName = ucwords($manufacturerName);
                    
                    if (strlen($manufacturerName) === 0 || strlen($deviceName) === 0)
                    {
                        $printfleetDeviceId = 0;
                    }
                    else
                    {
                        // Check to see if one exists already
                        $printfleetDevices = Proposalgen_Model_Mapper_DevicePf::getInstance();
                    }
                }
            }
        }
        return $isValid;
    }
}
