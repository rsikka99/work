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
                $upload->setDestination($this->view->App()->uploadPath);
                
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
                        
                        Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->deleteAllRowsForReport($reportId);
                        $this->parseCSVUpload($lines);
                        
                        // Reset the report stage here since we've cleared our mappings and such.
                        $report->setReportStage(Proposalgen_Model_Report_Step::STEP_FLEETDATA_UPLOAD);
                        
                        $this->_helper->flashMessenger(array (
                                'success' => "Fleet Data Imported Successfully." 
                        ));
                    }
                    catch ( Exception $e )
                    {
                        throw new Exception("Error Uploading.", 0, $e);
                        $this->_helper->flashMessenger(array (
                                'danger' => "Your file was not saved. Please double check the file and try again. If you continue to experience problems saving, contact your administrator." 
                        ));
                    }
                    
                    // Delete the file after we're done with it.
                    unlink($upload->getFileName());
                }
                else
                {
                    if ($upload->isUploaded())
                    {
                        $this->_helper->flashMessenger(array (
                                'danger' => implode("<br>", $upload->getMessages()) 
                        ));
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array (
                                'warning' => 'You must first select a file to upload.' 
                        ));
                    }
                }
                
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
        $currentDateTime = date('Y-m-d H:i:s');
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $dateInputFormat = "mm/dd/yyyy HH:mm:ss";
        $dateOutputFormat = "yyyy/mm/dd HH:mm:ss";
        $reportId = $this->getReport()->getReportId();
        $minimumDeviceAgeInDays = 4;
        $isValid = true;
        
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
                $devices [] = new ArrayObject(array_combine($headers, $line), ArrayObject::ARRAY_AS_PROPS);
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
                    'startmeterprintcolor', 
                    'endmeterprintcolor', 
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
                $rowsToSave = array ();
                
                // Loop through all the lines.
                foreach ( $devices as $deviceRow )
                {
                    // Convert some fields that may have the text 'false' to a boolean value
                    $deviceRow->is_color = (strcasecmp($deviceRow->is_color, 'false') === 0) ? 0 : 1;
                    $deviceRow->is_copier = (strcasecmp($deviceRow->is_copier, 'false') === 0) ? 0 : 1;
                    $deviceRow->is_scanner = (strcasecmp($deviceRow->is_scanner, 'false') === 0) ? 0 : 1;
                    $deviceRow->is_fax = (strcasecmp($deviceRow->is_fax, 'false') === 0) ? 0 : 1;
                    
                    // Convert some rows to null if they are 0 (empty)
                    $deviceRow->duty_cycle = (empty($deviceRow->duty_cycle)) ? null : $deviceRow->duty_cycle;
                    $deviceRow->ppm_black = (empty($deviceRow->ppm_black)) ? null : $deviceRow->ppm_black;
                    $deviceRow->ppm_color = (empty($deviceRow->ppm_color)) ? null : $deviceRow->ppm_color;
                    $deviceRow->black_prodcodeoem = (empty($deviceRow->black_prodcodeoem)) ? null : $deviceRow->black_prodcodeoem;
                    $deviceRow->black_yield = (empty($deviceRow->black_yield)) ? null : $deviceRow->black_yield;
                    $deviceRow->black_prodcostoem = (empty($deviceRow->black_prodcostoem)) ? null : $deviceRow->black_prodcostoem;
                    $deviceRow->cyan_prodcodeoem = (empty($deviceRow->cyan_prodcodeoem)) ? null : $deviceRow->cyan_prodcodeoem;
                    $deviceRow->cyan_yield = (empty($deviceRow->cyan_yield)) ? null : $deviceRow->cyan_yield;
                    $deviceRow->cyan_prodcostoem = (empty($deviceRow->cyan_prodcostoem)) ? null : $deviceRow->cyan_prodcostoem;
                    $deviceRow->magenta_prodcodeoem = (empty($deviceRow->magenta_prodcodeoem)) ? null : $deviceRow->magenta_prodcodeoem;
                    $deviceRow->magenta_yield = (empty($deviceRow->magenta_yield)) ? null : $deviceRow->magenta_yield;
                    $deviceRow->magenta_prodcostoem = (empty($deviceRow->magenta_prodcostoem)) ? null : $deviceRow->magenta_prodcostoem;
                    $deviceRow->yellow_prodcodeoem = (empty($deviceRow->yellow_prodcodeoem)) ? null : $deviceRow->yellow_prodcodeoem;
                    $deviceRow->yellow_yield = (empty($deviceRow->yellow_yield)) ? null : $deviceRow->yellow_yield;
                    $deviceRow->yellow_prodcostoem = (empty($deviceRow->yellow_prodcostoem)) ? null : $deviceRow->yellow_prodcostoem;
                    $deviceRow->wattspowernormal = (empty($deviceRow->wattspowernormal)) ? null : $deviceRow->wattspowernormal;
                    $deviceRow->wattspoweridle = (empty($deviceRow->wattspoweridle)) ? null : $deviceRow->wattspoweridle;
                    $deviceRow->startmeterlife = (empty($deviceRow->startmeterlife)) ? null : $deviceRow->startmeterlife;
                    $deviceRow->endmeterlife = (empty($deviceRow->endmeterlife)) ? null : $deviceRow->endmeterlife;
                    $deviceRow->startmeterblack = (empty($deviceRow->startmeterblack)) ? null : $deviceRow->startmeterblack;
                    $deviceRow->endmeterblack = (empty($deviceRow->endmeterblack)) ? null : $deviceRow->endmeterblack;
                    $deviceRow->startmetercolor = (empty($deviceRow->startmetercolor)) ? null : $deviceRow->startmetercolor;
                    $deviceRow->endmetercolor = (empty($deviceRow->endmetercolor)) ? null : $deviceRow->endmetercolor;
                    $deviceRow->startmeterprintblack = (empty($deviceRow->startmeterprintblack)) ? null : $deviceRow->startmeterprintblack;
                    $deviceRow->endmeterprintblack = (empty($deviceRow->endmeterprintblack)) ? null : $deviceRow->endmeterprintblack;
                    $deviceRow->startmeterprintcolor = (empty($deviceRow->startmeterprintcolor)) ? null : $deviceRow->startmeterprintcolor;
                    $deviceRow->endmeterprintcolor = (empty($deviceRow->endmeterprintcolor)) ? null : $deviceRow->endmeterprintcolor;
                    $deviceRow->startmetercopyblack = (empty($deviceRow->startmetercopyblack)) ? null : $deviceRow->startmetercopyblack;
                    $deviceRow->endmetercopyblack = (empty($deviceRow->endmetercopyblack)) ? null : $deviceRow->endmetercopyblack;
                    $deviceRow->startmetercopycolor = (empty($deviceRow->startmetercopycolor)) ? null : $deviceRow->startmetercopycolor;
                    $deviceRow->endmetercopycolor = (empty($deviceRow->endmetercopycolor)) ? null : $deviceRow->endmetercopycolor;
                    $deviceRow->startmeterscan = (empty($deviceRow->startmeterscan)) ? null : $deviceRow->startmeterscan;
                    $deviceRow->endmeterscan = (empty($deviceRow->endmeterscan)) ? null : $deviceRow->endmeterscan;
                    $deviceRow->startmeterfax = (empty($deviceRow->startmeterfax)) ? null : $deviceRow->startmeterfax;
                    $deviceRow->endmeterfax = (empty($deviceRow->endmeterfax)) ? null : $deviceRow->endmeterfax;
                    
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
                    
                    // If we have an empty manufacturer or device name then we cannot have an entry in our table
                    if (strlen($manufacturerName) === 0 || strlen($deviceName) === 0)
                    {
                        $printfleetDeviceId = 0;
                    }
                    else
                    {
                        // Check to see if one exists already
                        $pfDevice = Proposalgen_Model_Mapper_DevicePf::getInstance()->fetchByDeviceNameOrModelId($deviceName, $deviceRow->printermodelid);
                        
                        // If it doesn't then we make one.
                        if ($pfDevice === FALSE)
                        {
                            $pfDevice = new Proposalgen_Model_DevicePf();
                            $pfDevice->setPfModelId($deviceRow->printermodelid);
                            $pfDevice->setPfDbDeviceName($deviceName);
                            $pfDevice->setPfDbManufacturer($manufacturerName);
                            $pfDevice->setCreatedBy($userId);
                            $pfDevice->setDateCreated($currentDateTime);
                            
                            $insertId = Proposalgen_Model_Mapper_DevicePf::getInstance()->save($pfDevice);
                            $pfDevice->setDevicesPfId($insertId);
                        }
                    }
                    
                    // Prepare the dates
                    $startDate = (empty($deviceRow->startdate)) ? null : new Zend_Date($deviceRow->startdate, $dateInputFormat);
                    $endDate = (empty($deviceRow->enddate)) ? null : new Zend_Date($deviceRow->enddate, $dateInputFormat);
                    
                    $introductionDate = (empty($deviceRow->dateintroduction)) ? null : new Zend_Date($deviceRow->dateintroduction, $dateInputFormat);
                    $adoptionDate = (empty($deviceRow->dateadoption)) ? null : new Zend_Date($deviceRow->dateadoption, $dateInputFormat);
                    $discoveryDate = (empty($deviceRow->discoverydate)) ? null : new Zend_Date($deviceRow->discoverydate, $dateInputFormat);
                    
                    // Prepare the upload data collector model
                    $uploadDataCollectorRow = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->mapRowToObject($deviceRow);
                    //$uploadDataCollectorRow = new Proposalgen_Model_UploadDataCollectorRow($deviceRow);
                    $uploadDataCollectorRow->setReportId($reportId);
                    $uploadDataCollectorRow->setDevicesPfId($pfDevice->getDevicesPfId());
                    $uploadDataCollectorRow->setStartdate($startDate->toString($dateOutputFormat));
                    $uploadDataCollectorRow->setEnddate($endDate->toString($dateOutputFormat));
                    
                  
                    
                    // Validate the row
                    $deviceHasBadData = $uploadDataCollectorRow->IsValid();
                    $uploadDataCollectorRow->setInvalidData($deviceHasBadData);
                    $this->view->has_bad_data = $deviceHasBadData;
                    
                    $rowsToSave [] = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->mapObjectToRow($uploadDataCollectorRow);
                }
                
                // Save all our rows
                $saveResult = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->saveRows($rowsToSave);
                
                if (!$saveResult)
                {
                    throw new Exception("An error occured while saving the rows.");
                }
            }
        }
        return $isValid;
    }
}
