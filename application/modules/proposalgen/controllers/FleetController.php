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

        $report   = $this->getReport();
        $reportId = $report->id;

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
                $upload->addValidator('Extension', false, array(
                                                               'csv'
                                                          ));
                $upload->getValidator('Extension')->setMessage('File must have the csv extension.');
                $upload->addValidator('Count', false, 1);
                $upload->getValidator('Count')->setMessage('You are only allowed to upload 1 file at a time.');

                // Limit the size of all files to be uploaded to maximum 4MB and
                // mimimum 500B
                $upload->addValidator('FilesSize', false, array(
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
                        if ($this->parseCSVUpload($lines))
                        {

                            // Reset the report stage here since we've cleared our mappings and such.
                            $report->reportStage = Proposalgen_Model_Report_Step::STEP_FLEETDATA_UPLOAD;

                            $this->_helper->flashMessenger(array(
                                                                'success' => "Fleet Data Imported Successfully."
                                                           ));
                        }
                    }
                    catch (Exception $e)
                    {
                        throw new Exception("Error Uploading.", 0, $e);
                        $this->_helper->flashMessenger(array(
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
                        $this->_helper->flashMessenger(array(
                                                            'danger' => implode("<br>", $upload->getMessages())
                                                       ));
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array(
                                                            'warning' => 'You must first select a file to upload.'
                                                       ));
                    }
                }

                // Everytime we save anything related to a report, we should save it (updates the modification date)
                $this->saveReport(false);
            }
            else if (isset($values ["saveAndContinue"]))
            {
                $count = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->countUploadDataCollectorRowsForReport($reportId);
                if ($count < 2)
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => "You must have at least 2 valid devices to continue."
                                                   ));
                }
                else
                {
                    // Everytime we save anything related to a report, we should save it (updates the modification date)
                    $this->saveReport();

                    // Call the base controller to send us to the next logical step in the proposal.
                    $this->gotoNextStep();
                }
            }
        }

        /*
         * Prepare the page after we've handled posting. This is because if we're redirecting the user there is no point
         * in hitting the database again.
         */

        // Check to see if we already have uploaded data
        $uploadDataCollectorRowMapper = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance();
        $count                        = $uploadDataCollectorRowMapper->countUploadDataCollectorRowsForReport($reportId);
        $this->view->has_data         = ($count > 0);

        // Check to see if we have bad data
        $this->view->bad_data = true;
        if ($this->view->has_data)
        {
            $count                = $uploadDataCollectorRowMapper->countUploadDataCollectorRowsForReport($reportId, true);
            $this->view->bad_data = ($count > 0);
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
        $maxUploadLines         = 1000;
        $currentDateTime        = date('Y-m-d H:i:s');
        $userId                 = Zend_Auth::getInstance()->getIdentity()->id;
        $dateInputFormat        = "mm/dd/yyyy HH:mm:ss";
        $dateOutputFormat       = "yyyy/mm/dd HH:mm:ss";
        $reportId               = $this->getReport()->id;
        $minimumDeviceAgeInDays = 4;
        $isValid                = true;

        // Remove the headers from the start
        $headers = str_getcsv(strtolower(array_shift($lines)));

        // Count the number of valid rows in the file
        $devices = array();
        foreach ($lines as $key => $value)
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
            $this->_helper->flashMessenger(array(
                                                'danger' => "The uploaded file contains {$validLineCount} printers. The maximum number of printers supported in a single report is {$maxUploadLines}. Please modify your file and try again."
                                           ));
        }
        else
        {
            // required fields list
            $requiredHeaders = array(
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
            foreach ($requiredHeaders as $key => $value)
            {
                if (!in_array(strtolower($value), $headers))
                {
                    $isValid = false;
                    $this->_helper->flashMessenger(array(
                                                        'danger' => "This file is missing required column: {$value}"
                                                   ));
                }
            }

            // Finally if we're still valid, begin parsing.
            if ($isValid)
            {
                $rowsToSave = array();

                // Loop through all the lines.
                foreach ($devices as $deviceRow)
                {
                    // Convert some fields that may have the text 'false' to a boolean value
                    $deviceRow->is_color   = (strcasecmp($deviceRow->is_color, 'false') === 0) ? 0 : 1;
                    $deviceRow->is_copier  = (strcasecmp($deviceRow->is_copier, 'false') === 0) ? 0 : 1;
                    $deviceRow->is_scanner = (strcasecmp($deviceRow->is_scanner, 'false') === 0) ? 0 : 1;
                    $deviceRow->is_fax     = (strcasecmp($deviceRow->is_fax, 'false') === 0) ? 0 : 1;

                    // Convert some rows to null if they are 0 (empty)
                    $deviceRow->duty_cycle           = (empty($deviceRow->duty_cycle)) ? null : $deviceRow->duty_cycle;
                    $deviceRow->ppm_black            = (empty($deviceRow->ppm_black)) ? null : $deviceRow->ppm_black;
                    $deviceRow->ppm_color            = (empty($deviceRow->ppm_color)) ? null : $deviceRow->ppm_color;
                    $deviceRow->black_prodcodeoem    = (empty($deviceRow->black_prodcodeoem)) ? null : $deviceRow->black_prodcodeoem;
                    $deviceRow->black_yield          = (empty($deviceRow->black_yield)) ? null : $deviceRow->black_yield;
                    $deviceRow->black_prodcostoem    = (empty($deviceRow->black_prodcostoem)) ? null : $deviceRow->black_prodcostoem;
                    $deviceRow->cyan_prodcodeoem     = (empty($deviceRow->cyan_prodcodeoem)) ? null : $deviceRow->cyan_prodcodeoem;
                    $deviceRow->cyan_yield           = (empty($deviceRow->cyan_yield)) ? null : $deviceRow->cyan_yield;
                    $deviceRow->cyan_prodcostoem     = (empty($deviceRow->cyan_prodcostoem)) ? null : $deviceRow->cyan_prodcostoem;
                    $deviceRow->magenta_prodcodeoem  = (empty($deviceRow->magenta_prodcodeoem)) ? null : $deviceRow->magenta_prodcodeoem;
                    $deviceRow->magenta_yield        = (empty($deviceRow->magenta_yield)) ? null : $deviceRow->magenta_yield;
                    $deviceRow->magenta_prodcostoem  = (empty($deviceRow->magenta_prodcostoem)) ? null : $deviceRow->magenta_prodcostoem;
                    $deviceRow->yellow_prodcodeoem   = (empty($deviceRow->yellow_prodcodeoem)) ? null : $deviceRow->yellow_prodcodeoem;
                    $deviceRow->yellow_yield         = (empty($deviceRow->yellow_yield)) ? null : $deviceRow->yellow_yield;
                    $deviceRow->yellow_prodcostoem   = (empty($deviceRow->yellow_prodcostoem)) ? null : $deviceRow->yellow_prodcostoem;
                    $deviceRow->wattspowernormal     = (empty($deviceRow->wattspowernormal)) ? null : $deviceRow->wattspowernormal;
                    $deviceRow->wattspoweridle       = (empty($deviceRow->wattspoweridle)) ? null : $deviceRow->wattspoweridle;
                    $deviceRow->startmeterlife       = (empty($deviceRow->startmeterlife)) ? null : $deviceRow->startmeterlife;
                    $deviceRow->endmeterlife         = (empty($deviceRow->endmeterlife)) ? null : $deviceRow->endmeterlife;
                    $deviceRow->startmeterblack      = (empty($deviceRow->startmeterblack)) ? null : $deviceRow->startmeterblack;
                    $deviceRow->endmeterblack        = (empty($deviceRow->endmeterblack)) ? null : $deviceRow->endmeterblack;
                    $deviceRow->startmetercolor      = (empty($deviceRow->startmetercolor)) ? null : $deviceRow->startmetercolor;
                    $deviceRow->endmetercolor        = (empty($deviceRow->endmetercolor)) ? null : $deviceRow->endmetercolor;
                    $deviceRow->startmeterprintblack = (empty($deviceRow->startmeterprintblack)) ? null : $deviceRow->startmeterprintblack;
                    $deviceRow->endmeterprintblack   = (empty($deviceRow->endmeterprintblack)) ? null : $deviceRow->endmeterprintblack;
                    $deviceRow->startmeterprintcolor = (empty($deviceRow->startmeterprintcolor)) ? null : $deviceRow->startmeterprintcolor;
                    $deviceRow->endmeterprintcolor   = (empty($deviceRow->endmeterprintcolor)) ? null : $deviceRow->endmeterprintcolor;
                    $deviceRow->startmetercopyblack  = (empty($deviceRow->startmetercopyblack)) ? null : $deviceRow->startmetercopyblack;
                    $deviceRow->endmetercopyblack    = (empty($deviceRow->endmetercopyblack)) ? null : $deviceRow->endmetercopyblack;
                    $deviceRow->startmetercopycolor  = (empty($deviceRow->startmetercopycolor)) ? null : $deviceRow->startmetercopycolor;
                    $deviceRow->endmetercopycolor    = (empty($deviceRow->endmetercopycolor)) ? null : $deviceRow->endmetercopycolor;
                    $deviceRow->startmeterscan       = (empty($deviceRow->startmeterscan)) ? null : $deviceRow->startmeterscan;
                    $deviceRow->endmeterscan         = (empty($deviceRow->endmeterscan)) ? null : $deviceRow->endmeterscan;
                    $deviceRow->startmeterfax        = (empty($deviceRow->startmeterfax)) ? null : $deviceRow->startmeterfax;
                    $deviceRow->endmeterfax          = (empty($deviceRow->endmeterfax)) ? null : $deviceRow->endmeterfax;

                    $manufacturerName = strtolower(trim($deviceRow->manufacturer));

                    // In case the manufacturer is HP instead of hewlett-packard, change it to hewlett packard
                    if (strcmp('hp', $manufacturerName) === 0)
                    {
                        $manufacturerName = 'hewlett-packard';
                    }

                    // The model name coming in often has the manufacturer attached to it. Remove it.
                    $deviceName = str_replace("{$manufacturerName} ", '', strtolower($deviceRow->modelname));

                    // HotFix for HP Printers
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
                        if ($pfDevice === false)
                        {
                            $pfDevice                   = new Proposalgen_Model_DevicePf();
                            $pfDevice->pfModelId        = $deviceRow->printermodelid;
                            $pfDevice->pfDbDeviceName   = $deviceName;
                            $pfDevice->pfDbManufacturer = $manufacturerName;
                            $pfDevice->createdBy        = $userId;
                            $pfDevice->dateCreated      = $currentDateTime;

                            $insertId              = Proposalgen_Model_Mapper_DevicePf::getInstance()->save($pfDevice);
                            $pfDevice->devicesPfId = $insertId;
                        }
                    }

                    // Prepare the dates
                    $startDate = (empty($deviceRow->startdate)) ? null : new Zend_Date($deviceRow->startdate, $dateInputFormat);
                    $endDate   = (empty($deviceRow->enddate)) ? null : new Zend_Date($deviceRow->enddate, $dateInputFormat);

                    $introductionDate = (empty($deviceRow->dateintroduction)) ? null : new Zend_Date($deviceRow->dateintroduction, $dateInputFormat);
                    $adoptionDate     = (empty($deviceRow->dateadoption)) ? null : new Zend_Date($deviceRow->dateadoption, $dateInputFormat);
                    $discoveryDate    = (empty($deviceRow->discoverydate)) ? null : new Zend_Date($deviceRow->discoverydate, $dateInputFormat);

                    // Prepare the upload data collector model
                    $uploadDataCollectorRow = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->mapRowToObject($deviceRow);
                    //$uploadDataCollectorRow = new Proposalgen_Model_UploadDataCollectorRow($deviceRow);
                    $uploadDataCollectorRow->reportId    = $reportId;
                    $uploadDataCollectorRow->devicesPfId = $pfDevice->devicesPfId;
                    $uploadDataCollectorRow->startDate   = $startDate->toString($dateOutputFormat);
                    $uploadDataCollectorRow->endDate     = $endDate->toString($dateOutputFormat);

                    // Validate the row
                    $deviceHasBadData                    = $uploadDataCollectorRow->IsValid();
                    $uploadDataCollectorRow->invalidData = $deviceHasBadData;
                    $this->view->has_bad_data            = $deviceHasBadData;

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

    /**
     * This gets the devices for the jqgrid on the index page
     */
    public function previewlistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db           = Zend_Db_Table::getDefaultAdapter();
        $invalid_data = $this->_getParam('filter', 0);

        $page  = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx  = $_GET ['sidx'];
        $sord  = $_GET ['sord'];

        if (!$sidx)
        {
            $sidx = 9;
        }

        // get report id from session
        $report_id = $this->getReport()->id;

        try
        {
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array(
                            'udc' => 'pgen_upload_data_collector_rows'
                       ))
                ->where('invalid_data = ' . $invalid_data . ' AND report_id = ' . $report_id);
            $stmt   = $db->query($select);
            $result = $stmt->fetchAll();

            $count = count($result);
            if ($count > 0)
            {
                $total_pages = ceil($count / $limit);
            }
            else
            {
                $total_pages = 0;
            }
            if ($page > $total_pages)
            {
                $page = $total_pages;
            }
            $start = $limit * $page - $limit;
            if ($start < 0)
            {
                $start = 0;
            }

            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array(
                            'udc' => 'pgen_upload_data_collector_rows'
                       ))
                ->where('invalid_data = ' . $invalid_data . ' AND report_id = ' . $report_id)
                ->order($sidx . ' ' . $sord)
                ->limit($limit, $start);
            $stmt   = $db->query($select);
            $result = $stmt->fetchAll();

            if (count($result) > 0)
            {
                $i                 = 0;
                $formdata          = new stdClass();
                $formdata->page    = $page;
                $formdata->total   = $total_pages;
                $formdata->records = $count;
                foreach ($result as $key => $value)
                {
                    $formdata->rows [$i] ['id']   = $result [$key] ['id'];
                    $formdata->rows [$i] ['cell'] = array(
                        $result [$key] ['id'],
                        $result [$key] ['devices_pf_id'],
                        $result [$key] ['printermodelid'],
                        $result [$key] ['manufacturer'],
                        $result [$key] ['modelname'],
                        $result [$key] ['serialnumber'],
                        $result [$key] ['ipaddress'],
                        $result [$key] ['is_color'],
                        $result [$key] ['is_copier'],
                        $result [$key] ['is_scanner'],
                        $result [$key] ['is_fax'],
                        $result [$key] ['ppm_black'],
                        $result [$key] ['ppm_color'],
                        $result [$key] ['date_introduction'],
                        $result [$key] ['date_adoption'],
                        $result [$key] ['discovery_date'],
                        $result [$key] ['black_prodcodeoem'],
                        $result [$key] ['black_yield'],
                        $result [$key] ['black_prodcostoem'],
                        $result [$key] ['cyan_prodcodeoem'],
                        $result [$key] ['cyan_yield'],
                        $result [$key] ['cyan_prodcostoem'],
                        $result [$key] ['magenta_prodcodeoem'],
                        $result [$key] ['magenta_yield'],
                        $result [$key] ['magenta_prodcostoem'],
                        $result [$key] ['yellow_prodcodeoem'],
                        $result [$key] ['yellow_yield'],
                        $result [$key] ['yellow_prodcostoem'],
                        $result [$key] ['duty_cycle'],
                        $result [$key] ['wattspowernormal'],
                        $result [$key] ['wattspoweridle'],
                        $result [$key] ['startmeterlife'],
                        $result [$key] ['endmeterlife'],
                        $result [$key] ['startmeterblack'],
                        $result [$key] ['endmeterblack'],
                        $result [$key] ['startmetercolor'],
                        $result [$key] ['endmetercolor'],
                        $result [$key] ['startmeterprintblack'],
                        $result [$key] ['endmeterprintblack'],
                        $result [$key] ['startmeterprintcolor'],
                        $result [$key] ['endmeterprintcolor'],
                        $result [$key] ['startmetercopyblack'],
                        $result [$key] ['endmetercopyblack'],
                        $result [$key] ['startmetercopycolor'],
                        $result [$key] ['endmetercopycolor'],
                        $result [$key] ['startmeterscan'],
                        $result [$key] ['endmeterscan'],
                        $result [$key] ['startmeterfax'],
                        $result [$key] ['endmeterfax'],
                        $result [$key] ['tonerlevel_black'],
                        $result [$key] ['tonerlevel_cyan'],
                        $result [$key] ['tonerlevel_magenta'],
                        $result [$key] ['tonerlevel_yellow'],
                        $result [$key] ['startdate'],
                        $result [$key] ['enddate']
                    );
                    $i++;
                }
            }
            else
            {
                $formdata = array();
            }
        }
        catch (Exception $e)
        {
            My_Log::logException($e);
        }
        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * This handles the mapping of devices to our master devices
     */
    public function devicemappingAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_FLEETDATA_MAPDEVICES);

        $this->user_id = Zend_Auth::getInstance()->getIdentity()->id;

        Tangent_Timer::Milestone("Device Mapping Action Start");
        $db               = Zend_Db_Table::getDefaultAdapter();
        $this->view->grid = $this->_getParam('grid', 'none');

        $report_id = $this->getReport()->id;

        // get unmapped counts
        $select                     = new Zend_Db_Select($db);
        $select                     = $db->select()
            ->from(array(
                        'udc' => 'pgen_upload_data_collector_rows'
                   ))
            ->joinLeft(array(
                            'mmpf' => 'pgen_master_pf_device_matchups'
                       ), 'udc.devices_pf_id = mmpf.pf_device_id', array(
                                                                        'mmpf.master_device_id'
                                                                   ))
            ->joinLeft(array(
                            'pfdmu' => 'pgen_user_pf_device_matchups'
                       ), 'udc.devices_pf_id = pfdmu.pf_device_id AND pfdmu.user_id = ' . $this->user_id, array(
                                                                                                               'pfdmu.master_device_id'
                                                                                                          ))
            ->joinLeft(array(
                            'md' => 'pgen_master_devices'
                       ), 'md.id = pfdmu.master_device_id', array(
                                                                 'printer_model'
                                                            ))
            ->joinLeft(array(
                            'm' => 'manufacturers'
                       ), 'm.id = md.manufacturer_id', array(
                                                            'fullname'
                                                       ))
            ->where('udc.report_id = ?', $report_id)
            ->where('udc.invalid_data = 0')
            ->where('mmpf.master_device_id IS NULL')
            ->where('pfdmu.master_device_id > 0 || pfdmu.master_device_id IS NULL')
            ->group('udc.devices_pf_id')
            ->order('udc.modelname');
        $stmt                       = $db->query($select);
        $result                     = $stmt->fetchAll();
        $this->view->unmapped_count = count($result);
        if (count($result) > 0)
        {
            $this->view->message = "<p>We were unable to find a match for " . count($result) . " uploaded printer model(s).  You may search our master list for a match or add new models by clicking the corresponding \"Add\" button.<p>";
        }
        else
        {
            $this->view->message = "<p>All uploaded printers have been successfully mapped to an equivalent master printer. You may review mapped printers below or press &quot;Save and continue&quot;</p>";
        }

        // get mapped counts
        $select                   = new Zend_Db_Select($db);
        $select                   = $db->select()
            ->from(array(
                        'udc' => 'pgen_upload_data_collector_rows'
                   ))
            ->joinLeft(array(
                            'pfdmu' => 'pgen_user_pf_device_matchups'
                       ), 'udc.devices_pf_id = pfdmu.pf_device_id AND pfdmu.user_id = ' . $this->user_id, array(
                                                                                                               'pfdmu.master_device_id'
                                                                                                          ))
            ->joinLeft(array(
                            'mmpf' => 'pgen_master_pf_device_matchups'
                       ), 'udc.devices_pf_id = mmpf.pf_device_id', array(
                                                                        'mmpf.master_device_id'
                                                                   ))
            ->joinLeft(array(
                            'md' => 'pgen_master_devices'
                       ), 'md.id = mmpf.master_device_id', array(
                                                                'printer_model'
                                                           ))
            ->joinLeft(array(
                            'm' => 'manufacturers'
                       ), 'm.id = md.manufacturer_id', array(
                                                            'fullname'
                                                       ))
            ->where('udc.report_id = ?', $report_id, 'INTEGER')
            ->where('udc.invalid_data = 0')
            ->where('pfdmu.master_device_id IS NULL')
            ->where('mmpf.master_device_id > 0')
            ->group('udc.devices_pf_id')
            ->order('udc.modelname');
        $stmt                     = $db->query($select);
        $result                   = $stmt->fetchAll();
        $this->view->mapped_count = count($result);

        // check for previously uploaded data for report in device instance and
        // unknown device instance tables
        $notes  = '';
        $select = $db->select()
            ->from(array(
                        'udc' => 'pgen_upload_data_collector_rows'
                   ))
            ->where('udc.report_id = ' . $report_id);
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();

        Tangent_Timer::Milestone("Device Mapping Action End");
    }

    /**
     * Generates a list of devices that were not mapped automatically
     */
    public function devicemappinglistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();

        $this->user_id = Zend_Auth::getInstance()->getIdentity()->id;
        $report_id     = $this->getReport()->id;

        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array(
                        'udc' => 'pgen_upload_data_collector_rows'
                   ), array(
                           'id',
                           'report_id',
                           'devices_pf_id',
                           'printermodelid',
                           'modelname',
                           'manufacturer',
                           '(SELECT COUNT(*) AS count FROM pgen_upload_data_collector_rows AS sudc WHERE sudc.report_id=udc.report_id AND sudc.devices_pf_id=udc.devices_pf_id AND sudc.invalid_data = 0) AS group_count'
                      ))
            ->joinLeft(array(
                            'mmpf' => 'pgen_master_pf_device_matchups'
                       ), 'udc.devices_pf_id = mmpf.pf_device_id', array(
                                                                        'mmpf.master_device_id'
                                                                   ))
            ->joinLeft(array(
                            'pfdmu' => 'pgen_user_pf_device_matchups'
                       ), 'udc.devices_pf_id = pfdmu.pf_device_id AND pfdmu.user_id = ' . $this->user_id, array(
                                                                                                               'pfdmu.master_device_id'
                                                                                                          ))
            ->joinLeft(array(
                            'md' => 'pgen_master_devices'
                       ), 'md.id = pfdmu.master_device_id', array(
                                                                 'printer_model',
                                                                 'is_leased'
                                                            ))
            ->joinLeft(array(
                            'm' => 'manufacturers'
                       ), 'm.id = md.manufacturer_id', array(
                                                            'fullname'
                                                       ))
            ->where('udc.report_id = ' . $report_id . ' AND udc.invalid_data = 0 AND mmpf.master_device_id IS NULL AND (pfdmu.master_device_id > 0 || pfdmu.master_device_id IS NULL)')
            ->group('udc.devices_pf_id')
            ->order('udc.modelname');
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();

        $formdata = new stdClass();
        try
        {
            if (count($result) > 0)
            {

                $i = 0;
                foreach ($result as $key => $value)
                {
                    $is_added               = '';
                    $mapped_to_id           = '';
                    $mapped_to_modelname    = '';
                    $mapped_to_manufacturer = '';

                    $count = $result [$key] ['group_count'];

                    // set up mapped to suggestions
                    $is_leased                    = $result [$key] ['is_leased'];
                    $devices_pf_id                = $result [$key] ['devices_pf_id'];
                    $upload_data_collector_row_id = $result [$key] ['id'];

                    $mapped_to_id           = $result [$key] ['master_device_id'];
                    $mapped_to_modelname    = $result [$key] ['printer_model'];
                    $mapped_to_manufacturer = $result [$key] ['fullname'];

                    // check to see if device has been added
                    $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                    $where                        = $unknown_device_instanceTable->getAdapter()->quoteInto('report_id = ' . $report_id . ' AND upload_data_collector_row_id = ?', $upload_data_collector_row_id, 'INTEGER');
                    $unknown_device_instance      = $unknown_device_instanceTable->fetchRow($where);

                    if (count($unknown_device_instance) > 0)
                    {
                        $is_added = $key;
                    }
                    else
                    {
                        $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
                        $where                = $device_instanceTable->getAdapter()->quoteInto('report_id = ' . $report_id . ' AND upload_data_collector_row_id = ?', $upload_data_collector_row_id, 'INTEGER');
                        $device_instance      = $device_instanceTable->fetchRow($where);

                        if (count($device_instance) > 0)
                        {
                            $is_added = '';
                        }
                    }

                    $formdata->rows [$i] ['id']   = $upload_data_collector_row_id;
                    $formdata->rows [$i] ['cell'] = array(
                        $upload_data_collector_row_id,
                        $result [$key] ['devices_pf_id'],
                        $count,
                        ucwords(strtolower($result [$key] ['modelname'])),
                        $mapped_to_id,
                        $mapped_to_modelname,
                        $mapped_to_manufacturer,
                        null,
                        $is_added,
                        null
                    );
                    $i++;
                }
            }
        }
        catch (Exception $e)
        {
            // critical exception
            throw new Exception("Error Getting Data", 0, $e);
        }

        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * Gets a list of models for mapping auto complete
     */
    protected function getmodelsAction ()
    {
        $this->_helper->viewRenderer->setNoRender();
        $terms      = explode(" ", trim($_REQUEST ["searchText"]));
        $searchTerm = "%";
        foreach ($terms as $term)
        {
            $searchTerm .= "$term%";
        }
        // Fetch Devices like term
        $db = Zend_Db_Table::getDefaultAdapter();

        $sql = "SELECT concat(fullname, ' ', printer_model) as device_name, pgen_master_devices.id as master_device_id, fullname, printer_model FROM manufacturers
        JOIN pgen_master_devices on pgen_master_devices.manufacturer_id = manufacturers.id
        WHERE concat(fullname, ' ', printer_model) LIKE '%$searchTerm%' AND manufacturers.isDeleted = 0 ORDER BY device_name ASC LIMIT 10;";

        $results = $db->fetchAll($sql);
        // $results is an array of device names
        $devices = array();
        foreach ($results as $row)
        {
            $deviceName = $row ["fullname"] . " " . $row ["printer_model"];
            $deviceName = ucwords(strtolower($deviceName));
            $devices [] = array(
                "label"        => $deviceName,
                "value"        => $row ["master_device_id"],
                "manufacturer" => ucwords(strtolower($row ["fullname"]))
            );
        }

        $this->_helper->json($devices);
    }

    /**
     * Gets a list of devices that were mapped automatically
     */
    public function mastermappinglistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();

        $this->user_id = Zend_Auth::getInstance()->getIdentity()->id;
        $report_id     = $this->getReport()->id;
        $formdata      = new stdClass();

        $select = $db->select()
            ->from(array(
                        'udc' => 'pgen_upload_data_collector_rows'
                   ), array(
                           'id',
                           'report_id',
                           'devices_pf_id',
                           'printermodelid',
                           'modelname',
                           'manufacturer',
                           'is_excluded',
                           '(SELECT COUNT(*) AS count FROM pgen_upload_data_collector_rows AS sudc WHERE sudc.report_id=udc.report_id AND sudc.devices_pf_id=udc.devices_pf_id) AS group_count'
                      ))
            ->joinLeft(array(
                            'pfdmu' => 'pgen_user_pf_device_matchups'
                       ), 'udc.devices_pf_id = pfdmu.pf_device_id AND pfdmu.user_id = ' . $this->user_id, array(
                                                                                                               'master_device_id AS user_matchup_id'
                                                                                                          ))
            ->joinLeft(array(
                            'umd' => 'pgen_master_devices'
                       ), 'umd.id = pfdmu.master_device_id', array(
                                                                  'printer_model AS user_printer_model'
                                                             ))
            ->joinLeft(array(
                            'um' => 'manufacturers'
                       ), 'um.id = umd.manufacturer_id', array(
                                                              'fullname AS user_manufacturer_name'
                                                         ))
            ->joinLeft(array(
                            'mmpf' => 'pgen_master_pf_device_matchups'
                       ), 'udc.devices_pf_id = mmpf.pf_device_id', array(
                                                                        'master_device_id AS master_matchup_id'
                                                                   ))
            ->joinLeft(array(
                            'mmd' => 'pgen_master_devices'
                       ), 'mmd.id = mmpf.master_device_id', array(
                                                                 'printer_model AS master_printer_model',
                                                                 'is_leased'
                                                            ))
            ->joinLeft(array(
                            'mm' => 'manufacturers'
                       ), 'mm.id = mmd.manufacturer_id', array(
                                                              'fullname AS master_manufacturer_name'
                                                         ))
            ->where('udc.report_id = ?', $report_id, 'INTEGER')
            ->where('mmpf.master_device_id > 0')
            ->group('udc.devices_pf_id')
            ->order('udc.modelname');
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();

        try
        {
            if (count($result) > 0)
            {
                $i = 0;
                foreach ($result as $key => $value)
                {
                    $is_added               = '';
                    $mapped_to_id           = '';
                    $mapped_to_modelname    = '';
                    $mapped_to_manufacturer = '';

                    $count = $result [$key] ['group_count'];

                    // set up leased, mapped to suggestions
                    $is_leased                    = $result [$key] ['is_leased'];
                    $devices_pf_id                = $result [$key] ['devices_pf_id'];
                    $upload_data_collector_row_id = $result [$key] ['upload_data_collector_row_id'];

                    if ($result [$key] ['is_excluded'] == 1)
                    {
                        $mapped_to_id           = '';
                        $mapped_to_modelname    = '';
                        $mapped_to_manufacturer = '';
                    }
                    else if ($result [$key] ['user_matchup_id'] > 0)
                    {
                        $mapped_to_id           = $result [$key] ['user_matchup_id'];
                        $mapped_to_modelname    = $result [$key] ['user_printer_model'];
                        $mapped_to_manufacturer = $result [$key] ['user_manufacturer_name'];
                    }
                    else
                    {
                        $mapped_to_id           = $result [$key] ['master_matchup_id'];
                        $mapped_to_modelname    = $result [$key] ['master_printer_model'];
                        $mapped_to_manufacturer = $result [$key] ['master_manufacturer_name'];
                    }

                    // check to see if device has been added
                    $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                    $where                        = $unknown_device_instanceTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND upload_data_collector_row_id = ?', $upload_data_collector_row_id, 'INTEGER');
                    $unknown_device_instance      = $unknown_device_instanceTable->fetchRow($where);

                    if (count($unknown_device_instance) > 0)
                    {
                        $is_added = $key;
                    }
                    else
                    {
                        $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
                        $where                = $device_instanceTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND upload_data_collector_row_id = ?', $upload_data_collector_row_id, 'INTEGER');
                        $device_instance      = $device_instanceTable->fetchRow($where);

                        if (count($device_instance) > 0)
                        {
                            $is_added = '';
                        }
                    }

                    $formdata->rows [$i] ['id']   = $upload_data_collector_row_id;
                    $formdata->rows [$i] ['cell'] = array(
                        $upload_data_collector_row_id,
                        $result [$key] ['devices_pf_id'],
                        $count,
                        ucwords(strtolower($result [$key] ['modelname'])),
                        $mapped_to_id,
                        $mapped_to_modelname,
                        $mapped_to_manufacturer,
                        null,
                        $is_added,
                        null
                    );
                    $i++;
                }
            }
            else
            {
                $formdata = array();
            }
        }
        catch (Exception $e)
        {
            // critical exception
            throw new Exception("Error getting data", 0, $e);
        }

        // encode user data to return to the client:
        $json             = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    /**
     * Used to add an unknown device
     */
    public function adddeviceAction ()
    {
        $db                    = Zend_Db_Table::getDefaultAdapter();
        $this->view->formTitle = 'Add Unknown Printer';

        $this->user_id = Zend_Auth::getInstance()->getIdentity()->id;
        $report_id     = $this->getReport()->id;

        // add device form
        $form = new Proposalgen_Form_UnknownDevice(null, "edit");

        // hide device instance related fields
        $form->removeElement('ipaddress');
        $form->removeElement('serial_number');

        // fill toner_config dropdown
        $toner_configTable = new Proposalgen_Model_DbTable_TonerConfig();
        $toner_configs     = $toner_configTable->fetchAll();
        $currElement       = $form->getElement('toner_config');
        $currElement->addMultiOption('', 'Select Toner Config');
        foreach ($toner_configs as $row)
        {
            $currElement->addMultiOption($row ['id'], ucwords(strtolower($row ['name'])));
        }

        // hard code the filtered lists since there is no way to determine them
        // though the database
        $this->view->blackOnlyList     = "1:BLACK";
        $this->view->seperateColorList = "1:BLACK;2:CYAN;3:MAGENTA;4:YELLOW";
        $this->view->threeColorList    = "5:3 COLOR";
        $this->view->fourColorList     = "6:4 COLOR";

        // check if this page has been posted to
        if ($this->_request->isPost())
        {
            $date                         = date('Y-m-d H:i:s');
            $formData                     = $this->_request->getPost();
            $upload_data_collector_row_id = 0;

            // conditional requirements (if leased, don't make toners required)
            if (isset($formData ['request_support']) || isset($formData ['is_leased']))
            {
                $form->set_validation($formData);
            }

            if (isset($formData ["hdnID"]))
            {
                $upload_data_collector_row_id = $formData ["hdnID"];
            }
            else if (isset($formData ['btnSubmitRequest']))
            {
                $db->beginTransaction();
                try
                {
                    $ticket_id                    = $formData ['ticket_id'];
                    $devices_pf_id                = $formData ['devices_pf_id'];
                    $request_description          = $formData ['request_description'];
                    $upload_data_collector_row_id = $formData ['upload_data_collector_row_id'];

                    if ($ticket_id > 0)
                    {
                        $ticket_comment = $formData ['txtComment'];

                        if ($request_description != '')
                        {
                            // update ticket
                            $ticketTable = new Proposalgen_Model_DbTable_Ticket();
                            $ticketData  = array(
                                'description'  => $request_description,
                                'date_updated' => $date
                            );
                            $where       = $ticketTable->getAdapter()->quoteInto('id = ?', $ticket_id, 'INTEGER');
                            $ticketTable->update($ticketData, $where);

                            // add comment
                            if ($ticket_comment != '')
                            {
                                $ticket_commentsTable = new Proposalgen_Model_DbTable_TicketComment();
                                $ticket_commentsData  = array(
                                    'ticket_id'    => $ticket_id,
                                    'user_id'      => $this->user_id,
                                    'content'      => $ticket_comment,
                                    'date_created' => $date
                                );
                                $ticket_commentsTable->insert($ticket_commentsData);
                            }

                            $this->view->message = "Your ticket updates have been saved.";
                        }
                        else
                        {
                            $this->view->message = "You must enter a description for your ticket.";
                        }
                    }
                    else
                    {
                        $request_title = $formData ['request_title'];

                        // save ticket
                        $ticketTable = new Proposalgen_Model_DbTable_Ticket();
                        $ticketData  = array(
                            'user_id'      => $this->user_id,
                            'category_id'  => Proposalgen_Model_TicketCategory::PRINTFLEET_DEVICE_SUPPORT,
                            'status_id'    => Proposalgen_Model_TicketStatus::STATUS_NEW,
                            'title'        => $request_title,
                            'description'  => $request_description,
                            'date_created' => $date,
                            'date_updated' => $date
                        );
                        $ticket_id   = $ticketTable->insert($ticketData);

                        // get default pf data from upload_data_collector
                        $select = $db->select()
                            ->from(array(
                                        'udc' => 'pgen_upload_data_collector_rows'
                                   ))
                            ->where('report_id = ' . $report_id . ' AND devices_pf_id = ?', $devices_pf_id, 'INTEGER');
                        $stmt   = $db->query($select);
                        $result = $stmt->fetchAll();

                        // may return more then one record, but just grab data
                        // from first matching record
                        if (count($result) > 0)
                        {

                            $device_manufacturer   = $result [0] ['manufacturer'];
                            $printer_model         = $result [0] ['modelname'];
                            $launch_date           = $result [0] ['date_introduction'];
                            $device_cost           = 0;
                            $service_cost_per_page = 0;
                            $toner_config          = ($result [0] ['startmetercolor'] > 0 ? 2 : 1);
                            $is_copier             = $result [0] ['is_copier'];
                            $is_fax                = $result [0] ['is_fax'];
                            $is_duplex             = 0;
                            $is_scanner            = $result [0] ['is_scanner'];
                            $PPM_black             = $result [0] ['ppm_black'];
                            $PPM_color             = $result [0] ['ppm_color'];
                            $duty_cycle            = $result [0] ['duty_cycle'];
                            $watts_power_normal    = $result [0] ['wattspowernormal'];
                            $watts_power_idle      = $result [0] ['wattspoweridle'];
                        }

                        // save printer request ticket
                        $printer_requestTable = new Proposalgen_Model_DbTable_TicketPFRequest();
                        $printer_requestData  = array(
                            'ticket_id'             => $ticket_id,
                            'user_id'               => $this->user_id,
                            'pf_device_id'          => $devices_pf_id,
                            'manufacturer'          => $device_manufacturer,
                            'printer_model'         => $printer_model,
                            'launch_date'           => $launch_date,
                            'cost'                  => $device_cost,
                            'service_cost_per_page' => $service_cost_per_page,
                            'toner_config'          => $toner_config,
                            'is_copier'             => $is_copier,
                            'is_fax'                => $is_fax,
                            'is_duplex'             => $is_duplex,
                            'is_scanner'            => $is_scanner,
                            'PPM_black'             => $PPM_black,
                            'PPM_color'             => $PPM_color,
                            'duty_cycle'            => $duty_cycle,
                            'watts_power_normal'    => $watts_power_normal,
                            'watts_power_idle'      => $watts_power_idle
                        );

                        $printer_request_id = $printer_requestTable->insert($printer_requestData);

                        $this->view->message = "Support Request Submitted.";
                    }
                    $db->commit();
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    My_Log::logException($e);
                    throw new Exception("An error occurred saving mapping.", 0, $e);

                    $this->view->message = "There was an error saving your support request.";
                }
            }
            else if ($form->isValid($formData))
            {
                $valid_toners = true;
                $db->beginTransaction();
                $grid              = $formData ['grid'];
                $this->view->field = "";

                // validate toner config - simply make sure unneeded fields are
                // empty and no value saved
                $black_array       = array(
                    null,
                    null,
                    null
                );
                $cyan_array        = array(
                    null,
                    null,
                    null
                );
                $magenta_array     = array(
                    null,
                    null,
                    null
                );
                $yellow_array      = array(
                    null,
                    null,
                    null
                );
                $three_color_array = array(
                    null,
                    null,
                    null
                );
                $four_color_array  = array(
                    null,
                    null,
                    null
                );

                // COMP TONERS
                $black_comp_array       = array(
                    null,
                    null,
                    null
                );
                $cyan_comp_array        = array(
                    null,
                    null,
                    null
                );
                $magenta_comp_array     = array(
                    null,
                    null,
                    null
                );
                $yellow_comp_array      = array(
                    null,
                    null,
                    null
                );
                $three_color_comp_array = array(
                    null,
                    null,
                    null
                );
                $four_color_comp_array  = array(
                    null,
                    null,
                    null
                );

                switch ($formData ['toner_config'])
                {
                    case "1" :
                        // black only - sku / price / yield


                        if (($formData ['is_leased'] && empty($formData ['black_toner_yield'])) || (!$formData ['is_leased'] && (empty($formData ['black_toner_sku']) || empty($formData ['black_toner_cost']) || empty($formData ['black_toner_yield']))))
                        {
                            $valid_toners        = false;
                            $this->view->field   = "black";
                            $this->view->message = "Incomplete black toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        else
                        {
                            $black_array = array(
                                $formData ['black_toner_sku'],
                                $formData ['black_toner_cost'],
                                $formData ['black_toner_yield']
                            );
                        }

                        // validate black comp fields
                        if (!$formData ['is_leased'])
                        {

                            if (!empty($formData ['black_comp_sku']) || !empty($formData ['black_comp_cost']) || !empty($formData ['black_comp_yield']))
                            {
                                if (empty($formData ['black_comp_sku']) || empty($formData ['black_comp_cost']) || empty($formData ['black_comp_yield']))
                                {
                                    $valid_toners        = false;
                                    $this->view->field   = "black_comp";
                                    $this->view->message = "Incomplete compatible black toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                                else
                                {
                                    $black_comp_array = array(
                                        $formData ['black_comp_sku'],
                                        $formData ['black_comp_cost'],
                                        $formData ['black_comp_yield']
                                    );
                                }
                            }
                        }

                        break;
                    case "2" :
                        // 3 color - separated - sku / price / yield


                        if (($formData ['is_leased'] && empty($formData ['black_toner_yield'])) || (!$formData ['is_leased'] && (empty($formData ['black_toner_sku']) || empty($formData ['black_toner_cost']) || empty($formData ['black_toner_yield']))))
                        {
                            $valid_toners        = false;
                            $this->view->field   = "black";
                            $this->view->message = "Incomplete black toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        else
                        {
                            $black_array = array(
                                $formData ['black_toner_sku'],
                                $formData ['black_toner_cost'],
                                $formData ['black_toner_yield']
                            );
                        }
                        if (($formData ['is_leased'] && empty($formData ['cyan_toner_yield'])) || (!$formData ['is_leased'] && (empty($formData ['cyan_toner_sku']) || empty($formData ['cyan_toner_cost']) || empty($formData ['cyan_toner_yield']))))
                        {
                            $valid_toners        = false;
                            $this->view->field   = "cyan";
                            $this->view->message = "Incomplete cyan toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        else
                        {
                            $cyan_array = array(
                                $formData ['cyan_toner_sku'],
                                $formData ['cyan_toner_cost'],
                                $formData ['cyan_toner_yield']
                            );
                        }
                        if (($formData ['is_leased'] && empty($formData ['magenta_toner_yield'])) || (!$formData ['is_leased'] && (empty($formData ['magenta_toner_sku']) || empty($formData ['magenta_toner_cost']) || empty($formData ['magenta_toner_yield']))))
                        {
                            $valid_toners        = false;
                            $this->view->field   = "magenta";
                            $this->view->message = "Incomplete magenta toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        else
                        {
                            $magenta_array = array(
                                $formData ['magenta_toner_sku'],
                                $formData ['magenta_toner_cost'],
                                $formData ['magenta_toner_yield']
                            );
                        }
                        if (($formData ['is_leased'] && empty($formData ['yellow_toner_yield'])) || (!$formData ['is_leased'] && (empty($formData ['yellow_toner_sku']) || empty($formData ['yellow_toner_cost']) || empty($formData ['yellow_toner_yield']))))
                        {
                            $valid_toners        = false;
                            $this->view->field   = "yellow";
                            $this->view->message = "Incomplete yellow toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        else
                        {
                            $yellow_array = array(
                                $formData ['yellow_toner_sku'],
                                $formData ['yellow_toner_cost'],
                                $formData ['yellow_toner_yield']
                            );
                        }

                        // COMP 3 color - separated - sku / price / yield
                        if (!$formData ['is_leased'])
                        {

                            if (!empty($formData ['black_comp_sku']) || !empty($formData ['black_comp_cost']) || !empty($formData ['black_comp_yield']))
                            {
                                if (empty($formData ['black_comp_sku']) || empty($formData ['black_comp_cost']) || empty($formData ['black_comp_yield']))
                                {
                                    $valid_comps         = false;
                                    $this->view->field   = "black_comp";
                                    $this->view->message = "Incomplete compatible black toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                                else
                                {
                                    $black_comp_array = array(
                                        $formData ['black_comp_sku'],
                                        $formData ['black_comp_cost'],
                                        $formData ['black_comp_yield']
                                    );
                                }
                            }
                            if (!empty($formData ['cyan_comp_sku']) || !empty($formData ['cyan_comp_cost']) || !empty($formData ['cyan_comp_yield']))
                            {
                                if (empty($formData ['cyan_comp_sku']) || empty($formData ['cyan_comp_cost']) || empty($formData ['cyan_comp_yield']))
                                {
                                    $valid_comps         = false;
                                    $this->view->field   = "cyan_comp";
                                    $this->view->message = "Incomplete compatible cyan toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                                else
                                {
                                    $cyan_comp_array = array(
                                        $formData ['cyan_comp_sku'],
                                        $formData ['cyan_comp_cost'],
                                        $formData ['cyan_comp_yield']
                                    );
                                }
                            }
                            if (!empty($formData ['magenta_comp_sku']) || !empty($formData ['magenta_comp_cost']) || !empty($formData ['magenta_comp_yield']))
                            {
                                if (empty($formData ['magenta_comp_sku']) || empty($formData ['magenta_comp_cost']) || empty($formData ['magenta_comp_yield']))
                                {
                                    $valid_comps         = false;
                                    $this->view->field   = "magenta_comp";
                                    $this->view->message = "Incomplete compatible magenta toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                                else
                                {
                                    $magenta_comp_array = array(
                                        $formData ['magenta_comp_sku'],
                                        $formData ['magenta_comp_cost'],
                                        $formData ['magenta_comp_yield']
                                    );
                                }
                            }
                            if (!empty($formData ['yellow_comp_sku']) || !empty($formData ['yellow_comp_cost']) || !empty($formData ['yellow_comp_yield']))
                            {
                                if (empty($formData ['yellow_comp_sku']) || empty($formData ['yellow_comp_cost']) || empty($formData ['yellow_comp_yield']))
                                {
                                    $valid_comps         = false;
                                    $this->view->field   = "yellow_comp";
                                    $this->view->message = "Incomplete compatible yellow toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                                else
                                {
                                    $yellow_comp_array = array(
                                        $formData ['yellow_comp_sku'],
                                        $formData ['yellow_comp_cost'],
                                        $formData ['yellow_comp_yield']
                                    );
                                }
                            }
                        }
                        break;
                    case "3" :
                        // 3 color - combined - sku / price / yield


                        if (($formData ['is_leased'] && empty($formData ['black_toner_yield'])) || (!$formData ['is_leased'] && (empty($formData ['black_toner_sku']) || empty($formData ['black_toner_cost']) || empty($formData ['black_toner_yield']))))
                        {
                            $valid_toners        = false;
                            $this->view->field   = "black";
                            $this->view->message = "Incomplete black toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        else
                        {
                            $black_array = array(
                                $formData ['black_toner_sku'],
                                $formData ['black_toner_cost'],
                                $formData ['black_toner_yield']
                            );
                        }

                        if (($formData ['is_leased'] && empty($formData ['three_color_toner_yield'])) || (!$formData ['is_leased'] && (empty($formData ['three_color_toner_sku']) || empty($formData ['three_color_toner_cost']) || empty($formData ['three_color_toner_yield']))))
                        {
                            $valid_toners        = false;
                            $this->view->field   = "three_color";
                            $this->view->message = "Incomplete 3 color toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        else
                        {
                            $three_color_array = array(
                                $formData ['three_color_toner_sku'],
                                $formData ['three_color_toner_cost'],
                                $formData ['three_color_toner_yield']
                            );
                        }

                        // COMP 3 color - combined - sku / price / yield
                        if (!$formData ['is_leased'])
                        {

                            if (!empty($formData ['black_comp_sku']) || !empty($formData ['black_comp_cost']) || !empty($formData ['black_comp_yield']))
                            {
                                if (empty($formData ['black_comp_sku']) || empty($formData ['black_comp_cost']) || empty($formData ['black_comp_yield']))
                                {
                                    $valid_comps         = false;
                                    $this->view->field   = "black_comp";
                                    $this->view->message = "Incomplete compatible black toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                                else
                                {
                                    $black_comp_array = array(
                                        $formData ['black_comp_sku'],
                                        $formData ['black_comp_cost'],
                                        $formData ['black_comp_yield']
                                    );
                                }
                            }
                            if (!empty($formData ['three_color_comp_sku']) || !empty($formData ['three_color_comp_cost']) || !empty($formData ['three_color_comp_yield']))
                            {
                                if (empty($formData ['three_color_comp_sku']) || empty($formData ['three_color_comp_cost']) || empty($formData ['three_color_comp_yield']))
                                {
                                    $valid_comps         = false;
                                    $this->view->field   = "three_color_comp";
                                    $this->view->message = "Incomplete compatible 3 color toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                                else
                                {
                                    $three_color_comp_array = array(
                                        $formData ['three_color_comp_sku'],
                                        $formData ['three_color_comp_cost'],
                                        $formData ['three_color_comp_yield']
                                    );
                                }
                            }
                        }
                        break;
                    case "4" :
                        // 4 color - combined - sku / price / yield


                        if (($formData ['is_leased'] && empty($formData ['four_color_toner_yield'])) || (!$formData ['is_leased'] && (empty($formData ['four_color_toner_sku']) || empty($formData ['four_color_toner_cost']) || empty($formData ['four_color_toner_yield']))))
                        {
                            $valid_toners        = false;
                            $this->view->field   = "four_color";
                            $this->view->message = "Incomplete 4 color toner data supplied. Please fill out all fields.";
                            break;
                        }
                        else
                        {
                            $four_color_array = array(
                                $formData ['four_color_toner_sku'],
                                $formData ['four_color_toner_cost'],
                                $formData ['four_color_toner_yield']
                            );
                        }

                        // COMP 4 color - combined - sku / price / yield
                        if (!$formData ['is_leased'])
                        {
                            if (!empty($formData ['four_color_comp_sku']) || !empty($formData ['four_color_comp_cost']) || !empty($formData ['four_color_comp_yield']))
                            {
                                if (empty($formData ['four_color_comp_sku']) || empty($formData ['four_color_comp_cost']) || empty($formData ['four_color_comp_yield']))
                                {
                                    $valid_comps         = false;
                                    $this->view->field   = "four_color";
                                    $this->view->message = "Incomplete compatible 4 color toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                                else
                                {
                                    $four_color_comp_array = array(
                                        $formData ['four_color_comp_sku'],
                                        $formData ['four_color_comp_cost'],
                                        $formData ['four_color_comp_yield']
                                    );
                                }
                            }
                        }
                        break;
                    default :
                        break;
                }

                if ($valid_toners == true)
                {
                    try
                    {
                        // get common fields
                        $launch_date                  = new Zend_Date($formData ['mps_launch_date'], "mm/dd/yyyy HH:ii:ss");
                        $is_excluded                  = 0;
                        $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                        $unknown_device_instanceData  = array(
                            'device_manufacturer'     => ucwords(trim($formData ["device_manufacturer"])),
                            'printer_model'           => ucwords(trim($formData ["printer_model"])),
                            'launch_date'             => $launch_date->toString('yyyy-MM-dd HH:mm:ss'),
                            'is_copier'               => ($formData ['is_copier'] == true ? 1 : 0),
                            'is_scanner'              => ($formData ['is_scanner'] == true ? 1 : 0),
                            'is_fax'                  => ($formData ['is_fax'] == true ? 1 : 0),
                            'is_duplex'               => ($formData ['is_duplex'] == true ? 1 : 0),
                            'ppm_black'               => ($formData ['ppm_black'] > 0 ? $formData ['ppm_black'] : null),
                            'ppm_color'               => ($formData ['ppm_color'] > 0 ? $formData ['ppm_color'] : null),
                            'duty_cycle'              => ($formData ['duty_cycle'] > 0 ? $formData ['duty_cycle'] : null),
                            'watts_power_normal'      => ($formData ['watts_power_normal'] > 0 ? $formData ['watts_power_normal'] : null),
                            'watts_power_idle'        => ($formData ['watts_power_idle'] > 0 ? $formData ['watts_power_idle'] : null),
                            'cost'                    => ($formData ['cost'] > 0 ? $formData ['cost'] : null),
                            'toner_config_id'         => $formData ["toner_config"],
                            'black_toner_sku'         => $black_array [0],
                            'black_toner_cost'        => $black_array [1],
                            'black_toner_yield'       => $black_array [2],
                            'cyan_toner_sku'          => $cyan_array [0],
                            'cyan_toner_cost'         => $cyan_array [1],
                            'cyan_toner_yield'        => $cyan_array [2],
                            'magenta_toner_sku'       => $magenta_array [0],
                            'magenta_toner_cost'      => $magenta_array [1],
                            'magenta_toner_yield'     => $magenta_array [2],
                            'yellow_toner_sku'        => $yellow_array [0],
                            'yellow_toner_cost'       => $yellow_array [1],
                            'yellow_toner_yield'      => $yellow_array [2],
                            'three_color_toner_sku'   => $three_color_array [0],
                            'three_color_toner_cost'  => $three_color_array [1],
                            'three_color_toner_yield' => $three_color_array [2],
                            'four_color_toner_sku'    => $four_color_array [0],
                            'four_color_toner_cost'   => $four_color_array [1],
                            'four_color_toner_yield'  => $four_color_array [2],

                            'black_comp_sku'          => $black_comp_array [0],
                            'black_comp_cost'         => $black_comp_array [1],
                            'black_comp_yield'        => $black_comp_array [2],
                            'cyan_comp_sku'           => $cyan_comp_array [0],
                            'cyan_comp_cost'          => $cyan_comp_array [1],
                            'cyan_comp_yield'         => $cyan_comp_array [2],
                            'magenta_comp_sku'        => $magenta_comp_array [0],
                            'magenta_comp_cost'       => $magenta_comp_array [1],
                            'magenta_comp_yield'      => $magenta_comp_array [2],
                            'yellow_comp_sku'         => $yellow_comp_array [0],
                            'yellow_comp_cost'        => $yellow_comp_array [1],
                            'yellow_comp_yield'       => $yellow_comp_array [2],
                            'three_color_comp_sku'    => $three_color_comp_array [0],
                            'three_color_comp_cost'   => $three_color_comp_array [1],
                            'three_color_comp_yield'  => $three_color_comp_array [2],
                            'four_color_comp_sku'     => $four_color_comp_array [0],
                            'four_color_comp_cost'    => $four_color_comp_array [1],
                            'four_color_comp_yield'   => $four_color_comp_array [2],

                            'is_excluded'             => $is_excluded,
                            'is_leased'               => ($formData ['is_leased'] == true ? 1 : 0)
                        );
                        // loop through each instance and get instance specific data
                        $devices_pf_id = $formData ["devices_pf_id"];
                        $select        = new Zend_Db_Select($db);
                        $select        = $db->select()
                            ->from(array(
                                        'udc' => 'pgen_upload_data_collector_rows'
                                   ))
                            ->where('udc.report_id = ' . $report_id . ' AND udc.devices_pf_id = ?', $devices_pf_id, 'INTEGER');
                        $stmt          = $db->query($select);
                        $result        = $stmt->fetchAll();

                        foreach ($result as $key => $value)
                        {
                            $upload_data_collector_row_id = $result [$key] ["id"];

                            // check for unknown device
                            $select                  = new Zend_Db_Select($db);
                            $select                  = $db->select()
                                ->from(array(
                                            'udi' => 'pgen_unknown_device_instances'
                                       ))
                                ->where('udi.report_id = ' . $report_id . ' AND udi.upload_data_collector_row_id = ?', $upload_data_collector_row_id, 'INTEGER');
                            $stmt                    = $db->query($select);
                            $unknown_device_instance = $stmt->fetchAll();

                            if (count($unknown_device_instance) > 0)
                            {
                                $unknown_device_instance_id = $unknown_device_instance [0] ['upload_data_collector_row_id'];
                            }
                            else
                            {
                                $unknown_device_instance_id = 0;
                            }

                            // get jit support
                            $is_color    = $result [$key] ['is_color'];
                            $tonerLevels = array();
                            if ($is_color == 0)
                            {
                                $tonerLevels = array(
                                    'toner_level_black' => $result [$key] ['tonerlevel_black']
                                );
                            }
                            else
                            {
                                $tonerLevels = array(
                                    'toner_level_black'   => $result [$key] ['tonerlevel_black'],
                                    'toner_level_cyan'    => $result [$key] ['tonerlevel_cyan'],
                                    'toner_level_magenta' => $result [$key] ['tonerlevel_magenta'],
                                    'toner_level_yellow'  => $result [$key] ['tonerlevel_yellow']
                                );
                            }
                            $jit_supplies_supported = $this->determineJITSupport($is_color, $tonerLevels);

                            // get instance specific data
                            $start_date     = $result [$key] ["startdate"];
                            $end_date       = $result [$key] ["enddate"];
                            $discovery_date = $result [$key] ["discovery_date"];
                            $install_date   = null;
                            $date_created   = $date;

                            $startmeterlife         = $result [$key] ["startmeterlife"];
                            $endmeterlife           = $result [$key] ["endmeterlife"];
                            $start_meter_black      = $result [$key] ["startmeterblack"];
                            $end_meter_black        = $result [$key] ["endmeterblack"];
                            $start_meter_color      = $result [$key] ["startmetercolor"];
                            $end_meter_color        = $result [$key] ["endmetercolor"];
                            $start_meter_printblack = $result [$key] ["startmeterprintblack"];
                            $end_meter_printblack   = $result [$key] ["endmeterprintblack"];
                            $start_meter_printcolor = $result [$key] ["startmeterprintcolor"];
                            $end_meter_printcolor   = $result [$key] ["endmeterprintcolor"];
                            $start_meter_copyblack  = $result [$key] ["startmetercopyblack"];
                            $end_meter_copyblack    = $result [$key] ["endmetercopyblack"];
                            $start_meter_copycolor  = $result [$key] ["startmetercopycolor"];
                            $end_meter_copycolor    = $result [$key] ["endmetercopycolor"];
                            $start_meter_fax        = $result [$key] ["startmeterfax"];
                            $end_meter_fax          = $result [$key] ["endmeterfax"];
                            $start_meter_scan       = $result [$key] ["startmeterscan"];
                            $end_meter_scan         = $result [$key] ["endmeterscan"];

                            $unknown_device_instanceData ['upload_data_collector_row_id'] = $upload_data_collector_row_id;
                            $unknown_device_instanceData ['printermodelid']               = ucwords(trim($result [$key] ["printermodelid"]));
                            $unknown_device_instanceData ['mps_monitor_startdate']        = $start_date;
                            $unknown_device_instanceData ['mps_monitor_enddate']          = $end_date;
                            $unknown_device_instanceData ['mps_discovery_date']           = $discovery_date;
                            $unknown_device_instanceData ['install_date']                 = $install_date;
                            $unknown_device_instanceData ['printer_serial_number']        = ucwords(trim($result [$key] ["serialnumber"]));
                            $unknown_device_instanceData ['date_created']                 = $date_created;
                            $unknown_device_instanceData ['start_meter_life']             = $startmeterlife;
                            $unknown_device_instanceData ['end_meter_life']               = $endmeterlife;
                            $unknown_device_instanceData ['start_meter_black']            = $start_meter_black;
                            $unknown_device_instanceData ['end_meter_black']              = $end_meter_black;
                            $unknown_device_instanceData ['start_meter_color']            = $start_meter_color;
                            $unknown_device_instanceData ['end_meter_color']              = $end_meter_color;
                            $unknown_device_instanceData ['start_meter_printblack']       = $start_meter_printblack;
                            $unknown_device_instanceData ['end_meter_printblack']         = $end_meter_printblack;
                            $unknown_device_instanceData ['start_meter_printcolor']       = $start_meter_printcolor;
                            $unknown_device_instanceData ['end_meter_printcolor']         = $end_meter_printcolor;
                            $unknown_device_instanceData ['start_meter_copyblack']        = $start_meter_copyblack;
                            $unknown_device_instanceData ['end_meter_copyblack']          = $end_meter_copyblack;
                            $unknown_device_instanceData ['start_meter_copycolor']        = $start_meter_copycolor;
                            $unknown_device_instanceData ['end_meter_copycolor']          = $end_meter_copycolor;
                            $unknown_device_instanceData ['start_meter_fax']              = $start_meter_fax;
                            $unknown_device_instanceData ['end_meter_fax']                = $end_meter_fax;
                            $unknown_device_instanceData ['start_meter_scan']             = $start_meter_scan;
                            $unknown_device_instanceData ['end_meter_scan']               = $end_meter_scan;
                            $unknown_device_instanceData ['jit_supplies_supported']       = $jit_supplies_supported;
                            $unknown_device_instanceData ['ip_address']                   = ucwords(trim($result [$key] ["ipaddress"]));

                            // save instances
                            if ($unknown_device_instance_id > 0)
                            {
                                $where = $unknown_device_instanceTable->getAdapter()->quoteInto('report_id = ' . $report_id . ' AND id = ?', $unknown_device_instance_id, 'INTEGER');
                                $unknown_device_instanceTable->update($unknown_device_instanceData, $where);
                            }
                            else
                            {

                                $unknown_device_instanceData ['user_id']   = $this->_userId;
                                $unknown_device_instanceData ['report_id'] = $report_id;
                                $unknown_device_instance_id                = $unknown_device_instanceTable->insert($unknown_device_instanceData);
                            }

                            // check for device_instance (match on
                            // upload_data_collector_row_id)
                            $device_instance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchRow('upload_data_collector_row_id = ' . $upload_data_collector_row_id);
                            if ($device_instance)
                            {
                                $device_instance_id = $device_instance->getDeviceInstanceId();

                                // delete meters
                                $meter = Proposalgen_Model_Mapper_Meter::getInstance()->delete('device_instance_id = ' . $device_instance_id);

                                // delete device_instance
                                Proposalgen_Model_Mapper_DeviceInstance::getInstance()->delete('device_instance_id = ' . $device_instance_id);
                            }

                            // FIXME: (Lee Robert) - This was a quick hack to get this update working with the Tangent Mapper. It would be better to do a simple update using the new My_Model_Mapper_Abstract type of mapper. 
                            $uploadDataCollectorRow = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->find($upload_data_collector_row_id);
                            $uploadDataCollectorRow->isExcluded = 0;
                            Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->save($uploadDataCollectorRow);
                        }

                        // check for ticket for user/device_pf_id
                        $ticket_pf_requestTable = new Proposalgen_Model_DbTable_TicketPFRequest();
                        $where                  = $ticket_pf_requestTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND pf_device_id = ?', $devices_pf_id, 'INTEGER');
                        $ticket_pf_request      = $ticket_pf_requestTable->fetchRow($where);

                        if (count($ticket_pf_request) > 0)
                        {
                            // get ticket id
                            $ticket_id = $ticket_pf_request ['ticket_id'];

                            // get form data
                            $device_manufacturer = ucwords(trim($formData ["device_manufacturer"]));
                            $printer_model       = ucwords(trim($formData ["printer_model"]));
                            $launch_date         = $launch_date->toString('yyyy/MM/dd HH:ss');
                            $device_cost         = ($formData ['cost'] > 0 ? $formData ['cost'] : null);
                            $toner_config        = $formData ["toner_config"];
                            $is_copier           = ($formData ['is_copier'] == true ? 1 : 0);
                            $is_fax              = ($formData ['is_fax'] == true ? 1 : 0);
                            $is_scanner          = ($formData ['is_scanner'] == true ? 1 : 0);
                            $is_duplex           = ($formData ['is_duplex'] == true ? 1 : 0);
                            $ppm_black           = ($formData ['ppm_black'] > 0 ? $formData ['ppm_black'] : null);
                            $ppm_color           = ($formData ['ppm_color'] > 0 ? $formData ['ppm_color'] : null);
                            $duty_cycle          = ($formData ['duty_cycle'] > 0 ? $formData ['duty_cycle'] : null);
                            $watts_power_normal  = ($formData ['watts_power_normal'] > 0 ? $formData ['watts_power_normal'] : null);
                            $watts_power_idle    = ($formData ['watts_power_idle'] > 0 ? $formData ['watts_power_idle'] : null);

                            // update ticket request data
                            $ticket_pf_requestData = array(
                                'manufacturer'       => $device_manufacturer,
                                'printer_model'      => $printer_model,
                                'launch_date'        => $launch_date,
                                'cost'               => $device_cost,
                                'toner_config'       => $toner_config,
                                'is_copier'          => $is_copier,
                                'is_fax'             => $is_fax,
                                'is_duplex'          => $is_duplex,
                                'is_scanner'         => $is_scanner,
                                'PPM_black'          => $ppm_black,
                                'PPM_color'          => $ppm_color,
                                'duty_cycle'         => $duty_cycle,
                                'watts_power_normal' => $watts_power_normal,
                                'watts_power_idle'   => $watts_power_idle
                            );
                            $where                 = $ticket_pf_requestTable->getAdapter()->quoteInto('ticket_id = ?', $ticket_id, 'INTEGER');
                            $ticket_pf_requestTable->update($ticket_pf_requestData, $where);
                        }

                        // commit changes
                        $db->commit();

                        // redirect back to mapping page
                        $this->_redirect('proposalgen/fleet/devicemapping?grid=' . $grid);
                    }
                    catch (Exception $e)
                    {
                        $db->rollback();
                        My_Log::logException($e);
                        throw new Exception("An error occurred saving mapping.", 0, $e);
                        $this->view->message = "There was an error saving your unknown device.";
                    }
                }
            }
            else
            {
                // if formdata was not valid, repopulate form(error messages
                // from validations are automatically added)
                $this->view->message = "Form not valid. Please fill out all required fields.";
                $form->populate($formData);
            } // end else


            if ($upload_data_collector_row_id > 0)
            {
                // set default
                $toner_config        = 1;
                $allow_color_configs = true;

                // check to see if unknown device exists already and use it's
                // data
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array(
                                'udi' => 'pgen_unknown_device_instances'
                           ))
                    ->joinLeft(array(
                                    'udc' => 'pgen_upload_data_collector_rows'
                               ), 'udc.id = udi.upload_data_collector_row_id', array(
                                                                                    'devices_pf_id',
                                                                                    'is_color'
                                                                               ))
                    ->where('udi.report_id = ' . $report_id . ' AND udi.upload_data_collector_row_id = ?', $upload_data_collector_row_id, 'INTEGER');
                $stmt   = $db->query($select);
                $result = $stmt->fetchAll();

                // if no unknown device, then use uploaded data
                if (count($result) > 0)
                {
                    $printermodelid = $result [0] ['printermodelid'];

                    // get values
                    $devices_pf_id       = $result [0] ['devices_pf_id'];
                    $manufacturername    = strtolower($result [0] ['device_manufacturer']);
                    $devicename          = strtolower($result [0] ['printer_model']);
                    $tempDate            = $result [0] ['launch_date'];
                    $ppm_black           = $result [0] ['ppm_black'];
                    $ppm_color           = $result [0] ['ppm_color'];
                    $wattspowernormal    = $result [0] ['watts_power_normal'];
                    $wattspoweridle      = $result [0] ['watts_power_idle'];
                    $device_cost         = $result [0] ['cost'];
                    $duty_cycle          = $result [0] ['duty_cycle'];
                    $black_prodcodeoem   = $result [0] ['black_toner_sku'];
                    $black_yield         = $result [0] ['black_toner_yield'];
                    $black_prodcostoem   = $result [0] ['black_toner_cost'];
                    $yellow_prodcodeoem  = $result [0] ['yellow_toner_sku'];
                    $yellow_yield        = $result [0] ['yellow_toner_yield'];
                    $yellow_prodcostoem  = $result [0] ['yellow_toner_cost'];
                    $cyan_prodcodeoem    = $result [0] ['cyan_toner_sku'];
                    $cyan_yield          = $result [0] ['cyan_toner_yield'];
                    $cyan_prodcostoem    = $result [0] ['cyan_toner_cost'];
                    $magenta_prodcodeoem = $result [0] ['magenta_toner_sku'];
                    $magenta_yield       = $result [0] ['magenta_toner_yield'];
                    $magenta_prodcostoem = $result [0] ['magenta_toner_cost'];
                    $tcolor_prodcodeoem  = $result [0] ['three_color_toner_sku'];
                    $tcolor_yield        = $result [0] ['three_color_toner_yield'];
                    $tcolor_prodcostoem  = $result [0] ['three_color_toner_cost'];
                    $fcolor_prodcodeoem  = $result [0] ['four_color_toner_sku'];
                    $fcolor_yield        = $result [0] ['four_color_toner_yield'];
                    $fcolor_prodcostoem  = $result [0] ['four_color_toner_cost'];

                    $blackcomp_prodcodeoem   = $result [0] ['black_comp_sku'];
                    $blackcomp_yield         = $result [0] ['black_comp_yield'];
                    $blackcomp_prodcostoem   = $result [0] ['black_comp_cost'];
                    $yellowcomp_prodcodeoem  = $result [0] ['yellow_comp_sku'];
                    $yellowcomp_yield        = $result [0] ['yellow_comp_yield'];
                    $yellowcomp_prodcostoem  = $result [0] ['yellow_comp_cost'];
                    $cyancomp_prodcodeoem    = $result [0] ['cyan_comp_sku'];
                    $cyancomp_yield          = $result [0] ['cyan_comp_yield'];
                    $cyancomp_prodcostoem    = $result [0] ['cyan_comp_cost'];
                    $magentacomp_prodcodeoem = $result [0] ['magenta_comp_sku'];
                    $magentacomp_yield       = $result [0] ['magenta_comp_yield'];
                    $magentacomp_prodcostoem = $result [0] ['magenta_comp_cost'];
                    $tcolorcomp_prodcodeoem  = $result [0] ['three_color_comp_sku'];
                    $tcolorcomp_yield        = $result [0] ['three_color_comp_yield'];
                    $tcolorcomp_prodcostoem  = $result [0] ['three_color_comp_cost'];
                    $fcolorcomp_prodcodeoem  = $result [0] ['four_color_comp_sku'];
                    $fcolorcomp_yield        = $result [0] ['four_color_comp_yield'];
                    $fcolorcomp_prodcostoem  = $result [0] ['four_color_comp_cost'];

                    $toner_config = $result [0] ['toner_config_id'];
                    $is_copier    = $result [0] ['is_copier'];
                    $is_scanner   = $result [0] ['is_scanner'];
                    $is_fax       = $result [0] ['is_fax'];
                    $is_duplex    = $result [0] ['is_duplex'];
                    $is_leased    = $result [0] ['is_leased'];

                    // check color meters
                    if ($result [0] ['start_meter_color'] == 0 && $result [0] ['is_color'] == 0)
                    {
                        $allow_color_configs = false;
                    }
                }
                else
                {
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array(
                                    'udc' => 'pgen_upload_data_collector_rows'
                               ))
                        ->where('report_id = ' . $report_id . ' AND id = ?', $upload_data_collector_row_id, 'INTEGER');
                    $stmt   = $db->query($select);
                    $result = $stmt->fetchAll();

                    if (count($result) > 0)
                    {
                        $printermodelid = $result [0] ['printermodelid'];

                        // get values
                        $devices_pf_id    = $result [0] ['devices_pf_id'];
                        $manufacturername = strtolower($result [0] ['manufacturer']);
                        $devicename       = strtolower($result [0] ['modelname']);
                        $tempDate         = $result [0] ['date_introduction'];
                        $ppm_black        = $result [0] ['ppm_black'];
                        $ppm_color        = $result [0] ['ppm_color'];
                        $wattspowernormal = $result [0] ['wattspowernormal'];
                        $wattspoweridle   = $result [0] ['wattspoweridle'];
                        $device_cost      = '';
                        $duty_cycle       = $result [0] ['duty_cycle'];

                        $black_prodcodeoem   = $result [0] ['black_prodcodeoem'];
                        $black_yield         = $result [0] ['black_yield'];
                        $black_prodcostoem   = $result [0] ['black_prodcostoem'];
                        $yellow_prodcodeoem  = $result [0] ['yellow_prodcodeoem'];
                        $yellow_yield        = $result [0] ['yellow_yield'];
                        $yellow_prodcostoem  = $result [0] ['yellow_prodcostoem'];
                        $cyan_prodcodeoem    = $result [0] ['cyan_prodcodeoem'];
                        $cyan_yield          = $result [0] ['cyan_yield'];
                        $cyan_prodcostoem    = $result [0] ['cyan_prodcostoem'];
                        $magenta_prodcodeoem = $result [0] ['magenta_prodcodeoem'];
                        $magenta_yield       = $result [0] ['magenta_yield'];
                        $magenta_prodcostoem = $result [0] ['magenta_prodcostoem'];
                        $tcolor_prodcodeoem  = '';
                        $tcolor_yield        = '';
                        $tcolor_prodcostoem  = '';
                        $fcolor_prodcodeoem  = '';
                        $fcolor_yield        = '';
                        $fcolor_prodcostoem  = '';

                        $blackcomp_prodcodeoem   = '';
                        $blackcomp_yield         = '';
                        $blackcomp_prodcostoem   = '';
                        $yellowcomp_prodcodeoem  = '';
                        $yellowcomp_yield        = '';
                        $yellowcomp_prodcostoem  = '';
                        $cyancomp_prodcodeoem    = '';
                        $cyancomp_yield          = '';
                        $cyancomp_prodcostoem    = '';
                        $magentacomp_prodcodeoem = '';
                        $magentacomp_yield       = '';
                        $magentacomp_prodcostoem = '';
                        $tcolorcomp_prodcodeoem  = '';
                        $tcolorcomp_yield        = '';
                        $tcolorcomp_prodcostoem  = '';
                        $fcolorcomp_prodcodeoem  = '';
                        $fcolorcomp_yield        = '';
                        $fcolorcomp_prodcostoem  = '';

                        $toner_config = 1;

                        // check color meters
                        if ($result [0] ['startmetercolor'] == 0)
                        {
                            if ($result [0] ['is_color'] == 1)
                            {
                                $toner_config = 2;
                            }
                            else
                            {
                                $allow_color_configs = false;
                            }
                        }
                        else
                        {
                            $toner_config = 2;
                        }

                        $is_copier  = $result [0] ['is_copier'];
                        $is_scanner = $result [0] ['is_scanner'];
                        $is_fax     = $result [0] ['is_fax'];
                        $is_duplex  = 0;
                        $is_leased  = 0;
                    }
                    else
                    {
                        $this->view->message = "Device Not Found.";
                    }
                }

                // populate form with values
                $this->view->devices_pf_id                = $devices_pf_id;
                $this->view->printer_model_id             = $printermodelid;
                $this->view->upload_data_collector_row_id = $upload_data_collector_row_id;

                if (isset($formData ['hdnGrid']))
                {
                    $this->view->grid = $formData ['hdnGrid'];
                    $form->getElement('grid')->setValue($formData ['hdnGrid']);
                }
                $form->getElement('upload_data_collector_row_id')->setValue($upload_data_collector_row_id);
                $form->getElement('devices_pf_id')->setValue($devices_pf_id);

                // NOTE: if Hewlet-packard we also need to stip away HP
                $devicename = str_replace($manufacturername . ' ', '', $devicename);
                if ($manufacturername == 'hewlett-packard')
                {
                    $devicename = str_replace('hp ', '', $devicename);
                }
                $devicename = ucwords(trim($devicename));

                // prep HP manufacturer to Hewlett-Packard (do we want to do
                // this???)
                if ($manufacturername == "hp")
                {
                    $manufacturername = "hewlett-packard";
                }
                $manufacturername = ucwords(trim($manufacturername));

                $form->getElement('device_manufacturer')->setValue($manufacturername);
                $form->getElement('printer_model')->setValue($devicename);

                $launch_date = "";
                if ($tempDate != "0000-00-00 00:00:00")
                {
                    $launch_date = new Zend_Date($tempDate, "yyyy/mm/dd HH:ii:ss");
                    $launch_date = $launch_date->toString('mm/dd/yyyy');
                }
                $form->getElement('mps_launch_date')->setValue($launch_date);

                $form->getElement('is_copier')->setAttrib('checked', ($is_copier == "FALSE" ? 0 : 1));
                $form->getElement('is_scanner')->setAttrib('checked', ($is_scanner == "FALSE" ? 0 : 1));
                $form->getElement('is_fax')->setAttrib('checked', ($is_fax == "FALSE" ? 0 : 1));
                $form->getElement('is_duplex')->setAttrib('checked', ($is_duplex == "FALSE" ? 0 : 1));

                $form->getElement('ppm_black')->setValue($ppm_black);
                $form->getElement('ppm_color')->setValue($ppm_color);
                $form->getElement('duty_cycle')->setValue($duty_cycle);
                $form->getElement('watts_power_normal')->setValue($wattspowernormal);
                $form->getElement('watts_power_idle')->setValue($wattspoweridle);
                $form->getElement('cost')->setValue($device_cost);

                // toners
                $form->getElement('black_toner_sku')->setValue($black_prodcodeoem);
                $form->getElement('black_toner_yield')->setValue($black_yield);
                $form->getElement('black_toner_cost')->setValue(($black_prodcostoem > 0 ? $black_prodcostoem : null));
                $form->getElement('yellow_toner_sku')->setValue($yellow_prodcodeoem);
                $form->getElement('yellow_toner_yield')->setValue($yellow_yield);
                $form->getElement('yellow_toner_cost')->setValue(($yellow_prodcostoem > 0 ? $yellow_prodcostoem : null));
                $form->getElement('cyan_toner_sku')->setValue($cyan_prodcodeoem);
                $form->getElement('cyan_toner_yield')->setValue($cyan_yield);
                $form->getElement('cyan_toner_cost')->setValue(($cyan_prodcostoem > 0 ? $cyan_prodcostoem : null));
                $form->getElement('magenta_toner_sku')->setValue($magenta_prodcodeoem);
                $form->getElement('magenta_toner_yield')->setValue($magenta_yield);
                $form->getElement('magenta_toner_cost')->setValue(($magenta_prodcostoem > 0 ? $magenta_prodcostoem : null));

                // comp toners
                $form->getElement('black_comp_sku')->setValue($blackcomp_prodcodeoem);
                $form->getElement('black_comp_yield')->setValue($blackcomp_yield);
                $form->getElement('black_comp_cost')->setValue(($blackcomp_prodcostoem > 0 ? $blackcomp_prodcostoem : null));
                $form->getElement('yellow_comp_sku')->setValue($yellowcomp_prodcodeoem);
                $form->getElement('yellow_comp_yield')->setValue($yellowcomp_yield);
                $form->getElement('yellow_comp_cost')->setValue(($yellowcomp_prodcostoem > 0 ? $yellowcomp_prodcostoem : null));
                $form->getElement('cyan_comp_sku')->setValue($cyancomp_prodcodeoem);
                $form->getElement('cyan_comp_yield')->setValue($cyancomp_yield);
                $form->getElement('cyan_comp_cost')->setValue(($cyancomp_prodcostoem > 0 ? $cyancomp_prodcostoem : null));
                $form->getElement('magenta_comp_sku')->setValue($magentacomp_prodcodeoem);
                $form->getElement('magenta_comp_yield')->setValue($magentacomp_yield);
                $form->getElement('magenta_comp_cost')->setValue(($magentacomp_prodcostoem > 0 ? $magentacomp_prodcostoem : null));

                // check color meters
                if ($allow_color_configs == false)
                {
                    // remove color options from dropdown
                    // $form->getElement('toner_config')->removeMultiOption('3');
                    // $form->getElement('toner_config')->removeMultiOption('2');
                    // $form->getElement('toner_config')->removeMultiOption('4');
                }
                $form->getElement('toner_config')->setValue($toner_config);

                // toners not included in import
                $form->getElement('three_color_toner_sku')->setValue($tcolor_prodcodeoem);
                $form->getElement('three_color_toner_yield')->setValue($tcolor_yield);
                $form->getElement('three_color_toner_cost')->setValue(($tcolor_prodcostoem > 0 ? $tcolor_prodcostoem : null));
                $form->getElement('four_color_toner_sku')->setValue($fcolor_prodcodeoem);
                $form->getElement('four_color_toner_yield')->setValue($fcolor_yield);
                $form->getElement('four_color_toner_cost')->setValue(($fcolor_prodcostoem > 0 ? $fcolor_prodcostoem : null));

                // comp toners not included in import
                $form->getElement('three_color_comp_sku')->setValue($tcolorcomp_prodcodeoem);
                $form->getElement('three_color_comp_yield')->setValue($tcolorcomp_yield);
                $form->getElement('three_color_comp_cost')->setValue(($tcolorcomp_prodcostoem > 0 ? $tcolorcomp_prodcostoem : null));
                $form->getElement('four_color_comp_sku')->setValue($fcolorcomp_prodcodeoem);
                $form->getElement('four_color_comp_yield')->setValue($fcolorcomp_yield);
                $form->getElement('four_color_comp_cost')->setValue(($fcolorcomp_prodcostoem > 0 ? $fcolorcomp_prodcostoem : null));

                $form->getElement('is_leased')->setAttrib('checked', ($is_leased == "FALSE" ? 0 : 1));

                // if user has previously requested support for this device,
                // disable checkbox
                $tickets_pf_requestsTable = new Proposalgen_Model_DbTable_TicketPFRequest();
                $where                    = $tickets_pf_requestsTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND pf_device_id = ?', $devices_pf_id, 'INTEGER');
                $ticket_pf_request        = $tickets_pf_requestsTable->fetchRow($where);

                if (count($ticket_pf_request) > 0)
                {
                    $ticket_id = $ticket_pf_request ['ticket_id'];

                    if ($ticket_id > 0)
                    {
                        // allow ticket description to be edited
                        $this->view->edit_description = true;

                        // load details for ticket
                        $ticketsMapper             = Proposalgen_Model_Mapper_Ticket::getInstance();
                        $tickets                   = $ticketsMapper->find($ticket_id);

                        $this->view->ticket_number = $tickets->ticketId;
                        $this->view->ticket_title  = $tickets->title;
                        $this->view->reported_by      = $tickets->getUser()->username;
                        $this->view->ticket_type      = $tickets->getTicketCategory()->categoryName;//->categoryName
                        $this->view->ticket_details   = $tickets->description;
                        $this->view->ticket_status    = ucwords(strtolower($tickets->getTicketStatus()->statusName));
                        $this->view->ticket_status_id = ucwords(strtolower($tickets->statusId));

                        // get comment history
                        $ticket_comments_array = array();
                        $ticket_commentsMapper = Proposalgen_Model_Mapper_TicketComment::getInstance();
                        $ticket_comments       = $ticket_commentsMapper->fetchAll(array(
                                                                                       'ticket_id = ?' => $ticket_id
                                                                                  ));

                        foreach ($ticket_comments as $row)
                        {
                            $comment_date = new Zend_Date($row->commentDate, "yyyy-mm-dd HH:ii:ss");
                            $ticket_comments_array [] = array(
                                'username'     => $row->getUser()->username,
                                'date_created' => $comment_date->toString('mm/dd/yyyy'),
                                'content'      => $row->commentText
                            );
                        }
                        $this->view->ticket_comments = $ticket_comments_array;

                        // find pf_device
                        $ticketPfRequestMapper           = Proposalgen_Model_Mapper_TicketPFRequest::getInstance();
                        $ticketPfRequest                 = $ticketPfRequestMapper->find($ticket_id);
                        $this->view->devices_pf_id       = $ticketPfRequest->devicePfId;
                        $this->view->device_pf_name      = $ticketPfRequest->getDevicePf()->pfDbManufacturer . ' ' . $ticketPfRequest->getDevicePf()->pfDbDeviceName;
                        $this->view->user_suggested_name = $ticketPfRequest->deviceManufacturer . ' ' . $ticketPfRequest->printerModel;

                        // ticket exists, update ticket label
                        $form->getElement('request_support')->setLabel("View Support Ticket");
                    }
                }

                $mapped_to_id           = 0;
                $mapped_to_modelname    = '';
                $mapped_to_manufacturer = '';
                // loop through pf_device_matchup_users to find suggested mapping
                $select = $db->select()
                    ->from(array(
                                'pfdmu' => 'pgen_user_pf_device_matchups'
                           ), array(
                                   'pf_device_id',
                                   'master_device_id',
                                   'user_id'
                              ))
                    ->joinLeft(array(
                                    'md' => 'pgen_master_devices'
                               ), 'md.id = pfdmu.master_device_id', array(
                                                                         'printer_model'
                                                                    ))
                    ->joinLeft(array(
                                    'm' => 'manufacturers'
                               ), 'm.id = md.manufacturer_id', array(
                                                                    'displayname'
                                                               ))
                    ->where('pfdmu.pf_device_id = ' . $devices_pf_id . ' AND pfdmu.user_id = ' . $this->user_id);

                $stmt           = $db->query($select);
                $master_devices = $stmt->fetchAll();


                if (count($master_devices) > 0)
                {
                    $mapped_to_id           = $master_devices [0] ['master_device_id'];
                    $mapped_to_modelname    = $master_devices [0] ['printer_model'];
                    $mapped_to_manufacturer = $master_devices [0] ['manufacturer_name'];
                }
                else
                {
                    // loop through master_matchup_pf to find master mapping
                    $select         = $db->select()
                        ->from(array(
                                    'mmpf' => 'pgen_master_pf_device_matchups'
                               ), array(
                                       'pf_device_id',
                                       'master_device_id'
                                  ))
                        ->joinLeft(array(
                                        'md' => 'pgen_master_devices'
                                   ), 'md.id = mmpf.master_device_id', array(
                                                                            'printer_model'
                                                                       ))
                        ->joinLeft(array(
                                        'm' => 'manufacturers'
                                   ), 'm.id = md.manufacturer_id', array(
                                                                        'displayname'
                                                                   ))
                        ->where('mmpf.pf_device_id = ' . $devices_pf_id);
                    $stmt           = $db->query($select);
                    $master_devices = $stmt->fetchAll();

                    if (count($master_devices) > 0)
                    {
                        $mapped_to_id           = $master_devices [0] ['master_device_id'];
                        $mapped_to_modelname    = $master_devices [0] ['printer_model'];
                        $mapped_to_manufacturer = $master_devices [0] ['displayname'];
                    }
                }

                if ($mapped_to_id > 0)
                {
                    $this->view->mapped = "This printer is currently mapped to the " . $mapped_to_manufacturer . " " . $mapped_to_modelname . " by the System Administrator.";
                }
            }
        } // end if
        $this->view->deviceform = $form;
    }

    public function removedeviceAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();

        // get devices_pf_id
        $devices_pf_id = $this->_getParam('key', null);

        $db->beginTransaction();
        try
        {
            if ($devices_pf_id > 0)
            {
                // delete unknown_device_instances
                $upload_data_collectorTable   = new Proposalgen_Model_DbTable_UploadDataCollectorRow();
                $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();

                // get all uploaded rows for device
                $where                 = $upload_data_collectorTable->getAdapter()->quoteInto('devices_pf_id = ?', $devices_pf_id, 'INTEGER');
                $upload_data_collector = $upload_data_collectorTable->fetchAll($where);

                foreach ($upload_data_collector as $key => $value)
                {
                    $upload_data_collector_id = $upload_data_collector [$key] ['id'];
                    $where                    = $unknown_device_instanceTable->getAdapter()->quoteInto('upload_data_collector_row_id = ?', $upload_data_collector_id, 'INTEGER');
                    $unknown_device_instances = $unknown_device_instanceTable->fetchRow($where);
                    if (count($unknown_device_instances) > 0)
                    {
                        $unknown_device_instances_id = $unknown_device_instances ['id'];
                        $unknown_device_instanceTable->delete($where);
                    }
                }
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            echo $e . "<br />";
            $this->view->message = "Error removing device.";
            throw new Exception("An error occurred saving mapping.", 0, $e);
        }
    }

    public function savemappingAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_FLEETDATA_MAPDEVICES);

        Tangent_Timer::Milestone("Start Save Mapping");
        $db               = Zend_Db_Table::getDefaultAdapter();
        $this->view->grid = $this->_getParam('grid', 'none');

        $report_id = $this->getReport()->id;

        $date          = date('Y-m-d H:i:s T');
        $this->user_id = Zend_Auth::getInstance()->getIdentity()->id;

        $this->view->formTitle   = 'Upload Confirmation';
        $this->view->companyName = $this->getReportCompanyName();

        // get report id from session
        $report_id = $this->getReport()->id;

        if ($this->_request->isPost())
        {
            $db = Zend_Db_Table::getDefaultAdapter();

            $db->beginTransaction();
            try
            {
                $deviceArray     = array();
                $udcUpdateArray  = array();
                $metersDataArray = array();

                $formData = $this->_request->getPost();

                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array(
                                'udc' => 'pgen_upload_data_collector_rows'
                           ))
                    ->joinLeft(array(
                                    'di' => 'pgen_device_instances'
                               ), 'di.upload_data_collector_row_id = udc.id', array(
                                                                                   'di.id AS device_instance_id'
                                                                              ))
                    ->joinLeft(array(
                                    'udi' => 'pgen_unknown_device_instances'
                               ), 'udi.upload_data_collector_row_id = udc.id', array(
                                                                                    'id AS unknown_device_instance_id'
                                                                               ))
                    ->joinLeft(array(
                                    'mmpf' => 'pgen_master_pf_device_matchups'
                               ), 'udc.devices_pf_id = mmpf.pf_device_id', array(
                                                                                'master_device_id AS master_matchup_id'
                                                                           ))
                    ->joinLeft(array(
                                    'pfdmu' => 'pgen_user_pf_device_matchups'
                               ), 'udc.devices_pf_id = pfdmu.pf_device_id AND pfdmu.user_id = ' . $this->user_id, array(
                                                                                                                       'master_device_id AS user_matchup_id'
                                                                                                                  ))
                    ->joinLeft(array(
                                    'md' => 'pgen_master_devices'
                               ), 'md.id = pfdmu.master_device_id', array(
                                                                         'printer_model'
                                                                    ))
                    ->joinLeft(array(
                                    'm' => 'manufacturers'
                               ), 'm.id = md.manufacturer_id', array(
                                                                    'displayname'
                                                               ))
                    ->where('udc.report_id = ?', $report_id, 'INTEGER')
                    ->where('udc.invalid_data = 0')
                    ->where('udi.id IS NULL');
                $stmt   = $db->query($select);
                $result = $stmt->fetchAll();

                // *************************************************************
                // save device instances
                // *************************************************************
                $metersDataArray = array();

                foreach ($result as $key => $value)
                {
                    $is_leased        = 0;
                    $master_device_id = 0;

                    // get devices_pf_id
                    $devices_pf_id                = $result [$key] ['devices_pf_id'];
                    $upload_data_collector_row_id = $result [$key] ['id'];

                    // get mapped to master device id
                    if (isset($formData ['hdnMasterDevicesValue' . $devices_pf_id]))
                    {
                        $master_device_id = $formData ['hdnMasterDevicesValue' . $devices_pf_id];
                    }
                    else if ($result [$key] ['user_matchup_id'] > 0)
                    {
                        $master_device_id = $result [$key] ['user_matchup_id'];
                    }
                    else if ($result [$key] ['master_matchup_id'] > 0)
                    {
                        $master_device_id = $result [$key] ['master_matchup_id'];
                    }

                    if ($master_device_id > 0)
                    {
                        // get jit support
                        $is_color    = $result [$key] ['is_color'];
                        $tonerLevels = array();
                        if ($is_color == 0)
                        {
                            $tonerLevels = array(
                                'toner_level_black' => $result [$key] ['tonerlevel_black']
                            );
                        }
                        else
                        {
                            $tonerLevels = array(
                                'toner_level_black'   => $result [$key] ['tonerlevel_black'],
                                'toner_level_cyan'    => $result [$key] ['tonerlevel_cyan'],
                                'toner_level_magenta' => $result [$key] ['tonerlevel_magenta'],
                                'toner_level_yellow'  => $result [$key] ['tonerlevel_yellow']
                            );
                        }
                        $jit_supplies_supported = $this->determineJITSupport($is_color, $tonerLevels);

                        // save to device instance
                        $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
                        $devices_instanceData = array(
                            'id'                           => $result [$key] ['device_instance_id'],
                            'report_id'                    => $report_id,
                            'master_device_id'             => $master_device_id,
                            'upload_data_collector_row_id' => $upload_data_collector_row_id,
                            'serial_number'                => $result [$key] ['serialnumber'],
                            'mps_monitor_startdate'        => $result [$key] ['startdate'],
                            'mps_monitor_enddate'          => $result [$key] ['enddate'],
                            'mps_discovery_date'           => $result [$key] ['discovery_date'],
                            'jit_supplies_supported'       => ($jit_supplies_supported == true ? 1 : 0),
                            'ip_address'                   => $result [$key] ['ipaddress']
                        );
                        $deviceArray []       = $devices_instanceData;

                        // FIXME: (Lee Robert) - This was a quick hack to get this update working with the Tangent Mapper. It would be better to do a simple update using the new My_Model_Mapper_Abstract type of mapper. 
                        $uploadDataCollectorRow = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->find($upload_data_collector_row_id);
                        $uploadDataCollectorRow->isExcluded = 0;
                        Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->save($uploadDataCollectorRow);
                    }
                    else
                    {
                        // FIXME: (Lee Robert) - This was a quick hack to get this update working with the Tangent Mapper. It would be better to do a simple update using the new My_Model_Mapper_Abstract type of mapper. 
                        $uploadDataCollectorRow = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->find($upload_data_collector_row_id);
                        $uploadDataCollectorRow->isExcluded = 1;
                        Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->save($uploadDataCollectorRow);
                    }
                }
                $devicesMsg = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->saveRows($deviceArray);

                // *************************************************************
                // save meters
                // *************************************************************
                // get device instance records for report
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array(
                                'udc' => 'pgen_upload_data_collector_rows'
                           ))
                    ->joinLeft(array(
                                    'di' => 'pgen_device_instances'
                               ), 'di.id = udc.id', array(
                                                         'di.id AS device_instance_id'
                                                    ))
                    ->where('udc.report_id = ?', $report_id, 'INTEGER')
                    ->where('udc.invalid_data = 0')
                    ->where('di.id > 0');
                $stmt   = $db->query($select);
                $result = $stmt->fetchAll();

                $columns = array(
                    'life',
                    'black',
                    'color',
                    'printblack',
                    'printcolor',
                    'copyblack',
                    'copycolor',
                    'scan',
                    'fax'
                );

                $metersTable = new Proposalgen_Model_DbTable_Meter();
                foreach ($result as $key => $value)
                {
                    $device_instance_id = $result [$key] ['device_instance_id'];

                    // insert meter
                    foreach ($columns as $key2)
                    {
                        $meter_type  = $key2;
                        $start_meter = $result [$key] ["startmeter" . $meter_type];
                        $end_meter   = $result [$key] ["endmeter" . $meter_type];

                        $meter_type = strtoupper($meter_type);
                        switch ($meter_type)
                        {
                            case "PRINTCOLOR" :
                                $meter_type = "PRINT COLOR";
                                break;
                            case "COPYCOLOR" :
                                $meter_type = "COPY COLOR";
                                break;
                            case "PRINTBLACK" :
                                $meter_type = "PRINT BLACK";
                                break;
                            case "COPYBLACK" :
                                $meter_type = "COPY BLACK";
                                break;
                        }

                        if ($end_meter > 0 && $start_meter > 0)
                        {
                            // check to see if meter exists
                            $where  = $metersTable->getAdapter()->quoteInto('meter_type = "' . $meter_type . '" AND id = ?', $device_instance_id, 'INTEGER');
                            $meters = $metersTable->fetchRow($where);

                            $meter_id = null;
                            if (count($meters) > 0)
                            {
                                $meter_id = $meters ['id'];
                            }

                            $metersData         = array(
                                'id'                 => $meter_id,
                                'device_instance_id' => $device_instance_id,
                                'meter_type'         => $meter_type,
                                'start_meter'        => $start_meter,
                                'end_meter'          => $end_meter
                            );
                            $metersDataArray [] = $metersData;
                        }
                    }
                }
                $metersMsg = Proposalgen_Model_Mapper_Meter::getInstance()->saveRows($metersDataArray);

                // reset report stage flag
                $reportTable = new Proposalgen_Model_DbTable_Report();

                $db->commit();

                $this->saveReport();
                $this->gotoNextStep();

                // redirect back to mapping page
                $this->_redirect('/proposalgen/fleet/deviceleasing');
            }
            catch (Exception $e)
            {
                $db->rollback();
                throw new Exception("An error occurred saving mapping.", 0, $e);
            }
        }
    }

    /**
     * This function gets the name of the company the report was prepared for
     */
    public function getReportCompanyName ()
    {
        $session       = new Zend_Session_Namespace('report');
        $report_id     = $session->report_id;
        $questionTable = new Proposalgen_Model_DbTable_TextAnswer();
        $where         = $questionTable->getAdapter()->quoteInto('question_id = 4 AND report_id = ?', $report_id, 'INTEGER');
        $row           = $questionTable->fetchRow($where);
        if ($row ['textual_answer'])
        {
            return $row ['textual_answer'];
        }
        else
        {
            return null;
        }
    }

    /**
     *
     * @param $is_color    int
     *                     A switch indicating color ( 1 ) or b/w ( 0 ).
     * @param $tonerLevels array
     *            - An associative array of device toner levels
     *
     * @return boolean JIT Support
     *
     * @author Kevin Jervis
     */
    public function determineJITSupport ($is_color, $tonerLevels)
    {
        $JITCompatible   = false;
        $tonerLevelBlack = strtoupper($tonerLevels ['toner_level_black']);

        // If device is b/w, ensure it has a % for black toner.
        if ($is_color == 0)
        {
            if (strpos($tonerLevelBlack, '%'))
            {
                $JITCompatible = true;
            }
        }
        else
        {
            // Convert toner values to uppercase for comparison
            $tonerLevelCyan    = strtoupper($tonerLevels ['toner_level_cyan']);
            $tonerLevelMagenta = strtoupper($tonerLevels ['toner_level_magenta']);
            $tonerLevelYellow  = strtoupper($tonerLevels ['toner_level_yellow']);

            // If any toner reports a percentage, other toner levels must have
            // %, OK or LOW as value
            if (strpos($tonerLevelBlack, '%') || strpos($tonerLevelCyan, '%') || strpos($tonerLevelMagenta, '%') || strpos($tonerLevelYellow, '%'))
            {
                if ($tonerLevelBlack == 'LOW' || $tonerLevelBlack == 'OK' || strpos($tonerLevelBlack, '%'))
                {
                    if ($tonerLevelCyan == 'LOW' || $tonerLevelCyan == 'OK' || strpos($tonerLevelCyan, '%'))
                    {
                        if ($tonerLevelMagenta == 'LOW' || $tonerLevelMagenta == 'OK' || strpos($tonerLevelMagenta, '%'))
                        {
                            if ($tonerLevelYellow == 'LOW' || $tonerLevelYellow == 'OK' || strpos($tonerLevelYellow, '%'))
                            {
                                $JITCompatible = true;
                            }
                        }
                    }
                }
            }
        } // end else
        return (int)$JITCompatible;
    }

    public function deviceleasingAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_FLEETDATA_SUMMARY);

        $db = Zend_Db_Table::getDefaultAdapter();

        $this->view->formTitle   = 'Upload Summary';
        $this->view->companyName = $this->getReportCompanyName();

        $report_id = $this->getReport()->id;

        $upload_data_collectorTable = new Proposalgen_Model_DbTable_UploadDataCollectorRow();
        $where                      = $upload_data_collectorTable->getAdapter()->quoteInto('report_id = ?', $report_id, 'INTEGER');
        $result                     = $upload_data_collectorTable->fetchAll($where);
        $this->view->mappingArray   = $result;
        $this->view->upload_count   = count($result);

        // Get the excluded count
        $select                    = new Zend_Db_Select($db);
        $select                    = $db->select()
            ->from(array(
                        'udc' => 'pgen_upload_data_collector_rows'
                   ))
            ->where('(invalid_data = 1 OR is_excluded = 1) AND report_id = ' . $report_id);
        $stmt                      = $db->query($select);
        $this->view->exclude_count = count($stmt->fetchAll());

        $this->view->mapped_count = $this->view->upload_count - $this->view->exclude_count;

        // return instructional message
        $this->view->message = "<p>" . $this->view->mapped_count . " of " . $this->view->upload_count . " uploaded printers are mapped and available to include in your report. " . $this->view->exclude_count . " printer(s) have been excluded due to insufficient data.<p>";

        if ($this->_request->isPost())
        {
            // make sure all devices aren't excluded
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array(
                            'udc' => 'pgen_upload_data_collector_rows'
                       ))
                ->joinLeft(array(
                                'di' => 'pgen_device_instances'
                           ), 'di.upload_data_collector_row_id = udc.id', array(
                                                                               'id'
                                                                          ))
                ->joinLeft(array(
                                'udi' => 'pgen_unknown_device_instances'
                           ), 'udi.upload_data_collector_row_id = udc.id', array(
                                                                                'id'
                                                                           ))
                ->where('udc.invalid_data = 0 AND udc.report_id = ' . $report_id)
                ->where('di.is_excluded = 0 || udi.is_excluded = 0');
            $stmt   = $db->query($select);
            $result = $stmt->fetchAll();

            if (count($result) > 0)
            {
                $this->saveReport();
                $this->gotoNextStep();
            }
            else
            {
                $this->view->mapping_error = "<p class='warning'>You have marked all devices as excluded. You must include at least one printer to complete the report. Please review the printers below and try again.</p>";
            }
        }
    }

    public function deviceleasinglistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db           = Zend_Db_Table::getDefaultAdapter();
        $invalid_data = $this->_getParam('filter', 0);
        $formData     = new stdClass();
        $page         = $_GET ['page'];
        $limit        = $_GET ['rows'];
        $sortIndex    = $_GET ['sidx'];
        $sortOrder    = $_GET ['sord'];
        if (!$sortIndex)
        {
            $sortIndex = 9;
        }

        // get report id from session
        $report_id = $this->getReport()->id;

        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array(
                        'udc' => 'pgen_upload_data_collector_rows'
                   ))
            ->joinLeft(array(
                            'di' => 'pgen_device_instances'
                       ), 'di.upload_data_collector_row_id = udc.id', array(
                                                                           'di.id AS di_device_instance_id',
                                                                           'master_device_id',
                                                                           'is_excluded AS di_is_excluded'
                                                                      ))
            ->joinLeft(array(
                            'udi' => 'pgen_unknown_device_instances'
                       ), 'udi.upload_data_collector_row_id = udc.id', array(
                                                                            'udi.id AS udi_unknown_device_instance_id',
                                                                            'is_leased AS udi_is_leased',
                                                                            'is_excluded AS udi_is_excluded'
                                                                       ))
            ->joinLeft(array(
                            'md' => 'pgen_master_devices'
                       ), 'md.id = di.master_device_id', array(
                                                              'printer_model',
                                                              'is_leased'
                                                         ))
            ->joinLeft(array(
                            'm' => 'manufacturers'
                       ), 'm.id = md.manufacturer_id', array(
                                                            'fullname'
                                                       ))
            ->where('udc.report_id = ?', $report_id, 'INTEGER')
            ->where('udc.invalid_data = ?', $invalid_data, 'INTEGER')
            ->where('udc.is_excluded = 0')
            ->order('udc.modelname');
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();

        $count = count($result);
        if ($count > 0)
        {
            $total_pages = ceil($count / $limit);
        }
        else
        {
            $total_pages = 0;
        }

        if ($page > $total_pages)
        {
            $page = $total_pages;
        }

        $start = $limit * $page - $limit;
        if ($start < 0)
        {
            $start = 0;
        }

        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array(
                        'udc' => 'pgen_upload_data_collector_rows'
                   ))
            ->joinLeft(array(
                            'di' => 'pgen_device_instances'
                       ), 'di.upload_data_collector_row_id = udc.id', array(
                                                                           'di.id AS di_device_instance_id',
                                                                           'master_device_id',
                                                                           'is_excluded AS di_is_excluded'
                                                                      ))
            ->joinLeft(array(
                            'udi' => 'pgen_unknown_device_instances'
                       ), 'udi.upload_data_collector_row_id = udc.id', array(
                                                                            'udi.id AS udi_unknown_device_instance_id',
                                                                            'device_manufacturer AS udi_device_manufacturer',
                                                                            'printer_model AS udi_printer_model',
                                                                            'is_leased AS udi_is_leased',
                                                                            'is_excluded AS udi_is_excluded'
                                                                       ))
            ->joinLeft(array(
                            'md' => 'pgen_master_devices'
                       ), 'md.id = di.master_device_id', array(
                                                              'printer_model',
                                                              'is_leased'
                                                         ))
            ->joinLeft(array(
                            'm' => 'manufacturers'
                       ), 'm.id = md.manufacturer_id', array(
                                                            'fullname'
                                                       ))
            ->where('udc.report_id = ?', $report_id, 'INTEGER')
            ->where('udc.invalid_data = ?', $invalid_data, 'INTEGER')
            ->where('udc.is_excluded = 0')
            ->order($sortIndex . ' ' . $sortOrder)
            ->limit($limit, $start);
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();
        try
        {
            if (count($result) > 0)
            {
                $i                 = 0;
                $formData->page    = $page;
                $formData->total   = $total_pages;
                $formData->records = $count;
                foreach ($result as $key => $value)
                {
                    // set up mapped to suggestions
                    $ampv                         = 0;
                    $is_leased                    = $result [$key] ['is_leased'];
                    $devices_pf_id                = $result [$key] ['devices_pf_id'];
                    $upload_data_collector_row_id = $result [$key] ['id'];

                    $mapped_to    = '';
                    $mapped_to_id = null;

                    if ($result [$key] ['udi_unknown_device_instance_id'] > 0)
                    {

                        $mapped_to    = ucwords(strtolower($result [$key] ['udi_device_manufacturer'] . ' ' . $result [$key] ['udi_printer_model'])) . '<span style="color: red;"> (New)</span>';
                        $mapped_to_id = "udi" . $result [$key] ['udi_unknown_device_instance_id'];
                        $is_excluded  = $result [$key] ['udi_is_excluded'];
                        $is_leased    = $result [$key] ['udi_is_leased'];

                        // get average monthly page volume for unknown device
                        $unknown_device_instanceMapper = Proposalgen_Model_Mapper_UnknownDeviceInstance::getInstance();
                        $unknown_device_instance       = $unknown_device_instanceMapper->fetchAllUnknownDevicesAsKnownDevices($report_id, 'id = ' . $result [$key] ['udi_unknown_device_instance_id']);

                        if (count($unknown_device_instance) > 0)
                        {
                            $ampv = number_format($unknown_device_instance [0]->_averageMonthlyPageCount);
                        }
                    }
                    else if ($result [$key] ['di_device_instance_id'] > 0)
                    {
                        $mapped_to    = $result [$key] ['manufacturer_name'] . ' ' . $result [$key] ['printer_model'];
                        $mapped_to_id = $result [$key] ['master_device_id'];
                        $is_excluded  = $result [$key] ['di_is_excluded'];

                        // get average monthly page volume
                        $device_instanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
                        $device_instance       = $device_instanceMapper->fetchRow('device_instance_id = ' . $result [$key] ['di_device_instance_id']);

                        if (count($device_instance) > 0)
                        {
                            $ampv = number_format($device_instance->AverageMonthlyPageCount);
                        }
                    }
                    else
                    {
                        $is_excluded = 0;
                    }
                    $formData->rows [$i] ['id']   = $upload_data_collector_row_id;
                    $formData->rows [$i] ['cell'] = array(
                        $upload_data_collector_row_id,
                        $result [$key] ['devices_pf_id'],
                        ucwords(strtolower($result [$key] ['modelname'])) . "<br />(" . $result [$key] ['ipaddress'] . ")",
                        $mapped_to,
                        $ampv,
                        $is_leased,
                        $is_excluded,
                        $mapped_to_id
                    );
                    $i++;
                }
            }
            else
            {
                $formData = array();
            }
        }
        catch (Exception $e)
        {
            // critical exception
            My_Log::logException($e);
        }

        // encode user data to return to the client:
        $json             = Zend_Json::encode($formData);
        $this->view->data = $json;
    }

    public function deviceleasingexcludedlistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db       = Zend_Db_Table::getDefaultAdapter();
        $formData = new stdClass();
        $page     = $_GET ['page'];
        $limit    = $_GET ['rows'];
        $sidx     = $_GET ['sidx'];
        $sord     = $_GET ['sord'];
        if (!$sidx)
        {
            $sidx = 9;
        }

        $report_id = $this->getReport()->id;

        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array(
                        'udc' => 'pgen_upload_data_collector_rows'
                   ))
            ->where('(invalid_data = 1 OR is_excluded = 1) AND report_id = ' . $report_id);
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();

        $count = count($result);
        if ($count > 0)
        {
            $total_pages = ceil($count / $limit);
        }
        else
        {
            $total_pages = 0;
        }

        if ($page > $total_pages)
        {
            $page = $total_pages;
        }

        $start = $limit * $page - $limit;
        if ($start < 0)
        {
            $start = 0;
        }

        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array(
                        'udc' => 'pgen_upload_data_collector_rows'
                   ))
            ->where('(invalid_data = 1 OR is_excluded = 1) AND report_id = ' . $report_id)
            ->order($sidx . ' ' . $sord)
            ->limit($limit, $start);
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();

        try
        {
            if (count($result) > 0)
            {
                $i                 = 0;
                $formData->page    = $page;
                $formData->total   = $total_pages;
                $formData->records = $count;
                foreach ($result as $key => $value)
                {
                    // device must be at least 4 days old
                    $days          = 5;
                    $startDate     = new DateTime($result [$key] ['startdate']);
                    $endDate       = new DateTime($result [$key] ['enddate']);
                    $discoveryDate = new DateTime($result [$key] ['discovery_date']);

                    $interval1 = $startDate->diff($endDate);
                    $interval2 = $discoveryDate->diff($endDate);

                    $days = $interval1;
                    if ($interval1->days > $interval2->days && !$interval2->invert)
                    {
                        $days = $interval2;
                    }

                    $upload_data_collector_row_id = $result [$key] ['upload_data_collector_row_id'];

                    if ($days->days < 4)
                    {
                        $reason = 'Insufficient Monitor Interval';
                    }
                    else if ($result [$key] ['is_excluded'] == true)
                    {
                        $reason = 'Not Mapped';
                    }
                    else if ($result [$key] ['manufacturer'] == '')
                    {
                        $reason = 'Missing Manufacturer';
                    }
                    else if ($result [$key] ['modelname'] == '')
                    {
                        $reason = 'Missing Model Name';
                    }
                    else
                    {
                        $reason = 'Bad Meter Data';
                    }

                    $formData->rows [$i] ['id']   = $upload_data_collector_row_id;
                    $formData->rows [$i] ['cell'] = array(
                        $upload_data_collector_row_id,
                        $result [$key] ['devices_pf_id'],
                        ucwords(strtolower($result [$key] ['modelname'])) . " (" . $result [$key] ['ipaddress'] . ")",
                        $reason
                    );
                    $i++;
                }
            }
            else
            {
                $formData = array();
            }
        }
        catch (Exception $e)
        {
            // critical exception
        }

        // encode user data to return to the client:
        $json             = Zend_Json::encode($formData);
        $this->view->data = $json;
    }

    public function setleasedAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db            = Zend_Db_Table::getDefaultAdapter();
        $devices_pf_id = $this->_getParam('id', false);
        $value         = $this->_getParam('mode', 0);

        // get report id from session
        $report_id = $this->getReport()->id;

        $updateData = array(
            'is_leased' => $value
        );

        $db->beginTransaction();
        try
        {
            // create table instances
            $upload_data_collectorTable   = new Proposalgen_Model_DbTable_UploadDataCollectorRow();
            $device_instanceTable         = new Proposalgen_Model_DbTable_DeviceInstance();
            $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();

            // get upload_data_collector rows for report and device_pf
            $where                 = $upload_data_collectorTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND devices_pf_id = ?', $devices_pf_id, 'INTEGER');
            $upload_data_collector = $upload_data_collectorTable->fetchAll($where);

            foreach ($upload_data_collector as $key => $value)
            {
                $upload_data_collector_id = $upload_data_collector [$key] ['id'];

                // check if saved as device_instance
                $where           = $device_instanceTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND upload_data_collector_row_id = ?', $upload_data_collector_id, 'INTEGER');
                $device_instance = $device_instanceTable->fetchRow($where);

                if (count($device_instance) > 0)
                {
                    $device_instanceTable->update($updateData, $where);
                }
                else
                {
                    // check if saved as unknown_device_instance
                    $where                   = $unknown_device_instanceTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND upload_data_collector_row_id = ?', $upload_data_collector_id, 'INTEGER');
                    $unknown_device_instance = $unknown_device_instanceTable->fetchRow($where);

                    if (count($unknown_device_instance) > 0)
                    {
                        $unknown_device_instanceTable->update($updateData, $where);
                    }
                    else
                    {
                        // if neither, update upload record
                        $where = $upload_data_collectorTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND id = ?', $upload_data_collector_id, 'INTEGER');
                        $upload_data_collectorTable->update($updateData, $where);
                    }
                }
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
        }
    }

    public function setexcludedAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db                       = Zend_Db_Table::getDefaultAdapter();
        $upload_data_collector_id = $this->_getParam('id', false);
        $value                    = $this->_getParam('mode', 0);

        $updateData = array(
            'is_excluded' => $value
        );

        $db->beginTransaction();
        try
        {
            // check if saved as device_instance
            $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
            $where                = $device_instanceTable->getAdapter()->quoteInto('upload_data_collector_row_id = ?', $upload_data_collector_id, 'INTEGER');
            $device_instance      = $device_instanceTable->fetchRow($where);

            if (count($device_instance) > 0)
            {
                $device_instanceTable->update($updateData, $where);
            }
            else
            {
                // check if saved as unknown_device_instance
                $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                $where                        = $unknown_device_instanceTable->getAdapter()->quoteInto('upload_data_collector_row_id = ?', $upload_data_collector_id, 'INTEGER');
                $unknown_device_instance      = $unknown_device_instanceTable->fetchRow($where);

                if (count($unknown_device_instance) > 0)
                {
                    $unknown_device_instanceTable->update($updateData, $where);
                }
                else
                {
                    // if neither, update upload record
                    $upload_data_collectorTable = new Proposalgen_Model_DbTable_UploadDataCollectorRow();
                    $where                      = $upload_data_collectorTable->getAdapter()->quoteInto('id = ?', $upload_data_collector_id, 'INTEGER');
                    $upload_data_collectorTable->update($updateData, $where);
                }
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollBack();
        }
    }

    /**
     * Allows the user to set the report settings in the override hierarchy
     * BOOKMARK: REPORT SETTINGS
     */
    public function reportsettingsAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_REPORTSETTINGS);

        $reportSettingsService = new Proposalgen_Service_ReportSettings($this->getReport()->id, $this->_userId);

        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ['cancel']))
            {
                $this->gotoPreviousStep();
            }
            else
            {
                if ($reportSettingsService->update($values))
                {
                    $this->_helper->flashMessenger(array(
                                                        'success' => 'Settings saved.'
                                                   ));

                    $this->saveReport();
                    $this->gotoNextStep();
                }
                else
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => 'Please correct the errors below.'
                                                   ));
                }
            }
        }

        $this->view->form = $reportSettingsService->getForm();
    }
}
