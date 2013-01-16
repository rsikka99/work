<?php

/**
 * Data Controller: This controller handles data imports into the system
 *
 * @author Chris Garrah
 */
class Proposalgen_DataController extends Zend_Controller_Action
{
    public $config;
    protected $_redirector = null;

    function init ()
    {
        $this->config = Zend_Registry::get('config');
        $this->initView();
        $this->view->app = $this->config->app;
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
        $this->user_id = Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->dealer_company_id = Zend_Auth::getInstance()->getIdentity()->dealer_company_id;
        $this->privilege = Zend_Auth::getInstance()->getIdentity()->privileges;
        $this->view->privilege = Zend_Auth::getInstance()->getIdentity()->privileges;
        $this->MPSProgramName = $this->config->app->MPSProgramName;
        $this->view->MPSProgramName = $this->config->app->MPSProgramName;
    } // end init

    
    /**
     * * The default action - Upload the fleet data file into the database
     * NOTE: Perhaps change this action to take place under a different
     * action.
     * This action has been created only to test file uploads. It uplods
     * a csv file, reads it, and then deletes it when finished.
     */
    public function indexAction ()
    {
        $date = date('Y-m-d H:i:s T');
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->MPSProgramName = $this->MPSProgramName;
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        $this->view->formTitle = "Upload Data";
        $this->view->companyName = $this->getReportCompanyName();
        
        // make sure report stage has been set
        $reportTable = new Proposalgen_Model_DbTable_Report();
        $reportData = array (
                'report_stage' => 'upload' 
        );
        $where = $reportTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
        $report = $reportTable->fetchRow($where);
        if (count($report) > 0)
        {
            if ($report ['report_stage'] == '')
            {
                $reportTable->update($reportData, $where);
            }
        }
        
        // check for previously uploaded data for report in device instance and
        // unknown device instance tables
        $notes = '';
        $this->view->has_data = false;
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->where('udc.report_id = ' . $report_id);
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        
        if (count($result) > 0)
        {
            $this->view->has_data = true;
            // $notes .= "<p>This report already contains previously uploaded
            // data. Press the &quot;Continue&quot; button to keep this data or
            // if
            // you choose to upload a new file, any previously uploaded data
            // will be
            // deleted and this new data will be used.</p>";
        }
        
        // check for bad data
        $this->view->bad_data = false;
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->where('invalid_data = 1 AND udc.report_id = ' . $report_id);
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        
        if (count($result) > 0)
        {
            $this->view->bad_data = true;
        }
        
        $this->regenerateMenu('verify', 'upload');
        
        if ($this->_request->isPost())
        {
            $upload_limit = 1000;
            $formData = $this->_request->getPost();
            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->setDestination($this->config->app->uploadPath);
            
            $reportTable = new Proposalgen_Model_DbTable_Report();
            $where = $reportTable->getAdapter()->quoteInto('id = ?', $report_id);
            $report = $reportTable->fetchRow($where);
            
            if (count($report) > 0)
            {
                if ($report ['report_stage'] == 'finished')
                {
                    $this->view->report_is_finished = 1;
                }
                else
                {
                    $this->view->report_is_finished = 0;
                }
            }
            
            // Limit the extensions to csv files
            $upload->addValidator('Extension', false, array (
                    'csv' 
            ));
            $upload->getValidator('Extension')->setMessage('<p><span class="warning">*</span> File "' . basename($_FILES ['uploadedfile'] ['name']) . '" has an <em>invalid</em> extension. A <span style="color: red;">.csv</span> is required.</p>');
            
            // Limit the amount of files to maximum 1
            $upload->addValidator('Count', false, 1);
            $upload->getValidator('Count')->setMessage('<p><span class="warning">*</span> You are only allowed to upload 1 file at a time.</p>');
            
            // Limit the size of all files to be uploaded to maximum 4MB and
            // mimimum 500B
            $upload->addValidator('FilesSize', false, array (
                    'min' => '500B', 
                    'max' => '4MB' 
            ));
            $upload->getValidator('FilesSize')->setMessage('<p><span class="warning">*</span> File size must be between 500B and 4MB.</p>');
            
            if ($upload->receive())
            {
                $is_valid = true;
                
                // $db->beginTransaction();
                try
                {
                    $lines = file($upload->getFileName(), FILE_IGNORE_NEW_LINES);
                    
                    // get number of valid rows in file
                    $valid_count = 0;
                    foreach ( $lines as $key => $value )
                    {
                        if ($key > 0)
                        {
                            $devices [$key] = str_getcsv($value);
                            if ($devices [$key] [0] != '' && $devices [$key] [1] != '')
                            {
                                $valid_count ++;
                            }
                        }
                    }
                    if ($valid_count > $upload_limit)
                    {
                        $this->view->message = "<p class='warning'>The uploaded file contains " . count($lines) . " printers. The maximum number of printers supported in a single report is 1000. Please modify your file and try again.</p>";
                        $is_valid = false;
                    }
                    
                    // required fields list
                    $required = array (
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
                    
                    // grab the first row of items(the column headers)
                    $headers = str_getcsv(strtolower($lines [0]));
                    
                    // check headers to make sure required fields exist.
                    foreach ( $required as $key => $value )
                    {
                        if (! in_array(strtolower($required [$key]), $headers))
                        {
                            if (empty($this->view->message))
                            {
                                $this->view->message = "<h3>Upload failed</h3>";
                            }
                            $this->view->message .= "<p><span class=\"warning\">*</span> This file is missing required column: " . $required [$key] . ".</p>";
                            // throw exception
                            $is_valid = false;
                        }
                    }
                    
                    if ($is_valid)
                    {
                        // delete existing data
                        $this->delete_data();
                        
                        // reset report stage flag to upload
                        $reportTable = new Proposalgen_Model_DbTable_Report();
                        $reportData = array (
                                'report_stage' => 'upload' 
                        );
                        $where = $reportTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
                        $reportTable->update($reportData, $where);
                        
                        // create an associative array of the csv infomation
                        $ctr = 0;
                        $upload_data_collectorTable = new Proposalgen_Model_DbTable_UploadDataCollector();
                        foreach ( $lines as $key => $value )
                        {
                            if ($key > 0)
                            {
                                $devices [$key] = str_getcsv($value);
                                
                                if ($devices [$key] [0] != '' && $devices [$key] [1] != '')
                                {
                                    // combine the column headers and the device
                                    // data into one associative array
                                    $finalDevices [] = array_combine($headers, $devices [$key]);
                                    
                                    // get manufacturer
                                    $manufacturername = strtolower($finalDevices [$ctr] ['manufacturer']);
                                    $devicename = strtolower($finalDevices [$ctr] ['modelname']);
                                    $devicename = str_replace($manufacturername . ' ', '', $devicename);
                                    
                                    // NOTE: if Hewlet-packard we also need to
                                    // stip away HP
                                    if ($manufacturername == 'hewlett-packard')
                                    {
                                        $devicename = str_replace('hp ', '', $devicename);
                                    }
                                    $devicename = ucwords(trim($devicename));
                                    
                                    // prep HP manufacturer to Hewlett-Packard
                                    // (do we want to do this???)
                                    if ($manufacturername == "hp")
                                    {
                                        $manufacturername = "hewlett-packard";
                                    }
                                    $manufacturername = ucwords(trim($manufacturername));
                                    
                                    if ($devicename == '' || $manufacturername == '')
                                    {
                                        // skip record
                                        $devices_pf_id = 0;
                                    }
                                    else
                                    {
                                        // check for devices_pf
                                        $devices_pfTable = new Proposalgen_Model_DbTable_PFDevices();
                                        $where = $devices_pfTable->getAdapter()->quoteInto('pf_db_devicename = "' . $devicename . '" OR pf_model_id = ' . $finalDevices [$ctr] ['printermodelid'], null);
                                        $devices_pf = $devices_pfTable->fetchRow($where);
                                        
                                        if (count($devices_pf) > 0)
                                        {
                                            $devices_pf_id = $devices_pf->devices_pf_id;
                                        }
                                        else
                                        {
                                            // add to pf_device_matchup_users
                                            $devices_pfData = array (
                                                    'pf_model_id' => $finalDevices [$ctr] ['printermodelid'], 
                                                    'pf_db_devicename' => $devicename, 
                                                    'pf_db_manufacturer' => $manufacturername, 
                                                    'date_created' => $date, 
                                                    'created_by' => $this->user_id 
                                            );
                                            // print_r($devices_pfData);
                                            $devices_pf_id = $devices_pfTable->insert($devices_pfData);
                                        }
                                    }
                                    
                                    // prep dates
                                    if (! empty($finalDevices [$ctr] ['startdate']))
                                    {
                                        $startdate = new Zend_Date($finalDevices [$ctr] ['startdate'], "mm/dd/yyyy HH:ii:ss");
                                        $startdate = $startdate->toString('yyyy/mm/dd HH:ii:ss');
                                    }
                                    else
                                    {
                                        $startdate = null;
                                    }
                                    if (! empty($finalDevices [$ctr] ['enddate']))
                                    {
                                        $enddate = new Zend_Date($finalDevices [$ctr] ['enddate'], "mm/dd/yyyy HH:ii:ss");
                                        $enddate = $enddate->toString('yyyy/mm/dd HH:ii:ss');
                                    }
                                    else
                                    {
                                        $enddate = null;
                                    }
                                    if (! empty($finalDevices [$ctr] ['dateintroduction']))
                                    {
                                        $date_introduction = new Zend_Date($finalDevices [$ctr] ['dateintroduction'], "mm/dd/yyyy HH:ii:ss");
                                        $date_introduction = $date_introduction->toString('yyyy/mm/dd HH:ii:ss');
                                    }
                                    else
                                    {
                                        $date_introduction = null;
                                    }
                                    if (! empty($finalDevices [$ctr] ['dateadoption']))
                                    {
                                        $date_adoption = new Zend_Date($finalDevices [$ctr] ['dateadoption'], "mm/dd/yyyy HH:ii:ss");
                                        $date_adoption = $date_adoption->toString('yyyy/mm/dd HH:ii:ss');
                                    }
                                    else
                                    {
                                        $date_adoption = null;
                                    }
                                    if (! empty($finalDevices [$ctr] ['discoverydate']))
                                    {
                                        $discovery_date = new Zend_Date($finalDevices [$ctr] ['discoverydate'], "mm/dd/yyyy HH:ii:ss");
                                        $discovery_date = $discovery_date->toString('yyyy/mm/dd HH:ii:ss');
                                    }
                                    else
                                    {
                                        $discovery_date = null;
                                    }
                                    
                                    $upload_data_collectorData = array (
                                            'report_id' => $report_id, 
                                            'devices_pf_id' => $devices_pf_id, 
                                            'startdate' => $startdate, 
                                            'enddate' => $enddate, 
                                            'printermodelid' => $finalDevices [$ctr] ['printermodelid'], 
                                            'ipaddress' => $finalDevices [$ctr] ['ipaddress'], 
                                            'serialnumber' => $finalDevices [$ctr] ['serialnumber'], 
                                            'modelname' => $finalDevices [$ctr] ['modelname'], 
                                            'manufacturer' => $finalDevices [$ctr] ['manufacturer'], 
                                            'is_color' => (strtolower($finalDevices [$ctr] ['is_color']) == "false" ? 0 : 1), 
                                            'is_copier' => (strtolower($finalDevices [$ctr] ['is_copier']) == "false" ? 0 : 1), 
                                            'is_scanner' => (strtolower($finalDevices [$ctr] ['is_scanner']) == "false" ? 0 : 1), 
                                            'is_fax' => (strtolower($finalDevices [$ctr] ['is_fax']) == "false" ? 0 : 1), 
                                            'ppm_black' => ($finalDevices [$ctr] ['ppm_black'] == 0 ? null : $finalDevices [$ctr] ['ppm_black']), 
                                            'ppm_color' => ($finalDevices [$ctr] ['ppm_color'] == 0 ? null : $finalDevices [$ctr] ['ppm_color']), 
                                            'date_introduction' => $date_introduction, 
                                            'date_adoption' => $date_adoption, 
                                            'discovery_date' => $discovery_date, 
                                            'black_prodcodeoem' => ($finalDevices [$ctr] ['black_prodcodeoem'] == "0" ? null : $finalDevices [$ctr] ['black_prodcodeoem']), 
                                            'black_yield' => ($finalDevices [$ctr] ['black_yield'] == 0 ? null : $finalDevices [$ctr] ['black_yield']), 
                                            'black_prodcostoem' => ($finalDevices [$ctr] ['black_prodcostoem'] == 0 ? null : $finalDevices [$ctr] ['black_prodcostoem']), 
                                            'cyan_prodcodeoem' => ($finalDevices [$ctr] ['cyan_prodcodeoem'] == "0" ? null : $finalDevices [$ctr] ['cyan_prodcodeoem']), 
                                            'cyan_yield' => ($finalDevices [$ctr] ['cyan_yield'] == 0 ? null : $finalDevices [$ctr] ['cyan_yield']), 
                                            'cyan_prodcostoem' => ($finalDevices [$ctr] ['cyan_prodcostoem'] == 0 ? null : $finalDevices [$ctr] ['cyan_prodcostoem']), 
                                            'magenta_prodcodeoem' => ($finalDevices [$ctr] ['magenta_prodcodeoem'] == "0" ? null : $finalDevices [$ctr] ['magenta_prodcodeoem']), 
                                            'magenta_yield' => ($finalDevices [$ctr] ['magenta_yield'] == 0 ? null : $finalDevices [$ctr] ['magenta_yield']), 
                                            'magenta_prodcostoem' => ($finalDevices [$ctr] ['magenta_prodcostoem'] == 0 ? null : $finalDevices [$ctr] ['magenta_prodcostoem']), 
                                            'yellow_prodcodeoem' => ($finalDevices [$ctr] ['yellow_prodcodeoem'] == "0" ? null : $finalDevices [$ctr] ['yellow_prodcodeoem']), 
                                            'yellow_yield' => ($finalDevices [$ctr] ['yellow_yield'] == 0 ? null : $finalDevices [$ctr] ['yellow_yield']), 
                                            'yellow_prodcostoem' => ($finalDevices [$ctr] ['yellow_prodcostoem'] == 0 ? null : $finalDevices [$ctr] ['yellow_prodcostoem']), 
                                            'duty_cycle' => ($finalDevices [$ctr] ['duty_cycle'] == 0 ? null : $finalDevices [$ctr] ['duty_cycle']), 
                                            'wattspowernormal' => ($finalDevices [$ctr] ['wattspowernormal'] == 0 ? null : $finalDevices [$ctr] ['wattspowernormal']), 
                                            'wattspoweridle' => ($finalDevices [$ctr] ['wattspoweridle'] == 0 ? null : $finalDevices [$ctr] ['wattspoweridle']), 
                                            'startmeterlife' => ($finalDevices [$ctr] ['startmeterlife'] == 0 ? null : $finalDevices [$ctr] ['startmeterlife']), 
                                            'endmeterlife' => ($finalDevices [$ctr] ['endmeterlife'] == 0 ? null : $finalDevices [$ctr] ['endmeterlife']), 
                                            'startmeterblack' => ($finalDevices [$ctr] ['startmeterblack'] == 0 ? null : $finalDevices [$ctr] ['startmeterblack']), 
                                            'endmeterblack' => ($finalDevices [$ctr] ['endmeterblack'] == 0 ? null : $finalDevices [$ctr] ['endmeterblack']), 
                                            'startmetercolor' => ($finalDevices [$ctr] ['startmetercolor'] == 0 ? null : $finalDevices [$ctr] ['startmetercolor']), 
                                            'endmetercolor' => ($finalDevices [$ctr] ['endmetercolor'] == 0 ? null : $finalDevices [$ctr] ['endmetercolor']), 
                                            'startmeterprintblack' => ($finalDevices [$ctr] ['startmeterprintblack'] == 0 ? null : $finalDevices [$ctr] ['startmeterprintblack']), 
                                            'endmeterprintblack' => ($finalDevices [$ctr] ['endmeterprintblack'] == 0 ? null : $finalDevices [$ctr] ['endmeterprintblack']), 
                                            'startmeterprintcolor' => ($finalDevices [$ctr] ['startmeterprintcolor'] == 0 ? null : $finalDevices [$ctr] ['startmeterprintcolor']), 
                                            'endmeterprintcolor' => ($finalDevices [$ctr] ['endmeterprintcolor'] == 0 ? null : $finalDevices [$ctr] ['endmeterprintcolor']), 
                                            'startmetercopyblack' => ($finalDevices [$ctr] ['startmetercopyblack'] == 0 ? null : $finalDevices [$ctr] ['startmetercopyblack']), 
                                            'endmetercopyblack' => ($finalDevices [$ctr] ['endmetercopyblack'] == 0 ? null : $finalDevices [$ctr] ['endmetercopyblack']), 
                                            'startmetercopycolor' => ($finalDevices [$ctr] ['startmetercopycolor'] == 0 ? null : $finalDevices [$ctr] ['startmetercopycolor']), 
                                            'endmetercopycolor' => ($finalDevices [$ctr] ['endmetercopycolor'] == 0 ? null : $finalDevices [$ctr] ['endmetercopycolor']), 
                                            'startmeterscan' => ($finalDevices [$ctr] ['startmeterscan'] == 0 ? null : $finalDevices [$ctr] ['startmeterscan']), 
                                            'endmeterscan' => ($finalDevices [$ctr] ['endmeterscan'] == 0 ? null : $finalDevices [$ctr] ['endmeterscan']), 
                                            'startmeterfax' => ($finalDevices [$ctr] ['startmeterfax'] == 0 ? null : $finalDevices [$ctr] ['startmeterfax']), 
                                            'endmeterfax' => ($finalDevices [$ctr] ['endmeterfax'] == 0 ? null : $finalDevices [$ctr] ['endmeterfax']), 
                                            'tonerlevel_black' => $finalDevices [$ctr] ['tonerlevel_black'], 
                                            'tonerlevel_cyan' => $finalDevices [$ctr] ['tonerlevel_cyan'], 
                                            'tonerlevel_magenta' => $finalDevices [$ctr] ['tonerlevel_magenta'], 
                                            'tonerlevel_yellow' => $finalDevices [$ctr] ['tonerlevel_yellow'] 
                                    );
                                    
                                    // devicename and manufacturer are required,
                                    // endmeterblack cannot be null/0, all
                                    // meters must have an endmeter that is
                                    // larger than the start meter
                                    // also exclude if startdate is greater than
                                    // 5 years from today
                                    $days = 5;
                                    $date = date("m") . "/" . date("d") . "/" . (date("Y") - 5);
                                    
                                    // device must be at least 4 days old
                                    $startDate = new DateTime($finalDevices [$ctr] ['startdate']);
                                    $endDate = new DateTime($finalDevices [$ctr] ['enddate']);
                                    $discoveryDate = new DateTime($finalDevices [$ctr] ['discoverydate']);
                                    
                                    $interval1 = $startDate->diff($endDate);
                                    $interval2 = $discoveryDate->diff($endDate);
                                    
                                    $days = $interval1;
                                    if ($interval1->days > $interval2->days && ! $interval2->invert)
                                    {
                                        $days = $interval2;
                                    }
                                    
                                    // exclude invalid rows
                                    if ((empty($finalDevices [$ctr] ['modelname']) || empty($finalDevices [$ctr] ['manufacturer'])) || $this->checkMeters($ctr, $finalDevices) == false || strtotime($finalDevices [$ctr] ['startdate']) < strtotime($date) || $days->days < 4)
                                    {
                                        $this->view->bad_data = true;
                                        $upload_data_collectorData ['invalid_data'] = 1;
                                    }
                                    else
                                    {
                                        $upload_data_collectorData ['invalid_data'] = 0;
                                    }
                                    
                                    // always default to excluded to false
                                    $upload_data_collectorData ['is_excluded'] = 0;
                                    
                                    // creating/updating the 2D data array with
                                    // values
                                    $upload_data_collectorDataArray [] = $upload_data_collectorData;
                                    $ctr ++;
                                }
                            }
                        }
                        
                        // calling the function that actually adds the rows
                        $msg = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->saveRows($upload_data_collectorDataArray);
                        if ($msg)
                        {
                            $this->view->message = "Your file was not saved. Please double check the file and try again. If you continue to experience problems saving, contact your administrator.<br /><br />";
                        }
                        
                        // add to array after here
                        $this->view->has_data = true;
                        $this->view->message = "<p>The upload is complete. You can review your results below.</p>";
                        
                        if ($this->view->bad_data == true)
                        {
                            $notes .= "<p>Rows missing required data are automatically excluded and are displayed in the \"Results to be Excluded\" table below.</p>";
                        }
                    }
                    $db->commit();
                }
                catch ( Exception $e )
                {
                    // $db->rollBack();
                    echo $e . "<br /><br />";
                    $this->view->message = "Your file was not saved. Please double check the file and try again. If you continue to experience problems saving, contact your administrator.<br /><br />";
                }
                
                unlink($upload->getFileName());
            }
            else
            {
                // if upload fails, print error message message
                $this->view->errMessages = $upload->getMessages();
            }
        }
        
        if (! empty($notes) && ! isset($finalDevices))
        {
            $this->view->message .= $notes;
        }
        
        return;
    }

    /**
     *
     * @param $is_color int
     *            A switch indicating color ( 1 ) or b/w ( 0 ).
     * @param $tonerLevels array
     *            - An associative array of device toner levels
     *            
     * @return boolean JIT Support
     *        
     * @author Kevin Jervis
     */
    public function determineJITSupport ($is_color, $tonerLevels)
    {
        $JITCompatible = false;
        $tonerLevelBlack = strtoupper($tonerLevels ['toner_level_black']);
        
        // If device is b/w, ensure it has a % for black toner.
        if ($is_color == 0)
        {
            if (strpos($tonerLevelBlack, '%'))
                $JITCompatible = true;
        }
        else
        {
            // Convert toner values to uppercase for comparison
            $tonerLevelCyan = strtoupper($tonerLevels ['toner_level_cyan']);
            $tonerLevelMagenta = strtoupper($tonerLevels ['toner_level_magenta']);
            $tonerLevelYellow = strtoupper($tonerLevels ['toner_level_yellow']);
            
            // If any toner reports a percentage, other toner levels must have
            // %, OK or LOW as value
            if (strpos($tonerLevelBlack, '%') || strpos($tonerLevelCyan, '%') || strpos($tonerLevelMagenta, '%') || strpos($tonerLevelYellow, '%'))
                if ($tonerLevelBlack == 'LOW' || $tonerLevelBlack == 'OK' || strpos($tonerLevelBlack, '%'))
                    if ($tonerLevelCyan == 'LOW' || $tonerLevelCyan == 'OK' || strpos($tonerLevelCyan, '%'))
                        if ($tonerLevelMagenta == 'LOW' || $tonerLevelMagenta == 'OK' || strpos($tonerLevelMagenta, '%'))
                            if ($tonerLevelYellow == 'LOW' || $tonerLevelYellow == 'OK' || strpos($tonerLevelYellow, '%'))
                            {
                                $JITCompatible = true;
                            }
        } // end else
        return $JITCompatible;
    } // end function determineJITSupport

    public function previewlistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $invalid_data = $this->_getParam('filter', 0);
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
            $sidx = 9;
            
            // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        try
        {
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'udc' => 'proposalgenerator_upload_data_collector_rows' 
            ))
                ->where('invalid_data = ' . $invalid_data . ' AND report_id = ' . $report_id);
            $stmt = $db->query($select);
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
                $page = $total_pages;
            $start = $limit * $page - $limit;
            if ($start < 0)
                $start = 0;
            
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'udc' => 'proposalgenerator_upload_data_collector_rows' 
            ))
                ->where('invalid_data = ' . $invalid_data . ' AND report_id = ' . $report_id)
                ->order($sidx . ' ' . $sord)
                ->limit($limit, $start);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            if (count($result) > 0)
            {
                $i = 0;
                $formdata->page = $page;
                $formdata->total = $total_pages;
                $formdata->records = $count;
                foreach ( $result as $key => $value )
                {
                    $formdata->rows [$i] ['id'] = $result [$key] ['upload_data_collector_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $result [$key] ['upload_data_collector_id'], 
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
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function confirmationAction ()
    {
        $this->view->formTitle = 'Upload Confirmation';
        $this->view->companyName = $this->getReportCompanyName();
        
        // loop through $finalDevices and seperate into proper locations
        if ($this->_request->isPost())
        {
            // get report id from session
            $session = new Zend_Session_Namespace('report');
            $report_id = $session->report_id;
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try
            {
                // reset report stage flag
                $reportTable = new Proposalgen_Model_DbTable_Report();
                $reportData = array (
                        'report_stage' => 'mapping' 
                );
                $where = $reportTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
                $report = $reportTable->fetchRow($where);
                if ($report ['report_stage'] != 'finished')
                {
                    $reportTable->update($reportData, $where);
                }
                
                $db->commit();
                
                // redirect to mapping page
                $this->_redirect('/data/devicemapping');
            }
            catch ( Zend_Db_Exception $e )
            {
                $this->view->message = "Unknown Error.";
                throw new Exception("Unknown Database Error.", 0, $e);
            }
            catch ( Exception $e )
            {
                $db->rollBack();
                $this->view->message = "There was an error updating the report stage. Please contact your administrator.";
            }
        }
    }

    public function devicemappingAction ()
    {
        Tangent_Timer::Milestone("Device Mapping Action Start");
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->grid = $this->_getParam('grid', 'none');
        $this->view->formTitle = 'Printer Mapping';
        $this->view->companyName = $this->getReportCompanyName();
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        // get unmapped counts
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->joinLeft(array (
                'mmpf' => 'master_matchup_pf' 
        ), 'udc.devices_pf_id = mmpf.devices_pf_id', array (
                'mmpf.master_device_id' 
        ))
            ->joinLeft(array (
                'pfdmu' => 'pf_device_matchup_users' 
        ), 'udc.devices_pf_id = pfdmu.devices_pf_id AND pfdmu.user_id = ' . $this->user_id, array (
                'pfdmu.master_device_id' 
        ))
            ->joinLeft(array (
                'md' => 'master_device' 
        ), 'md.master_device_id = pfdmu.master_device_id', array (
                'printer_model' 
        ))
            ->joinLeft(array (
                'm' => 'manufacturer' 
        ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                'manufacturer_name' 
        ))
            ->where('udc.report_id = ?', $report_id, 'INTEGER')
            ->where('udc.invalid_data = 0')
            ->where('mmpf.master_device_id IS NULL')
            ->where('pfdmu.master_device_id > 0 || pfdmu.master_device_id IS NULL')
            ->group('udc.devices_pf_id')
            ->order('udc.modelname');
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
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
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->joinLeft(array (
                'pfdmu' => 'pf_device_matchup_users' 
        ), 'udc.devices_pf_id = pfdmu.devices_pf_id AND pfdmu.user_id = ' . $this->user_id, array (
                'pfdmu.master_device_id' 
        ))
            ->joinLeft(array (
                'mmpf' => 'master_matchup_pf' 
        ), 'udc.devices_pf_id = mmpf.devices_pf_id', array (
                'mmpf.master_device_id' 
        ))
            ->joinLeft(array (
                'md' => 'master_device' 
        ), 'md.master_device_id = mmpf.master_device_id', array (
                'printer_model' 
        ))
            ->joinLeft(array (
                'm' => 'manufacturer' 
        ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                'manufacturer_name' 
        ))
            ->where('udc.report_id = ?', $report_id, 'INTEGER')
            ->where('udc.invalid_data = 0')
            ->where('pfdmu.master_device_id IS NULL')
            ->where('mmpf.master_device_id > 0')
            ->group('udc.devices_pf_id')
            ->order('udc.modelname');
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        $this->view->mapped_count = count($result);
        
        // check for previously uploaded data for report in device instance and
        // unknown device instance tables
        $notes = '';
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->where('udc.report_id = ' . $report_id);
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        
        $this->regenerateMenu('upload', 'mapping');
        
        Tangent_Timer::Milestone("Device Mapping Action End");
    }

    public function devicemappinglistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ), array (
                'upload_data_collector_id', 
                'report_id', 
                'devices_pf_id', 
                'printermodelid', 
                'modelname', 
                'manufacturer', 
                '(SELECT COUNT(*) AS count FROM upload_data_collector AS sudc WHERE sudc.report_id=udc.report_id AND sudc.devices_pf_id=udc.devices_pf_id AND sudc.invalid_data = 0) AS group_count' 
        ))
            ->joinLeft(array (
                'mmpf' => 'master_matchup_pf' 
        ), 'udc.devices_pf_id = mmpf.devices_pf_id', array (
                'mmpf.master_device_id' 
        ))
            ->joinLeft(array (
                'pfdmu' => 'pf_device_matchup_users' 
        ), 'udc.devices_pf_id = pfdmu.devices_pf_id AND pfdmu.user_id = ' . $this->user_id, array (
                'pfdmu.master_device_id' 
        ))
            ->joinLeft(array (
                'md' => 'master_device' 
        ), 'md.master_device_id = pfdmu.master_device_id', array (
                'printer_model', 
                'is_leased' 
        ))
            ->joinLeft(array (
                'm' => 'manufacturer' 
        ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                'manufacturer_name' 
        ))
            ->where('udc.report_id = ' . $report_id . ' AND udc.invalid_data = 0 AND mmpf.master_device_id IS NULL AND (pfdmu.master_device_id > 0 || pfdmu.master_device_id IS NULL)')
            ->group('udc.devices_pf_id')
            ->order('udc.modelname');
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        
        try
        {
            if (count($result) > 0)
            {
                $i = 0;
                foreach ( $result as $key => $value )
                {
                    $is_added = '';
                    $mapped_to_id = '';
                    $mapped_to_modelname = '';
                    $mapped_to_manufacturer = '';
                    
                    $count = $result [$key] ['group_count'];
                    
                    // set up mapped to suggestions
                    $is_leased = $result [$key] ['is_leased'];
                    $devices_pf_id = $result [$key] ['devices_pf_id'];
                    $upload_data_collector_id = $result [$key] ['upload_data_collector_id'];
                    
                    $mapped_to_id = $result [$key] ['master_device_id'];
                    $mapped_to_modelname = $result [$key] ['printer_model'];
                    $mapped_to_manufacturer = $result [$key] ['manufacturer_name'];
                    
                    // check to see if device has been added
                    $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                    $where = $unknown_device_instanceTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                    $unknown_device_instance = $unknown_device_instanceTable->fetchRow($where);
                    
                    if (count($unknown_device_instance) > 0)
                    {
                        $is_added = $key;
                    }
                    else
                    {
                        $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
                        $where = $device_instanceTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                        $device_instance = $device_instanceTable->fetchRow($where);
                        
                        if (count($device_instance) > 0)
                        {
                            $is_added = '';
                        }
                    }
                    
                    $formdata->rows [$i] ['id'] = $upload_data_collector_id;
                    $formdata->rows [$i] ['cell'] = array (
                            $upload_data_collector_id, 
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
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function mastermappinglistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ), array (
                'upload_data_collector_id', 
                'report_id', 
                'devices_pf_id', 
                'printermodelid', 
                'modelname', 
                'manufacturer', 
                'is_excluded', 
                '(SELECT COUNT(*) AS count FROM upload_data_collector AS sudc WHERE sudc.report_id=udc.report_id AND sudc.devices_pf_id=udc.devices_pf_id) AS group_count' 
        ))
            ->joinLeft(array (
                'pfdmu' => 'pf_device_matchup_users' 
        ), 'udc.devices_pf_id = pfdmu.devices_pf_id AND pfdmu.user_id = ' . $this->user_id, array (
                'master_device_id AS user_matchup_id' 
        ))
            ->joinLeft(array (
                'umd' => 'master_device' 
        ), 'umd.master_device_id = pfdmu.master_device_id', array (
                'printer_model AS user_printer_model' 
        ))
            ->joinLeft(array (
                'um' => 'manufacturer' 
        ), 'um.manufacturer_id = umd.mastdevice_manufacturer', array (
                'manufacturer_name AS user_manufacturer_name' 
        ))
            ->joinLeft(array (
                'mmpf' => 'master_matchup_pf' 
        ), 'udc.devices_pf_id = mmpf.devices_pf_id', array (
                'master_device_id AS master_matchup_id' 
        ))
            ->joinLeft(array (
                'mmd' => 'master_device' 
        ), 'mmd.master_device_id = mmpf.master_device_id', array (
                'printer_model AS master_printer_model', 
                'is_leased' 
        ))
            ->joinLeft(array (
                'mm' => 'manufacturer' 
        ), 'mm.manufacturer_id = mmd.mastdevice_manufacturer', array (
                'manufacturer_name AS master_manufacturer_name' 
        ))
            ->where('udc.report_id = ?', $report_id, 'INTEGER')
            ->where('mmpf.master_device_id > 0')
            ->group('udc.devices_pf_id')
            ->order('udc.modelname');
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        
        try
        {
            if (count($result) > 0)
            {
                $i = 0;
                foreach ( $result as $key => $value )
                {
                    $is_added = '';
                    $mapped_to_id = '';
                    $mapped_to_modelname = '';
                    $mapped_to_manufacturer = '';
                    
                    $count = $result [$key] ['group_count'];
                    
                    // set up leased, mapped to suggestions
                    $is_leased = $result [$key] ['is_leased'];
                    $devices_pf_id = $result [$key] ['devices_pf_id'];
                    $upload_data_collector_id = $result [$key] ['upload_data_collector_id'];
                    
                    if ($result [$key] ['is_excluded'] == 1)
                    {
                        $mapped_to_id = '';
                        $mapped_to_modelname = '';
                        $mapped_to_manufacturer = '';
                    }
                    else if ($result [$key] ['user_matchup_id'] > 0)
                    {
                        $mapped_to_id = $result [$key] ['user_matchup_id'];
                        $mapped_to_modelname = $result [$key] ['user_printer_model'];
                        $mapped_to_manufacturer = $result [$key] ['user_manufacturer_name'];
                    }
                    else
                    {
                        $mapped_to_id = $result [$key] ['master_matchup_id'];
                        $mapped_to_modelname = $result [$key] ['master_printer_model'];
                        $mapped_to_manufacturer = $result [$key] ['master_manufacturer_name'];
                    }
                    
                    // check to see if device has been added
                    $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                    $where = $unknown_device_instanceTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                    $unknown_device_instance = $unknown_device_instanceTable->fetchRow($where);
                    
                    if (count($unknown_device_instance) > 0)
                    {
                        $is_added = $key;
                    }
                    else
                    {
                        $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
                        $where = $device_instanceTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                        $device_instance = $device_instanceTable->fetchRow($where);
                        
                        if (count($device_instance) > 0)
                        {
                            $is_added = '';
                        }
                    }
                    
                    $formdata->rows [$i] ['id'] = $upload_data_collector_id;
                    $formdata->rows [$i] ['cell'] = array (
                            $upload_data_collector_id, 
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
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function setmappedtoAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // get params
        $devices_pf_id = $this->_getParam('devices_pf_id', 0);
        $master_device_id = $this->_getParam('master_device_id', 0);
        
        $db->beginTransaction();
        try
        {
            // add pf_device_matchup_users record
            $pf_device_matchup_usersTable = new Proposalgen_Model_DbTable_PFMatchupUsers();
            if ($devices_pf_id > 0)
            {
                $where = $pf_device_matchup_usersTable->getAdapter()->quoteInto('devices_pf_id = ' . $devices_pf_id . ' AND user_id = ' . $this->user_id, null);
                $result = $pf_device_matchup_usersTable->fetchRow($where);
                
                $pf_device_matchup_usersData = array (
                        'master_device_id' => $master_device_id 
                );
                
                if (count($result) > 0)
                {
                    if ($master_device_id > 0)
                    {
                        $pf_device_matchup_usersTable->update($pf_device_matchup_usersData, $where);
                    }
                    else
                    {
                        $pf_device_matchup_usersTable->delete($where);
                    }
                }
                else
                {
                    $pf_device_matchup_usersData ['devices_pf_id'] = $devices_pf_id;
                    $pf_device_matchup_usersData ['user_id'] = $this->user_id;
                    $pf_device_matchup_usersTable->insert($pf_device_matchup_usersData);
                }
            }
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
            throw new Exception("An Error occured mapping the device ' . $master_device_id . '.", 0, $e);
        }
    }

    public function setleasedAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $devices_pf_id = $this->_getParam('id', false);
        $value = $this->_getParam('mode', 0);
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        $updateData = array (
                'is_leased' => $value 
        );
        
        $db->beginTransaction();
        try
        {
            // create table instances
            $upload_data_collectorTable = new Proposalgen_Model_DbTable_UploadDataCollector();
            $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
            $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
            
            // get upload_data_collector rows for report and device_pf
            $where = $upload_data_collectorTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND devices_pf_id = ?', $devices_pf_id, 'INTEGER');
            $upload_data_collector = $upload_data_collectorTable->fetchAll($where);
            
            foreach ( $upload_data_collector as $key => $value )
            {
                $upload_data_collector_id = $upload_data_collector [$key] ['upload_data_collector_id'];
                
                // check if saved as device_instance
                $where = $device_instanceTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                $device_instance = $device_instanceTable->fetchRow($where);
                
                if (count($device_instance) > 0)
                {
                    $device_instanceTable->update($updateData, $where);
                }
                else
                {
                    // check if saved as unknown_device_instance
                    $where = $unknown_device_instanceTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                    $unknown_device_instance = $unknown_device_instanceTable->fetchRow($where);
                    
                    if (count($unknown_device_instance) > 0)
                    {
                        $unknown_device_instanceTable->update($updateData, $where);
                    }
                    else
                    {
                        // if neither, update upload record
                        $where = $upload_data_collectorTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                        $upload_data_collectorTable->update($updateData, $where);
                    }
                }
            }
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollBack();
        }
    }

    public function setexcludedAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $upload_data_collector_id = $this->_getParam('id', false);
        $value = $this->_getParam('mode', 0);
        
        $updateData = array (
                'is_excluded' => $value 
        );
        
        $db->beginTransaction();
        try
        {
            // check if saved as device_instance
            $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
            $where = $device_instanceTable->getAdapter()->quoteInto('upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
            $device_instance = $device_instanceTable->fetchRow($where);
            
            if (count($device_instance) > 0)
            {
                $device_instanceTable->update($updateData, $where);
            }
            else
            {
                // check if saved as unknown_device_instance
                $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                $where = $unknown_device_instanceTable->getAdapter()->quoteInto('upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                $unknown_device_instance = $unknown_device_instanceTable->fetchRow($where);
                
                if (count($unknown_device_instance) > 0)
                {
                    $unknown_device_instanceTable->update($updateData, $where);
                }
                else
                {
                    // if neither, update upload record
                    $upload_data_collectorTable = new Proposalgen_Model_DbTable_UploadDataCollector();
                    $where = $upload_data_collectorTable->getAdapter()->quoteInto('upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                    $upload_data_collectorTable->update($updateData, $where);
                }
            }
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollBack();
        }
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
                $upload_data_collectorTable = new Proposalgen_Model_DbTable_UploadDataCollector();
                $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                
                // get all uploaded rows for device
                $where = $upload_data_collectorTable->getAdapter()->quoteInto('devices_pf_id = ?', $devices_pf_id, 'INTEGER');
                $upload_data_collector = $upload_data_collectorTable->fetchAll($where);
                
                foreach ( $upload_data_collector as $key => $value )
                {
                    $upload_data_collector_id = $upload_data_collector [$key] ['upload_data_collector_id'];
                    $where = $unknown_device_instanceTable->getAdapter()->quoteInto('upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                    $unknown_device_instances = $unknown_device_instanceTable->fetchRow($where);
                    
                    if (count($unknown_device_instances) > 0)
                    {
                        $unknown_device_instances_id = $unknown_device_instances ['unknown_device_instance_id'];
                        $unknown_device_instanceTable->delete($where);
                    }
                }
            }
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
            echo $e . "<br />";
            $this->view->message = "Error removing device.";
        }
    }

    public function savemappingAction ()
    {
        Tangent_Timer::Milestone("Start Save Mapping");
        $this->view->formTitle = 'Upload Confirmation';
        $this->view->companyName = $this->getReportCompanyName();
        $date = date('Y-m-d H:i:s T');
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        if ($this->_request->isPost())
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            
            $db->beginTransaction();
            try
            {
                $deviceArray = array ();
                $udcUpdateArray = array ();
                $metersDataArray = array ();
                
                $formData = $this->_request->getPost();
                
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        'udc' => 'proposalgenerator_upload_data_collector_rows' 
                ))
                    ->joinLeft(array (
                        'di' => 'device_instance' 
                ), 'di.upload_data_collector_id = udc.upload_data_collector_id', array (
                        'device_instance_id' 
                ))
                    ->joinLeft(array (
                        'udi' => 'unknown_device_instance' 
                ), 'udi.upload_data_collector_id = udc.upload_data_collector_id', array (
                        'unknown_device_instance_id' 
                ))
                    ->joinLeft(array (
                        'mmpf' => 'master_matchup_pf' 
                ), 'udc.devices_pf_id = mmpf.devices_pf_id', array (
                        'master_device_id AS master_matchup_id' 
                ))
                    ->joinLeft(array (
                        'pfdmu' => 'pf_device_matchup_users' 
                ), 'udc.devices_pf_id = pfdmu.devices_pf_id AND pfdmu.user_id = ' . $this->user_id, array (
                        'master_device_id AS user_matchup_id' 
                ))
                    ->joinLeft(array (
                        'md' => 'master_device' 
                ), 'md.master_device_id = pfdmu.master_device_id', array (
                        'printer_model' 
                ))
                    ->joinLeft(array (
                        'm' => 'manufacturer' 
                ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                        'manufacturer_name' 
                ))
                    ->where('udc.report_id = ?', $report_id, 'INTEGER')
                    ->where('udc.invalid_data = 0')
                    ->where('unknown_device_instance_id IS NULL');
                $stmt = $db->query($select);
                $result = $stmt->fetchAll();
                
                // *************************************************************
                // save device instances
                // *************************************************************
                $metersDataArray = array ();
                
                foreach ( $result as $key => $value )
                {
                    $is_leased = 0;
                    $master_device_id = 0;
                    
                    // get devices_pf_id
                    $devices_pf_id = $result [$key] ['devices_pf_id'];
                    $upload_data_collector_id = $result [$key] ['upload_data_collector_id'];
                    
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
                        $is_color = $result [$key] ['is_color'];
                        $tonerLevels = array ();
                        if ($is_color == 0)
                        {
                            $tonerLevels = array (
                                    'toner_level_black' => $result [$key] ['tonerlevel_black'] 
                            );
                        }
                        else
                        {
                            $tonerLevels = array (
                                    'toner_level_black' => $result [$key] ['tonerlevel_black'], 
                                    'toner_level_cyan' => $result [$key] ['tonerlevel_cyan'], 
                                    'toner_level_magenta' => $result [$key] ['tonerlevel_magenta'], 
                                    'toner_level_yellow' => $result [$key] ['tonerlevel_yellow'] 
                            );
                        }
                        $jit_supplies_supported = $this->determineJITSupport($is_color, $tonerLevels);
                        
                        // save to device instance
                        $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
                        $devices_instanceData = array (
                                'device_instance_id' => $result [$key] ['device_instance_id'], 
                                'report_id' => $report_id, 
                                'master_device_id' => $master_device_id, 
                                'upload_data_collector_id' => $upload_data_collector_id, 
                                'serial_number' => $result [$key] ['serialnumber'], 
                                'mps_monitor_startdate' => $result [$key] ['startdate'], 
                                'mps_monitor_enddate' => $result [$key] ['enddate'], 
                                'mps_discovery_date' => $result [$key] ['discovery_date'], 
                                'jit_supplies_supported' => ($jit_supplies_supported == true ? 1 : 0), 
                                'ip_address' => $result [$key] ['ipaddress'] 
                        );
                        $deviceArray [] = $devices_instanceData;
                        
                        // update uploaded record as not excluded
                        $upload_data_collectorData = array (
                                'upload_data_collector_id' => $upload_data_collector_id, 
                                'is_excluded' => 0 
                        );
                        $udcUpdateArray [] = $upload_data_collectorData;
                    }
                    else
                    {
                        // update uploaded record as excluded
                        $upload_data_collectorData = array (
                                'upload_data_collector_id' => $upload_data_collector_id, 
                                'is_excluded' => 1 
                        );
                        $udcUpdateArray [] = $upload_data_collectorData;
                    }
                }
                $devicesMsg = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->saveRows($deviceArray);
                $ucdMsg = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->saveRows($udcUpdateArray);
                
                // *************************************************************
                // save meters
                // *************************************************************
                // get device instance records for report
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        'udc' => 'proposalgenerator_upload_data_collector_rows' 
                ))
                    ->joinLeft(array (
                        'di' => 'device_instance' 
                ), 'di.upload_data_collector_id = udc.upload_data_collector_id', array (
                        'device_instance_id' 
                ))
                    ->where('udc.report_id = ?', $report_id, 'INTEGER')
                    ->where('udc.invalid_data = 0')
                    ->where('device_instance_id > 0');
                $stmt = $db->query($select);
                $result = $stmt->fetchAll();
                
                $columns = array (
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
                
                $metersTable = new Proposalgen_Model_DbTable_Meters();
                foreach ( $result as $key => $value )
                {
                    $device_instance_id = $result [$key] ['device_instance_id'];
                    
                    // insert meter
                    foreach ( $columns as $key2 )
                    {
                        $meter_type = $key2;
                        $start_meter = $result [$key] ["startmeter" . $meter_type];
                        $end_meter = $result [$key] ["endmeter" . $meter_type];
                        
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
                            $where = $metersTable->getAdapter()->quoteInto('meter_type = "' . $meter_type . '" AND device_instance_id = ?', $device_instance_id, 'INTEGER');
                            $meters = $metersTable->fetchRow($where);
                            
                            $meter_id = null;
                            if (count($meters) > 0)
                            {
                                $meter_id = $meters ['meter_id'];
                            }
                            
                            $metersData = array (
                                    'meter_id' => $meter_id, 
                                    'device_instance_id' => $device_instance_id, 
                                    'meter_type' => $meter_type, 
                                    'start_meter' => $start_meter, 
                                    'end_meter' => $end_meter 
                            );
                            $metersDataArray [] = $metersData;
                        }
                    }
                }
                $metersMsg = Proposalgen_Model_Mapper_Meter::getInstance()->saveRows($metersDataArray);
                
                // reset report stage flag
                $reportTable = new Proposalgen_Model_DbTable_Report();
                $reportData = array (
                        'report_stage' => 'leasing' 
                );
                $where = $reportTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
                $report = $reportTable->fetchRow($where);
                if ($report ['report_stage'] != 'finished')
                {
                    $reportTable->update($reportData, $where);
                }
                
                $db->commit();
                
                // redirect back to mapping page
                $this->_redirect('data/deviceleasing');
            }
            catch ( Exception $e )
            {
                $db->rollback();
                throw new Exception("An error occurred saving mapping.", 0, $e);
            }
        }
    }

    public function deviceleasingAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $this->view->formTitle = 'Upload Summary';
        $this->view->companyName = $this->getReportCompanyName();
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        $upload_data_collectorTable = new Proposalgen_Model_DbTable_UploadDataCollector();
        $where = $upload_data_collectorTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
        $result = $upload_data_collectorTable->fetchAll($where);
        $this->view->mappingArray = $result;
        
        // check for previously uploaded data for report in device instance and
        // unknown device instance tables
        $notes = '';
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->where('udc.report_id = ' . $report_id);
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        
        // get upload count
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->where('id = ' . $report_id);
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        $this->view->upload_count = count($result);
        
        // get exclude count
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->where('(invalid_data = 1 OR is_excluded = 1) AND report_id = ' . $report_id);
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        $this->view->exclude_count = count($result);
        
        $this->view->mapped_count = $this->view->upload_count - $this->view->exclude_count;
        
        // return instructional message
        $this->view->message = "<p>" . $this->view->mapped_count . " of " . $this->view->upload_count . " uploaded printers are mapped and available to include in your report. " . $this->view->exclude_count . " printer(s) have been excluded due to insufficient data.<p>";
        
        $this->regenerateMenu('mapping', 'leasing');
        
        if ($this->_request->isPost())
        {
            // make sure all devices aren't excluded
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'udc' => 'proposalgenerator_upload_data_collector_rows' 
            ))
                ->joinLeft(array (
                    'di' => 'device_instance' 
            ), 'di.upload_data_collector_id = udc.upload_data_collector_id', array (
                    'device_instance_id' 
            ))
                ->joinLeft(array (
                    'udi' => 'unknown_device_instance' 
            ), 'udi.upload_data_collector_id = udc.upload_data_collector_id', array (
                    'unknown_device_instance_id' 
            ))
                ->where('udc.invalid_data = 0 AND udc.report_id = ' . $report_id)
                ->where('di.is_excluded = 0 || udi.is_excluded = 0');
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            if (count($result) > 0)
            {
                // reset report stage flag
                $reportTable = new Proposalgen_Model_DbTable_Report();
                $reportData = array (
                        'report_stage' => 'settings' 
                );
                $where = $reportTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
                $report = $reportTable->fetchRow($where);
                if ($report ['report_stage'] != 'finished')
                {
                    $reportTable->update($reportData, $where);
                }
                
                // redirect back to mapping page
                $this->_redirect('data/reportsettings');
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
        $db = Zend_Db_Table::getDefaultAdapter();
        $invalid_data = $this->_getParam('filter', 0);
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
        {
            $sidx = 9;
        }
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->joinLeft(array (
                'di' => 'device_instance' 
        ), 'di.upload_data_collector_id = udc.upload_data_collector_id', array (
                'device_instance_id AS di_device_instance_id', 
                'master_device_id', 
                'is_excluded AS di_is_excluded' 
        ))
            ->joinLeft(array (
                'udi' => 'unknown_device_instance' 
        ), 'udi.upload_data_collector_id = udc.upload_data_collector_id', array (
                'unknown_device_instance_id AS udi_unknown_device_instance_id', 
                'is_leased AS udi_is_leased', 
                'is_excluded AS udi_is_excluded' 
        ))
            ->joinLeft(array (
                'md' => 'master_device' 
        ), 'md.master_device_id = di.master_device_id', array (
                'printer_model', 
                'is_leased' 
        ))
            ->joinLeft(array (
                'm' => 'manufacturer' 
        ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                'manufacturer_name' 
        ))
            ->where('udc.report_id = ?', $report_id, 'INTEGER')
            ->where('udc.invalid_data = ?', $invalid_data, 'INTEGER')
            ->where('udc.is_excluded = 0')
            ->order('udc.modelname');
        $stmt = $db->query($select);
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
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->joinLeft(array (
                'di' => 'device_instance' 
        ), 'di.upload_data_collector_id = udc.upload_data_collector_id', array (
                'device_instance_id AS di_device_instance_id', 
                'master_device_id', 
                'is_excluded AS di_is_excluded' 
        ))
            ->joinLeft(array (
                'udi' => 'unknown_device_instance' 
        ), 'udi.upload_data_collector_id = udc.upload_data_collector_id', array (
                'unknown_device_instance_id AS udi_unknown_device_instance_id', 
                'device_manufacturer AS udi_device_manufacturer', 
                'printer_model AS udi_printer_model', 
                'is_leased AS udi_is_leased', 
                'is_excluded AS udi_is_excluded' 
        ))
            ->joinLeft(array (
                'md' => 'master_device' 
        ), 'md.master_device_id = di.master_device_id', array (
                'printer_model', 
                'is_leased' 
        ))
            ->joinLeft(array (
                'm' => 'manufacturer' 
        ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                'manufacturer_name' 
        ))
            ->where('udc.report_id = ?', $report_id, 'INTEGER')
            ->where('udc.invalid_data = ?', $invalid_data, 'INTEGER')
            ->where('udc.is_excluded = 0')
            ->order($sidx . ' ' . $sord)
            ->limit($limit, $start);
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        
        try
        {
            if (count($result) > 0)
            {
                $i = 0;
                $formdata->page = $page;
                $formdata->total = $total_pages;
                $formdata->records = $count;
                foreach ( $result as $key => $value )
                {
                    // set up mapped to suggestions
                    $ampv = 0;
                    $is_leased = $result [$key] ['is_leased'];
                    $devices_pf_id = $result [$key] ['devices_pf_id'];
                    $upload_data_collector_id = $result [$key] ['upload_data_collector_id'];
                    
                    $mapped_to = '';
                    $mapped_to_id = null;
                    if ($result [$key] ['udi_unknown_device_instance_id'] > 0)
                    {
                        $mapped_to = ucwords(strtolower($result [$key] ['udi_device_manufacturer'] . ' ' . $result [$key] ['udi_printer_model'])) . '<span style="color: red;"> (New)</span>';
                        $mapped_to_id = "udi" . $result [$key] ['udi_unknown_device_instance_id'];
                        $is_excluded = $result [$key] ['udi_is_excluded'];
                        $is_leased = $result [$key] ['udi_is_leased'];
                        
                        // get average monthly page volume for unknown device
                        $unknown_device_instanceMapper = Proposalgen_Model_Mapper_UnknownDeviceInstance::getInstance();
                        $unknown_device_instance = $unknown_device_instanceMapper->fetchAllUnknownDevicesAsKnownDevices($report_id, 'unknown_device_instance_id = ' . $result [$key] ['udi_unknown_device_instance_id']);
                        
                        if (count($unknown_device_instance) > 0)
                        {
                            $ampv = number_format($unknown_device_instance [0]->_averageMonthlyPageCount);
                        }
                    }
                    else if ($result [$key] ['di_device_instance_id'] > 0)
                    {
                        $mapped_to = $result [$key] ['manufacturer_name'] . ' ' . $result [$key] ['printer_model'];
                        $mapped_to_id = $result [$key] ['master_device_id'];
                        $is_excluded = $result [$key] ['di_is_excluded'];
                        
                        // get average monthly page volume
                        $device_instanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
                        $device_instance = $device_instanceMapper->fetchRow('device_instance_id = ' . $result [$key] ['di_device_instance_id']);
                        
                        if (count($device_instance) > 0)
                        {
                            $ampv = number_format($device_instance->AverageMonthlyPageCount);
                        }
                    }
                    else
                    {
                        $is_excluded = 0;
                    }
                    
                    $formdata->rows [$i] ['id'] = $upload_data_collector_id;
                    $formdata->rows [$i] ['cell'] = array (
                            $upload_data_collector_id, 
                            $result [$key] ['devices_pf_id'], 
                            ucwords(strtolower($result [$key] ['modelname'])) . "<br />(" . $result [$key] ['ipaddress'] . ")", 
                            $mapped_to, 
                            $ampv, 
                            $is_leased, 
                            $is_excluded, 
                            $mapped_to_id 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function deviceleasingexcludedlistAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $page = $_GET ['page'];
        $limit = $_GET ['rows'];
        $sidx = $_GET ['sidx'];
        $sord = $_GET ['sord'];
        if (! $sidx)
        {
            $sidx = 9;
        }
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->where('(invalid_data = 1 OR is_excluded = 1) AND report_id = ' . $report_id);
        $stmt = $db->query($select);
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
            ->from(array (
                'udc' => 'proposalgenerator_upload_data_collector_rows' 
        ))
            ->where('(invalid_data = 1 OR is_excluded = 1) AND report_id = ' . $report_id)
            ->order($sidx . ' ' . $sord)
            ->limit($limit, $start);
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        
        try
        {
            if (count($result) > 0)
            {
                $i = 0;
                $formdata->page = $page;
                $formdata->total = $total_pages;
                $formdata->records = $count;
                foreach ( $result as $key => $value )
                {
                    // device must be at least 4 days old
                    $days = 5;
                    $startDate = new DateTime($result [$key] ['startdate']);
                    $endDate = new DateTime($result [$key] ['enddate']);
                    $discoveryDate = new DateTime($result [$key] ['discovery_date']);
                    
                    $interval1 = $startDate->diff($endDate);
                    $interval2 = $discoveryDate->diff($endDate);
                    
                    $days = $interval1;
                    if ($interval1->days > $interval2->days && ! $interval2->invert)
                    {
                        $days = $interval2;
                    }
                    
                    $upload_data_collector_id = $result [$key] ['upload_data_collector_id'];
                    
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
                    
                    $formdata->rows [$i] ['id'] = $upload_data_collector_id;
                    $formdata->rows [$i] ['cell'] = array (
                            $upload_data_collector_id, 
                            $result [$key] ['devices_pf_id'], 
                            ucwords(strtolower($result [$key] ['modelname'])) . " (" . $result [$key] ['ipaddress'] . ")", 
                            $reason 
                    );
                    $i ++;
                }
            }
            else
            {
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function devicedetailsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);
        
        try
        {
            if ($deviceID > 0)
            {
                // get default prices
                $default_price = 0;
                $userTable = new Proposalgen_Model_DbTable_Users();
                $where = $userTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                $user = $userTable->fetchRow($where);
                
                if (count($user) > 0)
                {
                    // check user
                    if ($user ['user_default_printer_cost'] > 0)
                    {
                        $default_price = money_format('%i', $user ['user_default_printer_cost']);
                    }
                    else
                    {
                        // check master
                        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
                        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', 1, 'INTEGER');
                        $dealer_company = $dealer_companyTable->fetchRow($where);
                        
                        if (count($dealer_company) > 0)
                        {
                            $default_price = money_format('%i', $dealer_company ['dc_default_printer_cost']);
                        }
                    }
                }
                
                // get user price margin
                $price_margin = $this->getPricingMargin('user', $this->dealer_company_id);
                if ($price_margin == 0)
                {
                    // get master price margin
                    $price_margin = $this->getPricingMargin('master', $this->dealer_company_id);
                }
                $price_margin = ($price_margin / 100) + 1;
                
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        'md' => 'master_device' 
                ))
                    ->joinLeft(array (
                        'm' => 'manufacturer' 
                ), 'm.manufacturer_id = md.mastdevice_manufacturer')
                    ->joinLeft(array (
                        'tc' => 'toner_config' 
                ), 'tc.toner_config_id = md.toner_config_id')
                    ->where('md.master_device_id = ?', $deviceID);
                $stmt = $db->query($select);
                $row = $stmt->fetchAll();
                
                if (count($row) > 0)
                {
                    // check for override price
                    $user_device_overrideTable = new Proposalgen_Model_DbTable_UserDeviceOverride();
                    $where = $user_device_overrideTable->getAdapter()->quoteInto('master_device_id = ' . $deviceID . ' AND user_id = ?', $this->user_id, 'INTEGER');
                    $user_device_override = $user_device_overrideTable->fetchRow($where);
                    
                    if (count($user_device_override) > 0)
                    {
                        $price = $user_device_override [0] ['override_device_price'];
                    }
                    else
                    {
                        $price = $row [0] ['device_price'] * $price_margin;
                    }
                    
                    $launch_date = new Zend_Date($row [0] ['launch_date'], "yyyy/mm/dd HH:ii:ss");
                    $formdata = array (
                            'manufacturer' => $row [0] ['manufacturer_name'], 
                            'printer_model' => $row [0] ['printer_model'], 
                            'launch_date' => $launch_date->toString('mm/dd/yyyy'), 
                            'is_copier' => $row [0] ['is_copier'], 
                            'is_scanner' => $row [0] ['is_scanner'], 
                            'is_fax' => $row [0] ['is_fax'], 
                            'is_duplex' => $row [0] ['is_duplex'], 
                            'ppm_black' => $row [0] ['PPM_black'], 
                            'ppm_color' => $row [0] ['PPM_color'], 
                            'duty_cycle' => $row [0] ['duty_cycle'], 
                            'watts_power_normal' => $row [0] ['watts_power_normal'], 
                            'watts_power_idle' => $row [0] ['watts_power_idle'], 
                            'device_price' => number_format($price > 0 ? $price : $default_price, 2, '.', ','), 
                            'toner_config' => ucwords(strtolower($row [0] ['toner_config_name'])), 
                            'is_leased' => $row [0] ['is_leased'], 
                            'leased_toner_yield' => $row [0] ['leased_toner_yield'] 
                    );
                }
            }
            else
            {
                // empty form values
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device.", 0, $e);
        } // end catch
        

        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function unknowndevicedetailsAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);
        
        try
        {
            if ($deviceID > 0)
            {
                // get default prices
                $default_price = 0;
                $userTable = new Proposalgen_Model_DbTable_Users();
                $where = $userTable->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                $user = $userTable->fetchRow($where);
                
                if (count($user) > 0)
                {
                    // check user
                    if ($user ['user_default_printer_cost'] > 0)
                    {
                        $default_price = money_format('%i', $user ['user_default_printer_cost']);
                    }
                    else
                    {
                        // check master
                        $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
                        $where = $dealer_companyTable->getAdapter()->quoteInto('dealer_company_id = ?', 1, 'INTEGER');
                        $dealer_company = $dealer_companyTable->fetchRow($where);
                        
                        if (count($dealer_company) > 0)
                        {
                            $default_price = money_format('%i', $dealer_company ['dc_default_printer_cost']);
                        }
                    }
                }
                
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        'udi' => 'unknown_device_instance' 
                ))
                    ->joinLeft(array (
                        'tc' => 'toner_config' 
                ), 'tc.toner_config_id = udi.toner_config_id')
                    ->where('udi.unknown_device_instance_id = ?', $deviceID);
                $stmt = $db->query($select);
                $row = $stmt->fetchAll();
                
                if (count($row) > 0)
                {
                    $launch_date = new Zend_Date($row [0] ['launch_date'], "yyyy/mm/dd HH:ii:ss");
                    $formdata = array (
                            'manufacturer' => $row [0] ['device_manufacturer'], 
                            'printer_model' => $row [0] ['printer_model'] . " (New)", 
                            'launch_date' => $launch_date->toString('mm/dd/yyyy'), 
                            'is_copier' => $row [0] ['is_copier'], 
                            'is_scanner' => $row [0] ['is_scanner'], 
                            'is_fax' => $row [0] ['is_fax'], 
                            'is_duplex' => $row [0] ['is_duplex'], 
                            'ppm_black' => $row [0] ['PPM_black'], 
                            'ppm_color' => $row [0] ['PPM_color'], 
                            'duty_cycle' => $row [0] ['duty_cycle'], 
                            'watts_power_normal' => $row [0] ['watts_power_normal'], 
                            'watts_power_idle' => $row [0] ['watts_power_idle'], 
                            'device_price' => number_format($row [0] ['device_price'] > 0 ? $row [0] ['device_price'] : $default_price, 2, '.', ','), 
                            'toner_config' => ucwords(strtolower($row [0] ['toner_config_name'])), 
                            'is_leased' => $row [0] ['is_leased'], 
                            'leased_toner_yield' => null 
                    );
                }
            }
            else
            {
                // empty form values
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device.", 0, $e);
        } // end catch
        

        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function devicetonersAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);
        
        try
        {
            $select = new Zend_Db_Select($db);
            $select = $db->select();
            $select->from(array (
                    'dt' => 'device_toner' 
            ))
                ->joinLeft(array (
                    't' => 'toner' 
            ), 't.toner_id = dt.toner_id')
                ->joinLeft(array (
                    'pt' => 'part_type' 
            ), 'pt.part_type_id = t.part_type_id')
                ->joinLeft(array (
                    'tc' => 'toner_color' 
            ), 'tc.toner_color_id = t.toner_color_id')
                ->joinLeft(array (
                    'm' => 'manufacturer' 
            ), 'm.manufacturer_id = t.manufacturer_id')
                ->where('dt.master_device_id = ?', $deviceID);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            if (count($result) > 0)
            {
                $i = 0;
                $type_name = '';
                
                // get price margin
                $price_margin = $this->getPricingMargin('user', $this->dealer_company_id);
                if ($price_margin == 0)
                {
                    $price_margin = $this->getPricingMargin('master', $this->dealer_company_id);
                }
                $price_margin = ($price_margin / 100) + 1;
                
                foreach ( $result as $row )
                {
                    $toner_id = $row ['toner_id'];
                    
                    // check for override price
                    $user_toner_overrideTable = new Proposalgen_Model_DbTable_UserTonerOverride();
                    $where = $user_toner_overrideTable->getAdapter()->quoteInto('toner_id = ' . $toner_id . ' AND user_id = ?', $this->user_id, 'INTEGER');
                    $user_toner_override = $user_toner_overrideTable->fetchRow($where);
                    
                    if (count($user_toner_override) > 0)
                    {
                        $price = $user_toner_override [0] ['override_toner_price'];
                    }
                    else
                    {
                        $price = $row ['toner_price'] * $price_margin;
                    }
                    
                    // Always uppercase OEM, but just captialize everything else
                    $type_name = ucwords(strtolower($row ['type_name']));
                    if ($type_name == "Oem")
                    {
                        $type_name = "OEM";
                    }
                    
                    $formdata->rows [$i] ['id'] = $row ['toner_id'];
                    $formdata->rows [$i] ['cell'] = array (
                            $row ['toner_id'], 
                            $row ['toner_SKU'], 
                            ucwords(strtolower($row ['manufacturer_name'])), 
                            $type_name, 
                            ucwords(strtolower($row ['toner_color_name'])), 
                            $row ['toner_yield'], 
                            money_format('%i', $price), 
                            $row ['master_device_id'] 
                    );
                    $i ++;
                }
            }
            else
            {
                // empty form values
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device parts.", 0, $e);
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function unknowndevicetonersAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();
        $deviceID = $this->_getParam('deviceid', false);
        
        try
        {
            $select = new Zend_Db_Select($db);
            $select = $db->select()
                ->from(array (
                    'udi' => 'unknown_device_instance' 
            ))
                ->joinLeft(array (
                    'tc' => 'toner_config' 
            ), 'tc.toner_config_id = udi.toner_config_id')
                ->where('udi.unknown_device_instance_id = ?', $deviceID);
            $stmt = $db->query($select);
            $result = $stmt->fetchAll();
            
            if (count($result) > 0)
            {
                $i = 0;
                $color_array = array (
                        'black', 
                        'cyan', 
                        'yellow', 
                        'magenta', 
                        '3color', 
                        '4color' 
                );
                
                foreach ( $color_array as $color )
                {
                    if ($result [0] [$color . '_toner_SKU'] != '')
                    {
                        $formdata->rows [$i] ['id'] = $i;
                        $formdata->rows [$i] ['cell'] = array (
                                $i, 
                                $result [0] [$color . '_toner_SKU'], 
                                null, 
                                "OEM", 
                                ucwords(strtolower($color)), 
                                $result [0] [$color . '_toner_yield'], 
                                money_format('%i', $result [0] [$color . '_toner_price']), 
                                null 
                        );
                        $i ++;
                    }
                }
            }
            else
            {
                // empty form values
                $formdata = array ();
            }
        }
        catch ( Exception $e )
        {
            // critical exception
            Throw new exception("Critical Error: Unable to find device parts.", 0, $e);
        }
        
        // encode user data to return to the client:
        $json = Zend_Json::encode($formdata);
        $this->view->data = $json;
    }

    public function adddeviceAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->formTitle = 'Add Unknown Printer';
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        // add device form
        $form = new Proposalgen_Form_UnknownDevice(null, "edit");
        
        // hide device instance related fields
        $form->removeElement('ipaddress');
        $form->removeElement('serial_number');
        
        // fill toner_config dropdown
        $toner_configTable = new Proposalgen_Model_DbTable_TonerConfig();
        $toner_configs = $toner_configTable->fetchAll();
        $currElement = $form->getElement('toner_config');
        $currElement->addMultiOption('', 'Select Toner Config');
        foreach ( $toner_configs as $row )
        {
            $currElement->addMultiOption($row ['toner_config_id'], ucwords(strtolower($row ['toner_config_name'])));
        }
        
        // hard code the filtered lists since there is no way to determine them
        // though the database
        $this->view->blackOnlyList = "1:BLACK";
        $this->view->seperateColorList = "1:BLACK;2:CYAN;3:MAGENTA;4:YELLOW";
        $this->view->threeColorList = "5:3 COLOR";
        $this->view->fourColorList = "6:4 COLOR";
        
        // check if this page has been posted to
        if ($this->_request->isPost())
        {
            $date = date('Y-m-d H:i:s T');
            $formData = $this->_request->getPost();
            $upload_data_collector_id = 0;
            
            // conditional requirements (if leased, don't make toners required)
            if (isset($formData ['request_support']) || isset($formData ['is_leased']))
            {
                $form->set_validation($formData);
            }
            
            if (isset($formData ["hdnID"]))
            {
                $upload_data_collector_id = $formData ["hdnID"];
            }
            else if (isset($formData ['btnSubmitRequest']))
            {
                $db->beginTransaction();
                try
                {
                    $ticket_id = $formData ['ticket_id'];
                    $devices_pf_id = $formData ['devices_pf_id'];
                    $request_description = $formData ['request_description'];
                    $upload_data_collector_id = $formData ['upload_data_collector_id'];
                    
                    if ($ticket_id > 0)
                    {
                        $ticket_comment = $formData ['txtComment'];
                        
                        if ($request_description != '')
                        {
                            // update ticket
                            $ticketTable = new Proposalgen_Model_DbTable_Tickets();
                            $ticketData = array (
                                    'description' => $request_description, 
                                    'date_updated' => $date 
                            );
                            $where = $ticketTable->getAdapter()->quoteInto('ticket_id = ?', $ticket_id, 'INTEGER');
                            $ticketTable->update($ticketData, $where);
                            
                            // add comment
                            if ($ticket_comment != '')
                            {
                                $ticket_commentsTable = new Proposalgen_Model_DbTable_TicketComments();
                                $ticket_commentsData = array (
                                        'ticket_id' => $ticket_id, 
                                        'user_id' => $this->user_id, 
                                        'comment_text' => $ticket_comment, 
                                        'comment_date' => $date 
                                );
                                $ticket_commentsTable->insert($ticket_commentsData);
                            }
                            
                            $db->commit();
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
                        $ticketTable = new Proposalgen_Model_DbTable_Tickets();
                        $ticketData = array (
                                'user_id' => $this->user_id, 
                                'category_id' => Proposalgen_Model_TicketCategory::PRINTFLEET_DEVICE_SUPPORT, 
                                'status_id' => Proposalgen_Model_TicketStatus::STATUS_NEW, 
                                'title' => $request_title, 
                                'description' => $request_description, 
                                'date_created' => $date, 
                                'date_updated' => $date 
                        );
                        $ticket_id = $ticketTable->insert($ticketData);
                        
                        // get default pf data from upload_data_collector
                        $select = new Zend_Db_Select($db);
                        $select = $db->select()
                            ->from(array (
                                'udc' => 'proposalgenerator_upload_data_collector_rows' 
                        ))
                            ->where('id = ' . $report_id . ' AND devices_pf_id = ?', $devices_pf_id, 'INTEGER');
                        $stmt = $db->query($select);
                        $result = $stmt->fetchAll();
                        
                        // may return more then one record, but just grab data
                        // from first matching record
                        if (count($result) > 0)
                        {
                            $device_manufacturer = $result [0] ['manufacturer'];
                            $printer_model = $result [0] ['modelname'];
                            $launch_date = $result [0] ['date_introduction'];
                            $device_price = 0;
                            $service_cost_per_page = 0;
                            $toner_config = ($result [0] ['startmetercolor'] > 0 ? 2 : 1);
                            $is_copier = $result [0] ['is_copier'];
                            $is_fax = $result [0] ['is_fax'];
                            $is_duplex = 0;
                            $is_scanner = $result [0] ['is_scanner'];
                            $PPM_black = $result [0] ['ppm_black'];
                            $PPM_color = $result [0] ['ppm_color'];
                            $duty_cycle = $result [0] ['duty_cycle'];
                            $watts_power_normal = $result [0] ['wattspowernormal'];
                            $watts_power_idle = $result [0] ['wattspoweridle'];
                        }
                        
                        // save printer request ticket
                        $printer_requestTable = new Proposalgen_Model_DbTable_TicketPFRequests();
                        $printer_requestData = array (
                                'ticket_id' => $ticket_id, 
                                'user_id' => $this->user_id, 
                                'devices_pf_id' => $devices_pf_id, 
                                'device_manufacturer' => $device_manufacturer, 
                                'printer_model' => $printer_model, 
                                'launch_date' => $launch_date, 
                                'device_price' => $device_price, 
                                'service_cost_per_page' => $service_cost_per_page, 
                                'toner_config' => $toner_config, 
                                'is_copier' => $is_copier, 
                                'is_fax' => $is_fax, 
                                'is_duplex' => $is_duplex, 
                                'is_scanner' => $is_scanner, 
                                'PPM_black' => $PPM_black, 
                                'PPM_color' => $PPM_color, 
                                'duty_cycle' => $duty_cycle, 
                                'watts_power_normal' => $watts_power_normal, 
                                'watts_power_idle' => $watts_power_idle 
                        );
                        $printer_request_id = $printer_requestTable->insert($printer_requestData);
                        
                        $db->commit();
                        $this->view->message = "Support Request Submitted.";
                    }
                }
                catch ( Exception $e )
                {
                    $db->rollBack();
                    echo $e;
                    die();
                    $this->view->message = "There was an error saving your support request.";
                }
            }
            else if ($form->isValid($formData))
            {
                $valid_toners = true;
                $db->beginTransaction();
                $grid = $formData ['grid'];
                $this->view->field = "";
                
                // validate toner config - simply make sure unneeded fields are
                // empty and no value saved
                $black_array = array (
                        null, 
                        null, 
                        null 
                );
                $cyan_array = array (
                        null, 
                        null, 
                        null 
                );
                $magenta_array = array (
                        null, 
                        null, 
                        null 
                );
                $yellow_array = array (
                        null, 
                        null, 
                        null 
                );
                $three_color_array = array (
                        null, 
                        null, 
                        null 
                );
                $four_color_array = array (
                        null, 
                        null, 
                        null 
                );
                
                // COMP TONERS
                $black_comp_array = array (
                        null, 
                        null, 
                        null 
                );
                $cyan_comp_array = array (
                        null, 
                        null, 
                        null 
                );
                $magenta_comp_array = array (
                        null, 
                        null, 
                        null 
                );
                $yellow_comp_array = array (
                        null, 
                        null, 
                        null 
                );
                $three_color_comp_array = array (
                        null, 
                        null, 
                        null 
                );
                $four_color_comp_array = array (
                        null, 
                        null, 
                        null 
                );
                
                switch ($formData ['toner_config'])
                {
                    case "1" :
                        // black only - sku / price / yield
                        $black_array = array (
                                $formData ['black_toner_SKU'], 
                                $formData ['black_toner_price'], 
                                $formData ['black_toner_yield'] 
                        );
                        
                        if (($formData ['is_leased'] && empty($formData ['black_toner_yield'])) || (! $formData ['is_leased'] && (empty($formData ['black_toner_SKU']) || empty($formData ['black_toner_price']) || empty($formData ['black_toner_yield']))))
                        {
                            $valid_toners = false;
                            $this->view->field = "black";
                            $this->view->message = "Incomplete black toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        
                        // validate black comp fields
                        if (! $formData ['is_leased'])
                        {
                            $black_comp_array = array (
                                    $formData ['black_comp_SKU'], 
                                    $formData ['black_comp_price'], 
                                    $formData ['black_comp_yield'] 
                            );
                            if (! empty($formData ['black_comp_SKU']) || ! empty($formData ['black_comp_price']) || ! empty($formData ['black_comp_yield']))
                            {
                                if (empty($formData ['black_comp_SKU']) || empty($formData ['black_comp_price']) || empty($formData ['black_comp_yield']))
                                {
                                    $valid_toners = false;
                                    $this->view->field = "black_comp";
                                    $this->view->message = "Incomplete compatible black toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                            }
                        }
                        
                        break;
                    case "2" :
                        // 3 color - separated - sku / price / yield
                        $black_array = array (
                                $formData ['black_toner_SKU'], 
                                $formData ['black_toner_price'], 
                                $formData ['black_toner_yield'] 
                        );
                        $cyan_array = array (
                                $formData ['cyan_toner_SKU'], 
                                $formData ['cyan_toner_price'], 
                                $formData ['cyan_toner_yield'] 
                        );
                        $magenta_array = array (
                                $formData ['magenta_toner_SKU'], 
                                $formData ['magenta_toner_price'], 
                                $formData ['magenta_toner_yield'] 
                        );
                        $yellow_array = array (
                                $formData ['yellow_toner_SKU'], 
                                $formData ['yellow_toner_price'], 
                                $formData ['yellow_toner_yield'] 
                        );
                        if (($formData ['is_leased'] && empty($formData ['black_toner_yield'])) || (! $formData ['is_leased'] && (empty($formData ['black_toner_SKU']) || empty($formData ['black_toner_price']) || empty($formData ['black_toner_yield']))))
                        {
                            $valid_toners = false;
                            $this->view->field = "black";
                            $this->view->message = "Incomplete black toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        if (($formData ['is_leased'] && empty($formData ['cyan_toner_yield'])) || (! $formData ['is_leased'] && (empty($formData ['cyan_toner_SKU']) || empty($formData ['cyan_toner_price']) || empty($formData ['cyan_toner_yield']))))
                        {
                            $valid_toners = false;
                            $this->view->field = "cyan";
                            $this->view->message = "Incomplete cyan toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        if (($formData ['is_leased'] && empty($formData ['magenta_toner_yield'])) || (! $formData ['is_leased'] && (empty($formData ['magenta_toner_SKU']) || empty($formData ['magenta_toner_price']) || empty($formData ['magenta_toner_yield']))))
                        {
                            $valid_toners = false;
                            $this->view->field = "magenta";
                            $this->view->message = "Incomplete magenta toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        if (($formData ['is_leased'] && empty($formData ['yellow_toner_yield'])) || (! $formData ['is_leased'] && (empty($formData ['yellow_toner_SKU']) || empty($formData ['yellow_toner_price']) || empty($formData ['yellow_toner_yield']))))
                        {
                            $valid_toners = false;
                            $this->view->field = "yellow";
                            $this->view->message = "Incomplete yellow toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        
                        // COMP 3 color - separated - sku / price / yield
                        if (! $formData ['is_leased'])
                        {
                            $black_comp_array = array (
                                    $formData ['black_comp_SKU'], 
                                    $formData ['black_comp_price'], 
                                    $formData ['black_comp_yield'] 
                            );
                            $cyan_comp_array = array (
                                    $formData ['cyan_comp_SKU'], 
                                    $formData ['cyan_comp_price'], 
                                    $formData ['cyan_comp_yield'] 
                            );
                            $magenta_comp_array = array (
                                    $formData ['magenta_comp_SKU'], 
                                    $formData ['magenta_comp_price'], 
                                    $formData ['magenta_comp_yield'] 
                            );
                            $yellow_comp_array = array (
                                    $formData ['yellow_comp_SKU'], 
                                    $formData ['yellow_comp_price'], 
                                    $formData ['yellow_comp_yield'] 
                            );
                            if (! empty($formData ['black_comp_SKU']) || ! empty($formData ['black_comp_price']) || ! empty($formData ['black_comp_yield']))
                            {
                                if (empty($formData ['black_comp_SKU']) || empty($formData ['black_comp_price']) || empty($formData ['black_comp_yield']))
                                {
                                    $valid_comps = false;
                                    $this->view->field = "black_comp";
                                    $this->view->message = "Incomplete compatible black toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                            }
                            if (! empty($formData ['cyan_comp_SKU']) || ! empty($formData ['cyan_comp_price']) || ! empty($formData ['cyan_comp_yield']))
                            {
                                if (empty($formData ['cyan_comp_SKU']) || empty($formData ['cyan_comp_price']) || empty($formData ['cyan_comp_yield']))
                                {
                                    $valid_comps = false;
                                    $this->view->field = "cyan_comp";
                                    $this->view->message = "Incomplete compatible cyan toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                            }
                            if (! empty($formData ['magenta_comp_SKU']) || ! empty($formData ['magenta_comp_price']) || ! empty($formData ['magenta_comp_yield']))
                            {
                                if (empty($formData ['magenta_comp_SKU']) || empty($formData ['magenta_comp_price']) || empty($formData ['magenta_comp_yield']))
                                {
                                    $valid_comps = false;
                                    $this->view->field = "magenta_comp";
                                    $this->view->message = "Incomplete compatible magenta toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                            }
                            if (! empty($formData ['yellow_comp_SKU']) || ! empty($formData ['yellow_comp_price']) || ! empty($formData ['yellow_comp_yield']))
                            {
                                if (empty($formData ['yellow_comp_SKU']) || empty($formData ['yellow_comp_price']) || empty($formData ['yellow_comp_yield']))
                                {
                                    $valid_comps = false;
                                    $this->view->field = "yellow_comp";
                                    $this->view->message = "Incomplete compatible yellow toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                            }
                        }
                        break;
                    case "3" :
                        // 3 color - combined - sku / price / yield
                        $black_array = array (
                                $formData ['black_toner_SKU'], 
                                $formData ['black_toner_price'], 
                                $formData ['black_toner_yield'] 
                        );
                        $three_color_array = array (
                                $formData ['3color_toner_SKU'], 
                                $formData ['3color_toner_price'], 
                                $formData ['3color_toner_yield'] 
                        );
                        if (($formData ['is_leased'] && empty($formData ['black_toner_yield'])) || (! $formData ['is_leased'] && (empty($formData ['black_toner_SKU']) || empty($formData ['black_toner_price']) || empty($formData ['black_toner_yield']))))
                        {
                            $valid_toners = false;
                            $this->view->field = "black";
                            $this->view->message = "Incomplete black toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        if (($formData ['is_leased'] && empty($formData ['3color_toner_yield'])) || (! $formData ['is_leased'] && (empty($formData ['3color_toner_SKU']) || empty($formData ['3color_toner_price']) || empty($formData ['3color_toner_yield']))))
                        {
                            $valid_toners = false;
                            $this->view->field = "3color";
                            $this->view->message = "Incomplete 3 color toner data supplied.<br />Please fill out all fields.";
                            break;
                        }
                        
                        // COMP 3 color - combined - sku / price / yield
                        if (! $formData ['is_leased'])
                        {
                            $black_comp_array = array (
                                    $formData ['black_comp_SKU'], 
                                    $formData ['black_comp_price'], 
                                    $formData ['black_comp_yield'] 
                            );
                            $three_color_comp_array = array (
                                    $formData ['3color_comp_SKU'], 
                                    $formData ['3color_comp_price'], 
                                    $formData ['3color_comp_yield'] 
                            );
                            
                            if (! empty($formData ['black_comp_SKU']) || ! empty($formData ['black_comp_price']) || ! empty($formData ['black_comp_yield']))
                            {
                                if (empty($formData ['black_comp_SKU']) || empty($formData ['black_comp_price']) || empty($formData ['black_comp_yield']))
                                {
                                    $valid_comps = false;
                                    $this->view->field = "black_comp";
                                    $this->view->message = "Incomplete compatible black toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                            }
                            if (! empty($formData ['3color_comp_SKU']) || ! empty($formData ['3color_comp_price']) || ! empty($formData ['3color_comp_yield']))
                            {
                                if (empty($formData ['3color_comp_SKU']) || empty($formData ['3color_comp_price']) || empty($formData ['3color_comp_yield']))
                                {
                                    $valid_comps = false;
                                    $this->view->field = "3color_comp";
                                    $this->view->message = "Incomplete compatible 3 color toner data supplied.<br />Please fill out all fields.";
                                    break;
                                }
                            }
                        }
                        break;
                    case "4" :
                        // 4 color - combined - sku / price / yield
                        $four_color_array = array (
                                $formData ['4color_toner_SKU'], 
                                $formData ['4color_toner_price'], 
                                $formData ['4color_toner_yield'] 
                        );
                        if (($formData ['is_leased'] && empty($formData ['4color_toner_yield'])) || (! $formData ['is_leased'] && (empty($formData ['4color_toner_SKU']) || empty($formData ['4color_toner_price']) || empty($formData ['4color_toner_yield']))))
                        {
                            $valid_toners = false;
                            $this->view->field = "4color";
                            $this->view->message = "Incomplete 4 color toner data supplied. Please fill out all fields.";
                            break;
                        }
                        
                        // COMP 4 color - combined - sku / price / yield
                        if (! $formData ['is_leased'])
                        {
                            $four_color_comp_array = array (
                                    $formData ['4color_comp_SKU'], 
                                    $formData ['4color_comp_price'], 
                                    $formData ['4color_comp_yield'] 
                            );
                            if (! empty($formData ['4color_comp_SKU']) || ! empty($formData ['4color_comp_price']) || ! empty($formData ['4color_comp_yield']))
                            {
                                if (empty($formData ['4color_comp_SKU']) || empty($formData ['4color_comp_price']) || empty($formData ['4color_comp_yield']))
                                {
                                    $valid_comps = false;
                                    $this->view->field = "4color";
                                    $this->view->message = "Incomplete compatible 4 color toner data supplied.<br />Please fill out all fields.";
                                    break;
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
                        $launch_date = new Zend_Date($formData ['mps_launch_date'], "mm/dd/yyyy HH:ii:ss");
                        $is_excluded = 0;
                        
                        $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                        $unknown_device_instanceData = array (
                                'device_manufacturer' => ucwords(trim($formData ["device_manufacturer"])), 
                                'printer_model' => ucwords(trim($formData ["printer_model"])), 
                                'launch_date' => $launch_date->toString('yyyy/mm/dd HH:ss'), 
                                'is_copier' => ($formData ['is_copier'] == true ? 1 : 0), 
                                'is_scanner' => ($formData ['is_scanner'] == true ? 1 : 0), 
                                'is_fax' => ($formData ['is_fax'] == true ? 1 : 0), 
                                'is_duplex' => ($formData ['is_duplex'] == true ? 1 : 0), 
                                'ppm_black' => ($formData ['ppm_black'] > 0 ? $formData ['ppm_black'] : null), 
                                'ppm_color' => ($formData ['ppm_color'] > 0 ? $formData ['ppm_color'] : null), 
                                'duty_cycle' => ($formData ['duty_cycle'] > 0 ? $formData ['duty_cycle'] : null), 
                                'watts_power_normal' => ($formData ['watts_power_normal'] > 0 ? $formData ['watts_power_normal'] : null), 
                                'watts_power_idle' => ($formData ['watts_power_idle'] > 0 ? $formData ['watts_power_idle'] : null), 
                                'device_price' => ($formData ['device_price'] > 0 ? $formData ['device_price'] : null), 
                                'toner_config_id' => $formData ["toner_config"], 
                                'black_toner_SKU' => $black_array [0], 
                                'black_toner_price' => $black_array [1], 
                                'black_toner_yield' => $black_array [2], 
                                'cyan_toner_SKU' => $cyan_array [0], 
                                'cyan_toner_price' => $cyan_array [1], 
                                'cyan_toner_yield' => $cyan_array [2], 
                                'magenta_toner_SKU' => $magenta_array [0], 
                                'magenta_toner_price' => $magenta_array [1], 
                                'magenta_toner_yield' => $magenta_array [2], 
                                'yellow_toner_SKU' => $yellow_array [0], 
                                'yellow_toner_price' => $yellow_array [1], 
                                'yellow_toner_yield' => $yellow_array [2], 
                                '3color_toner_SKU' => $three_color_array [0], 
                                '3color_toner_price' => $three_color_array [1], 
                                '3color_toner_yield' => $three_color_array [2], 
                                '4color_toner_SKU' => $four_color_array [0], 
                                '4color_toner_price' => $four_color_array [1], 
                                '4color_toner_yield' => $four_color_array [2], 
                                
                                'black_comp_SKU' => $black_comp_array [0], 
                                'black_comp_price' => $black_comp_array [1], 
                                'black_comp_yield' => $black_comp_array [2], 
                                'cyan_comp_SKU' => $cyan_comp_array [0], 
                                'cyan_comp_price' => $cyan_comp_array [1], 
                                'cyan_comp_yield' => $cyan_comp_array [2], 
                                'magenta_comp_SKU' => $magenta_comp_array [0], 
                                'magenta_comp_price' => $magenta_comp_array [1], 
                                'magenta_comp_yield' => $magenta_comp_array [2], 
                                'yellow_comp_SKU' => $yellow_comp_array [0], 
                                'yellow_comp_price' => $yellow_comp_array [1], 
                                'yellow_comp_yield' => $yellow_comp_array [2], 
                                '3color_comp_SKU' => $three_color_comp_array [0], 
                                '3color_comp_price' => $three_color_comp_array [1], 
                                '3color_comp_yield' => $three_color_comp_array [2], 
                                '4color_comp_SKU' => $four_color_comp_array [0], 
                                '4color_comp_price' => $four_color_comp_array [1], 
                                '4color_comp_yield' => $four_color_comp_array [2], 
                                
                                'is_excluded' => $is_excluded, 
                                'is_leased' => ($formData ['is_leased'] == true ? 1 : 0) 
                        );
                        
                        // loop through each instance and get instance specific
                        // data
                        $devices_pf_id = $formData ["devices_pf_id"];
                        $select = new Zend_Db_Select($db);
                        $select = $db->select()
                            ->from(array (
                                'udc' => 'proposalgenerator_upload_data_collector_rows' 
                        ))
                            ->where('udc.report_id = ' . $report_id . ' AND udc.devices_pf_id = ?', $devices_pf_id, 'INTEGER');
                        $stmt = $db->query($select);
                        $result = $stmt->fetchAll();
                        
                        foreach ( $result as $key => $value )
                        {
                            $upload_data_collector_id = $result [$key] ["upload_data_collector_id"];
                            
                            // check for unknown device
                            $select = new Zend_Db_Select($db);
                            $select = $db->select()
                                ->from(array (
                                    'udi' => 'unknown_device_instance' 
                            ))
                                ->where('udi.report_id = ' . $report_id . ' AND udi.upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                            $stmt = $db->query($select);
                            $unknown_device_instance = $stmt->fetchAll();
                            
                            if (count($unknown_device_instance) > 0)
                            {
                                $unknown_device_instance_id = $unknown_device_instance [0] ['unknown_device_instance_id'];
                            }
                            else
                            {
                                $unknown_device_instance_id = 0;
                            }
                            
                            // get jit support
                            $is_color = $result [$key] ['is_color'];
                            $tonerLevels = array ();
                            if ($is_color == 0)
                            {
                                $tonerLevels = array (
                                        'toner_level_black' => $result [$key] ['tonerlevel_black'] 
                                );
                            }
                            else
                            {
                                $tonerLevels = array (
                                        'toner_level_black' => $result [$key] ['tonerlevel_black'], 
                                        'toner_level_cyan' => $result [$key] ['tonerlevel_cyan'], 
                                        'toner_level_magenta' => $result [$key] ['tonerlevel_magenta'], 
                                        'toner_level_yellow' => $result [$key] ['tonerlevel_yellow'] 
                                );
                            }
                            $jit_supplies_supported = $this->determineJITSupport($is_color, $tonerLevels);
                            
                            // get instance specific data
                            $start_date = $result [$key] ["startdate"];
                            $end_date = $result [$key] ["enddate"];
                            $discovery_date = $result [$key] ["discovery_date"];
                            $install_date = null;
                            $date_created = $date;
                            
                            $startmeterlife = $result [$key] ["startmeterlife"];
                            $endmeterlife = $result [$key] ["endmeterlife"];
                            $start_meter_black = $result [$key] ["startmeterblack"];
                            $end_meter_black = $result [$key] ["endmeterblack"];
                            $start_meter_color = $result [$key] ["startmetercolor"];
                            $end_meter_color = $result [$key] ["endmetercolor"];
                            $start_meter_printblack = $result [$key] ["startmeterprintblack"];
                            $end_meter_printblack = $result [$key] ["endmeterprintblack"];
                            $start_meter_printcolor = $result [$key] ["startmeterprintcolor"];
                            $end_meter_printcolor = $result [$key] ["endmeterprintcolor"];
                            $start_meter_copyblack = $result [$key] ["startmetercopyblack"];
                            $end_meter_copyblack = $result [$key] ["endmetercopyblack"];
                            $start_meter_copycolor = $result [$key] ["startmetercopycolor"];
                            $end_meter_copycolor = $result [$key] ["endmetercopycolor"];
                            $start_meter_fax = $result [$key] ["startmeterfax"];
                            $end_meter_fax = $result [$key] ["endmeterfax"];
                            $start_meter_scan = $result [$key] ["startmeterscan"];
                            $end_meter_scan = $result [$key] ["endmeterscan"];
                            
                            $unknown_device_instanceData ['upload_data_collector_id'] = $upload_data_collector_id;
                            $unknown_device_instanceData ['printermodelid'] = ucwords(trim($result [$key] ["printermodelid"]));
                            $unknown_device_instanceData ['mps_monitor_startdate'] = $start_date;
                            $unknown_device_instanceData ['mps_monitor_enddate'] = $end_date;
                            $unknown_device_instanceData ['mps_discovery_date'] = $discovery_date;
                            $unknown_device_instanceData ['install_date'] = $install_date;
                            $unknown_device_instanceData ['printer_serial_number'] = ucwords(trim($result [$key] ["serialnumber"]));
                            $unknown_device_instanceData ['date_created'] = $date_created;
                            $unknown_device_instanceData ['start_meter_life'] = $startmeterlife;
                            $unknown_device_instanceData ['end_meter_life'] = $endmeterlife;
                            $unknown_device_instanceData ['start_meter_black'] = $start_meter_black;
                            $unknown_device_instanceData ['end_meter_black'] = $end_meter_black;
                            $unknown_device_instanceData ['start_meter_color'] = $start_meter_color;
                            $unknown_device_instanceData ['end_meter_color'] = $end_meter_color;
                            $unknown_device_instanceData ['start_meter_printblack'] = $start_meter_printblack;
                            $unknown_device_instanceData ['end_meter_printblack'] = $end_meter_printblack;
                            $unknown_device_instanceData ['start_meter_printcolor'] = $start_meter_printcolor;
                            $unknown_device_instanceData ['end_meter_printcolor'] = $end_meter_printcolor;
                            $unknown_device_instanceData ['start_meter_copyblack'] = $start_meter_copyblack;
                            $unknown_device_instanceData ['end_meter_copyblack'] = $end_meter_copyblack;
                            $unknown_device_instanceData ['start_meter_copycolor'] = $start_meter_copycolor;
                            $unknown_device_instanceData ['end_meter_copycolor'] = $end_meter_copycolor;
                            $unknown_device_instanceData ['start_meter_fax'] = $start_meter_fax;
                            $unknown_device_instanceData ['end_meter_fax'] = $end_meter_fax;
                            $unknown_device_instanceData ['start_meter_scan'] = $start_meter_scan;
                            $unknown_device_instanceData ['end_meter_scan'] = $end_meter_scan;
                            $unknown_device_instanceData ['jit_supplies_supported'] = $jit_supplies_supported;
                            $unknown_device_instanceData ['ip_address'] = ucwords(trim($result [$key] ["ipaddress"]));
                            
                            // save instances
                            if ($unknown_device_instance_id > 0)
                            {
                                $where = $unknown_device_instanceTable->getAdapter()->quoteInto('id = ' . $report_id . ' AND unknown_device_instance_id = ?', $unknown_device_instance_id, 'INTEGER');
                                $unknown_device_instanceTable->update($unknown_device_instanceData, $where);
                            }
                            else
                            {
                                $unknown_device_instanceData ['user_id'] = $this->user_id;
                                $unknown_device_instanceData ['report_id'] = $report_id;
                                
                                $unknown_device_instance_id = $unknown_device_instanceTable->insert($unknown_device_instanceData);
                            }
                            
                            // check for device_instance (match on
                            // upload_data_collector_id)
                            $device_instance = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->fetchRow('upload_data_collector_id = ' . $upload_data_collector_id);
                            if ($device_instance)
                            {
                                $device_instance_id = $device_instance->getDeviceInstanceId();
                                
                                // delete meters
                                $meter = Proposalgen_Model_Mapper_Meter::getInstance()->delete('device_instance_id = ' . $device_instance_id);
                                
                                // delete device_instance
                                Proposalgen_Model_Mapper_DeviceInstance::getInstance()->delete('device_instance_id = ' . $device_instance_id);
                            }
                            
                            // update uploaded record as not excluded
                            $upload_data_collectorData = array (
                                    'upload_data_collector_id' => $upload_data_collector_id, 
                                    'is_excluded' => 0 
                            );
                            $udcUpdateArray [] = $upload_data_collectorData;
                        }
                        $ucdMsg = Proposalgen_Model_Mapper_UploadDataCollectorRow::getInstance()->saveRows($udcUpdateArray);
                        
                        // check for ticket for user/device_pf_id
                        $ticket_pf_requestTable = new Proposalgen_Model_DbTable_TicketPFRequests();
                        $where = $ticket_pf_requestTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND devices_pf_id = ?', $devices_pf_id, 'INTEGER');
                        $ticket_pf_request = $ticket_pf_requestTable->fetchRow($where);
                        
                        if (count($ticket_pf_request) > 0)
                        {
                            // get ticket id
                            $ticket_id = $ticket_pf_request ['ticket_id'];
                            
                            // get form data
                            $device_manufacturer = ucwords(trim($formData ["device_manufacturer"]));
                            $printer_model = ucwords(trim($formData ["printer_model"]));
                            $launch_date = $launch_date->toString('yyyy/mm/dd HH:ss');
                            $device_price = ($formData ['device_price'] > 0 ? $formData ['device_price'] : null);
                            $toner_config = $formData ["toner_config"];
                            $is_copier = ($formData ['is_copier'] == true ? 1 : 0);
                            $is_fax = ($formData ['is_fax'] == true ? 1 : 0);
                            $is_scanner = ($formData ['is_scanner'] == true ? 1 : 0);
                            $is_duplex = ($formData ['is_duplex'] == true ? 1 : 0);
                            $ppm_black = ($formData ['ppm_black'] > 0 ? $formData ['ppm_black'] : null);
                            $ppm_color = ($formData ['ppm_color'] > 0 ? $formData ['ppm_color'] : null);
                            $duty_cycle = ($formData ['duty_cycle'] > 0 ? $formData ['duty_cycle'] : null);
                            $watts_power_normal = ($formData ['watts_power_normal'] > 0 ? $formData ['watts_power_normal'] : null);
                            $watts_power_idle = ($formData ['watts_power_idle'] > 0 ? $formData ['watts_power_idle'] : null);
                            
                            // update ticket request data
                            $ticket_pf_requestData = array (
                                    'device_manufacturer' => $device_manufacturer, 
                                    'printer_model' => $printer_model, 
                                    'launch_date' => $launch_date, 
                                    'device_price' => $device_price, 
                                    'toner_config' => $toner_config, 
                                    'is_copier' => $is_copier, 
                                    'is_fax' => $is_fax, 
                                    'is_duplex' => $is_duplex, 
                                    'is_scanner' => $is_scanner, 
                                    'PPM_black' => $PPM_black, 
                                    'PPM_color' => $PPM_color, 
                                    'duty_cycle' => $duty_cycle, 
                                    'watts_power_normal' => $watts_power_normal, 
                                    'watts_power_idle' => $watts_power_idle 
                            );
                            $where = $ticket_pf_requestTable->getAdapter()->quoteInto('ticket_id = ?', $ticket_id, 'INTEGER');
                            $ticket_pf_requestTable->update($ticket_pf_requestData, $where);
                        }
                        
                        // commit changes
                        $db->commit();
                        
                        // redirect back to mapping page
                        $this->_redirect('data/devicemapping?grid=' . $grid);
                    }
                    catch ( Exception $e )
                    {
                        $db->rollback();
                        $this->view->message = "There was an error saving your unknown device.";
                        // echo $e;
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
            

            if ($upload_data_collector_id > 0)
            {
                // set default
                $toner_config = 1;
                $allow_color_configs = true;
                
                // check to see if unknown device exists already and use it's
                // data
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        'udi' => 'unknown_device_instance' 
                ))
                    ->joinLeft(array (
                        'udc' => 'proposalgenerator_upload_data_collector_rows' 
                ), 'udc.upload_data_collector_id = udi.upload_data_collector_id', array (
                        'devices_pf_id', 
                        'is_color' 
                ))
                    ->where('udi.report_id = ' . $report_id . ' AND udi.upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                $stmt = $db->query($select);
                $result = $stmt->fetchAll();
                
                // if no unknown device, then use uploaded data
                if (count($result) > 0)
                {
                    $printermodelid = $result [0] ['printermodelid'];
                    
                    // get values
                    $devices_pf_id = $result [0] ['devices_pf_id'];
                    $manufacturername = strtolower($result [0] ['device_manufacturer']);
                    $devicename = strtolower($result [0] ['printer_model']);
                    $tempDate = $result [0] ['launch_date'];
                    $ppm_black = $result [0] ['PPM_black'];
                    $ppm_color = $result [0] ['PPM_color'];
                    $wattspowernormal = $result [0] ['watts_power_normal'];
                    $wattspoweridle = $result [0] ['watts_power_idle'];
                    $device_price = $result [0] ['device_price'];
                    $duty_cycle = $result [0] ['duty_cycle'];
                    $black_prodcodeoem = $result [0] ['black_toner_SKU'];
                    $black_yield = $result [0] ['black_toner_yield'];
                    $black_prodcostoem = $result [0] ['black_toner_price'];
                    $yellow_prodcodeoem = $result [0] ['yellow_toner_SKU'];
                    $yellow_yield = $result [0] ['yellow_toner_yield'];
                    $yellow_prodcostoem = $result [0] ['yellow_toner_price'];
                    $cyan_prodcodeoem = $result [0] ['cyan_toner_SKU'];
                    $cyan_yield = $result [0] ['cyan_toner_yield'];
                    $cyan_prodcostoem = $result [0] ['cyan_toner_price'];
                    $magenta_prodcodeoem = $result [0] ['magenta_toner_SKU'];
                    $magenta_yield = $result [0] ['magenta_toner_yield'];
                    $magenta_prodcostoem = $result [0] ['magenta_toner_price'];
                    $tcolor_prodcodeoem = $result [0] ['3color_toner_SKU'];
                    $tcolor_yield = $result [0] ['3color_toner_yield'];
                    $tcolor_prodcostoem = $result [0] ['3color_toner_price'];
                    $fcolor_prodcodeoem = $result [0] ['4color_toner_SKU'];
                    $fcolor_yield = $result [0] ['4color_toner_yield'];
                    $fcolor_prodcostoem = $result [0] ['4color_toner_price'];
                    
                    $blackcomp_prodcodeoem = $result [0] ['black_comp_SKU'];
                    $blackcomp_yield = $result [0] ['black_comp_yield'];
                    $blackcomp_prodcostoem = $result [0] ['black_comp_price'];
                    $yellowcomp_prodcodeoem = $result [0] ['yellow_comp_SKU'];
                    $yellowcomp_yield = $result [0] ['yellow_comp_yield'];
                    $yellowcomp_prodcostoem = $result [0] ['yellow_comp_price'];
                    $cyancomp_prodcodeoem = $result [0] ['cyan_comp_SKU'];
                    $cyancomp_yield = $result [0] ['cyan_comp_yield'];
                    $cyancomp_prodcostoem = $result [0] ['cyan_comp_price'];
                    $magentacomp_prodcodeoem = $result [0] ['magenta_comp_SKU'];
                    $magentacomp_yield = $result [0] ['magenta_comp_yield'];
                    $magentacomp_prodcostoem = $result [0] ['magenta_comp_price'];
                    $tcolorcomp_prodcodeoem = $result [0] ['3color_comp_SKU'];
                    $tcolorcomp_yield = $result [0] ['3color_comp_yield'];
                    $tcolorcomp_prodcostoem = $result [0] ['3color_comp_price'];
                    $fcolorcomp_prodcodeoem = $result [0] ['4color_comp_SKU'];
                    $fcolorcomp_yield = $result [0] ['4color_comp_yield'];
                    $fcolorcomp_prodcostoem = $result [0] ['4color_comp_price'];
                    
                    $toner_config = $result [0] ['toner_config_id'];
                    $is_copier = $result [0] ['is_copier'];
                    $is_scanner = $result [0] ['is_scanner'];
                    $is_fax = $result [0] ['is_fax'];
                    $is_duplex = $result [0] ['is_duplex'];
                    $is_leased = $result [0] ['is_leased'];
                    
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
                        ->from(array (
                            'udc' => 'proposalgenerator_upload_data_collector_rows' 
                    ))
                        ->where('id = ' . $report_id . ' AND upload_data_collector_id = ?', $upload_data_collector_id, 'INTEGER');
                    $stmt = $db->query($select);
                    $result = $stmt->fetchAll();
                    
                    if (count($result) > 0)
                    {
                        $printermodelid = $result [0] ['printermodelid'];
                        
                        // get values
                        $devices_pf_id = $result [0] ['devices_pf_id'];
                        $manufacturername = strtolower($result [0] ['manufacturer']);
                        $devicename = strtolower($result [0] ['modelname']);
                        $tempDate = $result [0] ['date_introduction'];
                        $ppm_black = $result [0] ['ppm_black'];
                        $ppm_color = $result [0] ['ppm_color'];
                        $wattspowernormal = $result [0] ['wattspowernormal'];
                        $wattspoweridle = $result [0] ['wattspoweridle'];
                        $device_price = '';
                        $duty_cycle = $result [0] ['duty_cycle'];
                        
                        $black_prodcodeoem = $result [0] ['black_prodcodeoem'];
                        $black_yield = $result [0] ['black_yield'];
                        $black_prodcostoem = $result [0] ['black_prodcostoem'];
                        $yellow_prodcodeoem = $result [0] ['yellow_prodcodeoem'];
                        $yellow_yield = $result [0] ['yellow_yield'];
                        $yellow_prodcostoem = $result [0] ['yellow_prodcostoem'];
                        $cyan_prodcodeoem = $result [0] ['cyan_prodcodeoem'];
                        $cyan_yield = $result [0] ['cyan_yield'];
                        $cyan_prodcostoem = $result [0] ['cyan_prodcostoem'];
                        $magenta_prodcodeoem = $result [0] ['magenta_prodcodeoem'];
                        $magenta_yield = $result [0] ['magenta_yield'];
                        $magenta_prodcostoem = $result [0] ['magenta_prodcostoem'];
                        $tcolor_prodcodeoem = '';
                        $tcolor_yield = '';
                        $tcolor_prodcostoem = '';
                        $fcolor_prodcodeoem = '';
                        $fcolor_yield = '';
                        $fcolor_prodcostoem = '';
                        
                        $blackcomp_prodcodeoem = '';
                        $blackcomp_yield = '';
                        $blackcomp_prodcostoem = '';
                        $yellowcomp_prodcodeoem = '';
                        $yellowcomp_yield = '';
                        $yellowcomp_prodcostoem = '';
                        $cyancomp_prodcodeoem = '';
                        $cyancomp_yield = '';
                        $cyancomp_prodcostoem = '';
                        $magentacomp_prodcodeoem = '';
                        $magentacomp_yield = '';
                        $magentacomp_prodcostoem = '';
                        $tcolorcomp_prodcodeoem = '';
                        $tcolorcomp_yield = '';
                        $tcolorcomp_prodcostoem = '';
                        $fcolorcomp_prodcodeoem = '';
                        $fcolorcomp_yield = '';
                        $fcolorcomp_prodcostoem = '';
                        
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
                        
                        $is_copier = $result [0] ['is_copier'];
                        $is_scanner = $result [0] ['is_scanner'];
                        $is_fax = $result [0] ['is_fax'];
                        $is_duplex = 0;
                        $is_leased = 0;
                    }
                    else
                    {
                        $this->view->message = "Device Not Found.";
                    }
                }
                
                // populate form with values
                $this->view->devices_pf_id = $devices_pf_id;
                $this->view->printer_model_id = $printermodelid;
                $this->view->upload_data_collector_id = $upload_data_collector_id;
                
                if (isset($formData ['hdnGrid']))
                {
                    $this->view->grid = $formData ['hdnGrid'];
                    $form->getElement('grid')->setValue($formData ['hdnGrid']);
                }
                $form->getElement('upload_data_collector_id')->setValue($upload_data_collector_id);
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
                $form->getElement('device_price')->setValue($device_price);
                
                // toners
                $form->getElement('black_toner_SKU')->setValue($black_prodcodeoem);
                $form->getElement('black_toner_yield')->setValue($black_yield);
                $form->getElement('black_toner_price')->setValue(($black_prodcostoem > 0 ? $black_prodcostoem : null));
                $form->getElement('yellow_toner_SKU')->setValue($yellow_prodcodeoem);
                $form->getElement('yellow_toner_yield')->setValue($yellow_yield);
                $form->getElement('yellow_toner_price')->setValue(($yellow_prodcostoem > 0 ? $yellow_prodcostoem : null));
                $form->getElement('cyan_toner_SKU')->setValue($cyan_prodcodeoem);
                $form->getElement('cyan_toner_yield')->setValue($cyan_yield);
                $form->getElement('cyan_toner_price')->setValue(($cyan_prodcostoem > 0 ? $cyan_prodcostoem : null));
                $form->getElement('magenta_toner_SKU')->setValue($magenta_prodcodeoem);
                $form->getElement('magenta_toner_yield')->setValue($magenta_yield);
                $form->getElement('magenta_toner_price')->setValue(($magenta_prodcostoem > 0 ? $magenta_prodcostoem : null));
                
                // comp toners
                $form->getElement('black_comp_SKU')->setValue($blackcomp_prodcodeoem);
                $form->getElement('black_comp_yield')->setValue($blackcomp_yield);
                $form->getElement('black_comp_price')->setValue(($blackcomp_prodcostoem > 0 ? $blackcomp_prodcostoem : null));
                $form->getElement('yellow_comp_SKU')->setValue($yellowcomp_prodcodeoem);
                $form->getElement('yellow_comp_yield')->setValue($yellowcomp_yield);
                $form->getElement('yellow_comp_price')->setValue(($yellowcomp_prodcostoem > 0 ? $yellowcomp_prodcostoem : null));
                $form->getElement('cyan_comp_SKU')->setValue($cyancomp_prodcodeoem);
                $form->getElement('cyan_comp_yield')->setValue($cyancomp_yield);
                $form->getElement('cyan_comp_price')->setValue(($cyancomp_prodcostoem > 0 ? $cyancomp_prodcostoem : null));
                $form->getElement('magenta_comp_SKU')->setValue($magentacomp_prodcodeoem);
                $form->getElement('magenta_comp_yield')->setValue($magentacomp_yield);
                $form->getElement('magenta_comp_price')->setValue(($magentacomp_prodcostoem > 0 ? $magentacomp_prodcostoem : null));
                
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
                $form->getElement('3color_toner_SKU')->setValue($tcolor_prodcodeoem);
                $form->getElement('3color_toner_yield')->setValue($tcolor_yield);
                $form->getElement('3color_toner_price')->setValue(($tcolor_prodcostoem > 0 ? $tcolor_prodcostoem : null));
                $form->getElement('4color_toner_SKU')->setValue($fcolor_prodcodeoem);
                $form->getElement('4color_toner_yield')->setValue($fcolor_yield);
                $form->getElement('4color_toner_price')->setValue(($fcolor_prodcostoem > 0 ? $fcolor_prodcostoem : null));
                
                // comp toners not included in import
                $form->getElement('3color_comp_SKU')->setValue($tcolorcomp_prodcodeoem);
                $form->getElement('3color_comp_yield')->setValue($tcolorcomp_yield);
                $form->getElement('3color_comp_price')->setValue(($tcolorcomp_prodcostoem > 0 ? $tcolorcomp_prodcostoem : null));
                $form->getElement('4color_comp_SKU')->setValue($fcolorcomp_prodcodeoem);
                $form->getElement('4color_comp_yield')->setValue($fcolorcomp_yield);
                $form->getElement('4color_comp_price')->setValue(($fcolorcomp_prodcostoem > 0 ? $fcolorcomp_prodcostoem : null));
                
                $form->getElement('is_leased')->setAttrib('checked', ($is_leased == "FALSE" ? 0 : 1));
                
                // if user has previously requested support for this device,
                // disable checkbox
                $tickets_pf_requestsTable = new Proposalgen_Model_DbTable_TicketPFRequests();
                $where = $tickets_pf_requestsTable->getAdapter()->quoteInto('user_id = ' . $this->user_id . ' AND devices_pf_id = ?', $devices_pf_id, 'INTEGER');
                $ticket_pf_request = $tickets_pf_requestsTable->fetchRow($where);
                
                if (count($ticket_pf_request) > 0)
                {
                    $ticket_id = $ticket_pf_request ['ticket_id'];
                    
                    if ($ticket_id > 0)
                    {
                        // allow ticket description to be edited
                        $this->view->edit_description = true;
                        
                        // load details for ticket
                        $ticketsMapper = Proposalgen_Model_Mapper_Ticket::getInstance();
                        $tickets = $ticketsMapper->find($ticket_id);
                        $this->view->ticket_number = $tickets->TicketId;
                        $this->view->ticket_title = $tickets->Title;
                        $this->view->reported_by = $tickets->User->UserName;
                        $this->view->ticket_type = $tickets->Category->CategoryName;
                        $this->view->ticket_details = $tickets->Description;
                        $this->view->ticket_status = ucwords(strtolower($tickets->Status->StatusName));
                        $this->view->ticket_status_id = ucwords(strtolower($tickets->Status->StatusId));
                        
                        // get comment history
                        $ticket_comments_array = array ();
                        $ticket_commentsMapper = Proposalgen_Model_Mapper_TicketComment::getInstance();
                        $ticket_comments = $ticket_commentsMapper->fetchAll(array (
                                'ticket_id = ?' => $ticket_id 
                        ));
                        
                        foreach ( $ticket_comments as $row )
                        {
                            $comment_date = new Zend_Date($row->CommentDate, "yyyy-mm-dd HH:ii:ss");
                            $ticket_comments_array [] = array (
                                    'username' => $row->User->UserName, 
                                    'comment_date' => $comment_date->toString('mm/dd/yyyy'), 
                                    'comment_text' => $row->CommentText 
                            );
                        }
                        $this->view->ticket_comments = $ticket_comments_array;
                        
                        // find pf_device
                        $ticketpfrequestMapper = Proposalgen_Model_Mapper_TicketPFRequest::getInstance();
                        $ticketpfrequest = $ticketpfrequestMapper->find($ticket_id);
                        $this->view->devices_pf_id = $ticketpfrequest->devicePfId;
                        $this->view->device_pf_name = $ticketpfrequest->_devicePf->PfDbManufacturer . ' ' . $ticketpfrequest->_devicePf->PfDbDeviceName;
                        $this->view->user_suggested_name = $ticketpfrequest->deviceManufacturer . ' ' . $ticketpfrequest->printerModel;
                        
                        // ticket exists, update ticket label
                        $form->getElement('request_support')->setLabel("View Support Ticket");
                    }
                }
                
                $mapped_to_id = 0;
                $mapped_to_modelname = '';
                $mapped_to_manufacturer = '';
                // loop through pf_device_matchup_users to find suggested
                // mapping
                $select = new Zend_Db_Select($db);
                $select = $db->select()
                    ->from(array (
                        'pfdmu' => 'pf_device_matchup_users' 
                ), array (
                        'devices_pf_id', 
                        'master_device_id', 
                        'user_id' 
                ))
                    ->joinLeft(array (
                        'md' => 'master_device' 
                ), 'md.master_device_id = pfdmu.master_device_id', array (
                        'printer_model' 
                ))
                    ->joinLeft(array (
                        'm' => 'manufacturer' 
                ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                        'manufacturer_name' 
                ))
                    ->where('pfdmu.devices_pf_id = ' . $devices_pf_id . ' AND pfdmu.user_id = ' . $this->user_id);
                $stmt = $db->query($select);
                $master_devices = $stmt->fetchAll();
                
                if (count($master_devices) > 0)
                {
                    $mapped_to_id = $master_devices [0] ['master_device_id'];
                    $mapped_to_modelname = $master_devices [0] ['printer_model'];
                    $mapped_to_manufacturer = $master_devices [0] ['manufacturer_name'];
                }
                else
                {
                    // loop through master_matchup_pf to find master mapping
                    $select = new Zend_Db_Select($db);
                    $select = $db->select()
                        ->from(array (
                            'mmpf' => 'master_matchup_pf' 
                    ), array (
                            'devices_pf_id', 
                            'master_device_id' 
                    ))
                        ->joinLeft(array (
                            'md' => 'master_device' 
                    ), 'md.master_device_id = mmpf.master_device_id', array (
                            'printer_model' 
                    ))
                        ->joinLeft(array (
                            'm' => 'manufacturer' 
                    ), 'm.manufacturer_id = md.mastdevice_manufacturer', array (
                            'manufacturer_name' 
                    ))
                        ->where('mmpf.devices_pf_id = ' . $devices_pf_id);
                    $stmt = $db->query($select);
                    $master_devices = $stmt->fetchAll();
                    
                    if (count($master_devices) > 0)
                    {
                        $mapped_to_id = $master_devices [0] ['master_device_id'];
                        $mapped_to_modelname = $master_devices [0] ['printer_model'];
                        $mapped_to_manufacturer = $master_devices [0] ['manufacturer_name'];
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

    public function cancelimportAction ()
    {
        // remove any existing data for report
        $this->delete_data();
        
        // redirect to upload page
        $this->_redirect('/survey/verify');
    }

    public function delete_data ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        $db->beginTransaction();
        if ($report_id > 0)
        {
            try
            {
                // delete any meters, device_instances, unknown_device_instances
                // and requests for report
                $metersTable = new Proposalgen_Model_DbTable_Meters();
                $device_instanceTable = new Proposalgen_Model_DbTable_DeviceInstance();
                
                $where = $device_instanceTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
                $device_instances = $device_instanceTable->fetchAll($where);
                foreach ( $device_instances as $key => $value )
                {
                    $where = $metersTable->getAdapter()->quoteInto('device_instance_id = ?', $device_instances [$key] ['device_instance_id'], 'INTEGER');
                    $metersTable->delete($where);
                    
                    $where = $device_instanceTable->getAdapter()->quoteInto('device_instance_id = ?', $device_instances [$key] ['device_instance_id'], 'INTEGER');
                    $device_instanceTable->delete('device_instance_id = ' . $device_instances [$key] ['device_instance_id']);
                }
                
                // delete unknown_device_instances for report_id
                $unknown_device_instanceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
                $where = $unknown_device_instanceTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
                $unknown_device_instanceTable->delete($where);
                
                // delete any upload data
                $upload_data_collectorTable = new Proposalgen_Model_DbTable_UploadDataCollector();
                $where = $upload_data_collectorTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
                $upload_data_collectorTable->delete($where);
                
                // reset report back to upload
                $reportTable = new Proposalgen_Model_DbTable_Report();
                $reportData = array (
                        'report_stage' => 'upload' 
                );
                $where = $reportTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
                $report = $reportTable->fetchRow($where);
                if ($report ['report_stage'] != 'finished')
                {
                    $reportTable->update($reportData, $where);
                }
                
                $this->view->message = "The devices for the current report have been removed.";
                $db->commit();
            }
            catch ( Exception $e )
            {
                $db->rollBack();
                $this->view->message = "An Error occured and the devices for report " . $report_id . " were not removed.";
                throw new Exception("An Error occured and the devices for report " . $report_id . " were not removed.", 0, $e);
            }
        }
    }
    
    // function checks all the meters to make sure than the startmeter is not
    // greater than the endmeter
    public function checkMeters ($ctr, $finalDevices)
    {
        // if end meter black is empty, but has startmeterlife and
        // startmetercolor then allow it
        if (empty($finalDevices [$ctr] ['endmeterblack']) && (empty($finalDevices [$ctr] ['startmeterlife']) || empty($finalDevices [$ctr] ['startmetercolor'])))
            return 0;
        if (($finalDevices [$ctr] ['startmeterblack'] > $finalDevices [$ctr] ['endmeterblack']) || ($finalDevices [$ctr] ['startmeterblack'] < 0 || $finalDevices [$ctr] ['endmeterblack'] < 0))
            return 0;
        if (($finalDevices [$ctr] ['startmeterlife'] > $finalDevices [$ctr] ['endmeterlife']) || ($finalDevices [$ctr] ['startmeterlife'] < 0 || $finalDevices [$ctr] ['endmeterlife'] < 0))
            return 0;
        if (($finalDevices [$ctr] ['startmetercolor'] > $finalDevices [$ctr] ['endmetercolor']) || ($finalDevices [$ctr] ['startmetercolor'] < 0 || $finalDevices [$ctr] ['endmetercolor'] < 0))
            return 0;
        if (($finalDevices [$ctr] ['startmeterprintblack'] > $finalDevices [$ctr] ['endmeterprintblack']) || ($finalDevices [$ctr] ['startmeterprintblack'] < 0 || $finalDevices [$ctr] ['endmeterprintblack'] < 0))
            return 0;
        if (($finalDevices [$ctr] ['startmeterprintcolor'] > $finalDevices [$ctr] ['endmeterprintcolor']) || ($finalDevices [$ctr] ['startmeterprintcolor'] < 0 || $finalDevices [$ctr] ['endmeterprintcolor'] < 0))
            return 0;
        if (($finalDevices [$ctr] ['startmeterprintblack'] > $finalDevices [$ctr] ['endmeterprintblack']) || ($finalDevices [$ctr] ['startmeterprintblack'] < 0 || $finalDevices [$ctr] ['endmeterprintblack'] < 0))
            return 0;
        if (($finalDevices [$ctr] ['startmetercopyblack'] > $finalDevices [$ctr] ['endmetercopyblack']) || ($finalDevices [$ctr] ['startmetercopyblack'] < 0 || $finalDevices [$ctr] ['endmetercopyblack'] < 0))
            return 0;
        if (($finalDevices [$ctr] ['startmetercopycolor'] > $finalDevices [$ctr] ['endmetercopycolor']) || ($finalDevices [$ctr] ['startmetercopycolor'] < 0 || $finalDevices [$ctr] ['endmetercopycolor'] < 0))
            return 0;
        if (($finalDevices [$ctr] ['startmeterscan'] > $finalDevices [$ctr] ['endmeterscan']) || ($finalDevices [$ctr] ['startmeterscan'] < 0 || $finalDevices [$ctr] ['endmeterscan'] < 0))
            return 0;
        if (($finalDevices [$ctr] ['startmeterfax'] > $finalDevices [$ctr] ['endmeterfax']) || ($finalDevices [$ctr] ['startmeterfax'] < 0 || $finalDevices [$ctr] ['endmeterfax'] < 0))
            return 0;
        
        return 1;
    }

    public function getAvgCostOfDevices ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $session = new Zend_Session_Namespace('report');
        $reportId = $session->report_id;
        $totalDeviceCost = 0;
        $userTable = new Proposalgen_Model_DbTable_Users();
        $dealerTable = new Proposalgen_Model_DbTable_DealerCompany();
        
        // getting the default prices
        $row = $userTable->fetchRow('user_id = ' . $this->user_id);
        $userDefaultPrice = $row ['user_default_printer_cost'];
        $row = $dealerTable->fetchRow('dealer_company_id = ' . $this->dealer_company_id);
        $dealerDefaultPrice = $row ['dc_default_printer_cost'];
        
        $row = $dealerTable->fetchRow('company_name = "MASTER"');
        $systemDefaultPrice = $row ['dc_default_printer_cost'];
        
        $dealerOverrideTable = new Proposalgen_Model_DbTable_DealerDeviceOverride();
        $userOverrideTable = new Proposalgen_Model_DbTable_UserDeviceOverride();
        $numOfDevice = $this->getTotalDevicesIncluded($reportId);
        $sql = "SELECT master_device.master_device_id, device_instance.master_device_id, master_device.device_price
				FROM device_instance, master_device
				WHERE device_instance.report_id = ?
				AND device_instance.is_excluded = 0
				AND device_instance.master_device_id = master_device.master_device_id";
        $stmt = $db->query($sql, array (
                $reportId 
        ));
        $results = $stmt->fetchAll();
        
        foreach ( $results as $device )
        {
            if ($userOverrideTable->fetchRow('user_id = ' . $this->user_id . ' AND master_device_id = ' . $device ['master_device_id']))
            {
                // applying the user ride price
                $userDeviceRow = $userOverrideTable->fetchRow('user_id = ' . $this->user_id . ' AND master_device_id = ' . $device ['master_device_id']);
                $totalDeviceCost += $userDeviceRow ['override_device_price'];
            }
            elseif ($dealerOverrideTable->fetchRow('dealer_company_id = ' . $this->dealer_company_id . ' AND master_device_id = ' . $device ['master_device_id']))
            {
                // if no user override price, apply dealer override price
                $dealerDeviceRow = $dealerOverrideTable->fetchRow('dealer_company_id = ' . $this->dealer_company_id . ' AND master_device_id = ' . $device ['master_device_id']);
                $totalDeviceCost += $dealerDeviceRow ['override_device_price'];
            }
            elseif ($device ['device_price'])
            {
                // if no dealer override price, apply master device price
                $totalDeviceCost += $device ['device_price'];
            }
            elseif ($userDefaultPrice)
            {
                // if no master device price, apply user default price
                $totalDeviceCost += $userDefaultPrice;
            }
            elseif ($dealerDefaultPrice)
            {
                // if no user default price, apply dealer default price
                $totalDeviceCost += $dealerDefaultPrice;
            }
            else
            {
                $totalDeviceCost += $systemDefaultPrice;
            }
        }
        
        $unknownDeviceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
        $where = $unknownDeviceTable->getAdapter()->quoteInto('is_excluded = 0 AND report_id = ?', $reportId, 'INTEGER');
        $unknownDevices = $unknownDeviceTable->fetchAll($where);
        foreach ( $unknownDevices as $device )
        {
            if ($device ['device_price'])
            {
                $totalDeviceCost += $device ['device_price'];
            }
            elseif ($userDefaultPrice)
            {
                $totalDeviceCost += $userDefaultPrice;
            }
            elseif ($dealerDefaultPrice)
            {
                $totalDeviceCost += $dealerDefaultPrice;
            }
            else
            {
                $totalDeviceCost += $systemDefaultPrice;
            }
        }
        
        if (! $numOfDevice)
        {
            if ($userDefaultPrice)
            {
                $totalDeviceCost = $userDefaultPrice;
            }
            elseif ($dealerDefaultPrice)
            {
                $totalDeviceCost = $dealerDefaultPrice;
            }
            else
            {
                $totalDeviceCost = $systemDefaultPrice;
            }
            
            // throw new exception("Error Creating report. Division by zero");
            return $totalDeviceCost;
        }
        else
        {
            return $totalDeviceCost / $numOfDevice;
        }
    }

    public function getTotalDevicesIncluded ($reportId)
    {
        $unknownDeviceTable = new Proposalgen_Model_DbTable_UnknownDeviceInstance();
        $deviceTable = new Proposalgen_Model_DbTable_DeviceInstance();
        
        $where = $deviceTable->getAdapter()->quoteInto("is_excluded = 0 AND report_id = ?", $reportId, 'INTEGER');
        $results = $deviceTable->fetchAll($where);
        $count = count($results);
        
        $where = $unknownDeviceTable->getAdapter()->quoteInto("is_excluded = 0 AND report_id = ?", $reportId, 'INTEGER');
        $results = $unknownDeviceTable->fetchAll($where);
        $count += count($results);
        
        return $count;
    }

    /**
     * This function gets the name of the company the report was prepared for
     */
    public function getReportCompanyName ()
    {
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        $questionTable = new Proposalgen_Model_DbTable_TextAnswer();
        $where = $questionTable->getAdapter()->quoteInto('question_id = 4 AND report_id = ?', $report_id, 'INTEGER');
        $row = $questionTable->fetchRow($where);
        if ($row ['textual_answer'])
        {
            return $row ['textual_answer'];
        }
        else
        {
            return null;
        }
    }

    function resizeImage ($image, $width, $height, $scale)
    {
        list ( $imagewidth, $imageheight, $imageType ) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType)
        {
            case "image/gif" :
                $source = imagecreatefromgif($image);
                break;
            case "image/pjpeg" :
            case "image/jpeg" :
            case "image/jpg" :
                $source = imagecreatefromjpeg($image);
                break;
            case "image/png" :
            case "image/x-png" :
                $source = imagecreatefrompng($image);
                break;
        }
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $width, $height);
        
        switch ($imageType)
        {
            case "image/gif" :
                imagegif($newImage, $image);
                break;
            case "image/pjpeg" :
            case "image/jpeg" :
            case "image/jpg" :
                imagejpeg($newImage, $image, 90);
                break;
            case "image/png" :
            case "image/x-png" :
                imagepng($newImage, $image);
                break;
        }
        
        chmod($image, 0777);
        return $image;
    }
    
    // You do not need to alter these functions
    function resizeThumbnailImage ($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale)
    {
        list ( $imagewidth, $imageheight, $imageType ) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);
        
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType)
        {
            case "image/gif" :
                $source = imagecreatefromgif($image);
                break;
            case "image/pjpeg" :
            case "image/jpeg" :
            case "image/jpg" :
                $source = imagecreatefromjpeg($image);
                break;
            case "image/png" :
            case "image/x-png" :
                $source = imagecreatefrompng($image);
                break;
        }
        imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
        switch ($imageType)
        {
            case "image/gif" :
                imagegif($newImage, $thumb_image_name);
                break;
            case "image/pjpeg" :
            case "image/jpeg" :
            case "image/jpg" :
                imagejpeg($newImage, $thumb_image_name, 90);
                break;
            case "image/png" :
            case "image/x-png" :
                imagepng($newImage, $thumb_image_name);
                break;
        }
        chmod($thumb_image_name, 0777);
        return $thumb_image_name;
    }
    
    // You do not need to alter these functions
    function getHeight ($image)
    {
        $size = getimagesize($image);
        $height = $size [1];
        return $height;
    }
    
    // You do not need to alter these functions
    function getWidth ($image)
    {
        $size = getimagesize($image);
        $width = $size [0];
        return $width;
    }

    public function showimageAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->disableLayout();
        $page = $this->_getParam('page', null);
        $size = $this->_getParam('size', null);
        $default = $this->_getParam('default', false);
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        if ($size == "thumb")
        {
            $field = "company_logo";
            $report_field = "company_image_override";
        }
        else
        {
            $field = "full_company_logo";
            $report_field = "full_company_image_override";
        }
        
        $db->begintransaction();
        try
        {
            // check report table first
            $reportTable = new Proposalgen_Model_DbTable_Report();
            $userTable = new Proposalgen_Model_DbTable_Users();
            $dealer_companyTable = new Proposalgen_Model_DbTable_DealerCompany();
            
            $where = $reportTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
            $report = $reportTable->fetchRow($where);
            
            if (count($report) > 0 && $page == "reportsettings")
            {
                $image = base64_decode($report [$report_field]);
                
                if ($default == true)
                {
                    $where = $userTable->getAdapter()->quoteInto("user_id = ?", $this->user_id, 'INTEGER');
                    $user = $userTable->fetchRow($where);
                    $image = base64_decode($user [$field]);
                    
                    if (count($user) > 0 && empty($user [$field]))
                    {
                        $where = $dealer_companyTable->getAdapter()->quoteInto("dealer_company_id = ?", $this->dealer_company_id, 'INTEGER');
                        $dealer_company = $dealer_companyTable->fetchRow($where);
                        $image = base64_decode($dealer_company [$field]);
                        
                        if (count($dealer_company) > 0 && empty($dealer_company [$field]))
                        {
                            $where = $dealer_companyTable->getAdapter()->quoteInto("company_name = 'MASTER'", null);
                            $dealer_company = $dealer_companyTable->fetchRow($where);
                            $image = base64_decode($dealer_company [$field]);
                        }
                    }
                }
            }
            else if (in_array("Standard User", $this->privilege) || $page == "managemysettings")
            {
                $where = $userTable->getAdapter()->quoteInto("user_id = ?", $this->user_id, 'INTEGER');
                $user = $userTable->fetchRow($where);
                $image = base64_decode($user [$field]);
                
                if (count($user) > 0 && empty($user [$field]) || $default == true)
                {
                    $where = $dealer_companyTable->getAdapter()->quoteInto("dealer_company_id = ?", $this->dealer_company_id, 'INTEGER');
                    $dealer_company = $dealer_companyTable->fetchRow($where);
                    $image = base64_decode($dealer_company [$field]);
                    
                    if (count($dealer_company) > 0 && empty($dealer_company [$field]))
                    {
                        $where = $dealer_companyTable->getAdapter()->quoteInto("company_name = 'MASTER'", null);
                        $dealer_company = $dealer_companyTable->fetchRow($where);
                        $image = base64_decode($dealer_company [$field]);
                    }
                }
            }
            else if (in_array("Dealer Admin", $this->privilege))
            {
                $where = $dealer_companyTable->getAdapter()->quoteInto("dealer_company_id = ?", $this->dealer_company_id, 'INTEGER');
                $dealer_company = $dealer_companyTable->fetchRow($where);
                $image = base64_decode($dealer_company [$field]);
                
                if ((count($dealer_company) > 0 && empty($dealer_company [$field])) || $default == true)
                {
                    $where = $dealer_companyTable->getAdapter()->quoteInto("company_name = 'MASTER'", null);
                    $dealer_company = $dealer_companyTable->fetchRow($where);
                    $image = base64_decode($dealer_company [$field]);
                }
            }
            else if (in_array("System Admin", $this->privilege))
            {
                $where = $dealer_companyTable->getAdapter()->quoteInto("company_name = 'MASTER'", null);
                $dealer_company = $dealer_companyTable->fetchRow($where);
                $image = base64_decode($dealer_company [$field]);
            }
            
            $this->view->data = $image;
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
            echo $e;
        }
    }

    public function removeimageAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->disableLayout();
        $page = $this->_getParam('page', null);
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        $db->begintransaction();
        try
        {
            $upload_dir = $this->config->app->uploadPath; // The directory for
            // the images to be
            // saved in
            $upload_path = $upload_dir . "/"; // The path to where the image
            // will be saved
            $large_image_prefix = "resize_"; // The prefix name to large image
            $thumb_image_prefix = "thumbnail_"; // The prefix name to the thumb
            // image
            $large_image_name = $large_image_prefix . $_SESSION ['random_key']; // New
            // name
            // of
            // the
            // large
            // image
            // (append
            // the
            // timestamp
            // to
            // the
            // filename)
            $thumb_image_name = $thumb_image_prefix . $_SESSION ['random_key']; // New
            // name
            // of
            // the
            // thumbnail
            // image
            // (append
            // the
            // timestamp
            // to
            // the
            // filename)
            $large_image_location = $upload_path . $large_image_name . $_SESSION ['user_file_ext'];
            $thumb_image_location = $upload_path . $thumb_image_name . $_SESSION ['user_file_ext'];
            
            $data ["full_company_image_override"] = null;
            $data ["company_image_override"] = null;
            
            // table is dealer_company table
            $table = new Proposalgen_Model_DbTable_Report();
            $where = $table->getAdapter()->quoteInto("id = ?", $report_id, 'INTEGER');
            $table->update($data, $where);
            $this->view->data = "<p>The image has been removed and the default image is being used.</p>";
            
            // Delete the physical files from the server
            if (file_exists($large_image_location))
            {
                unlink($large_image_location);
            }
            if (file_exists($thumb_image_location))
            {
                unlink($thumb_image_location);
            }
            
            $db->commit();
        }
        catch ( Exception $e )
        {
            $db->rollback();
        }
    }

    public function init_upload_settings ()
    {
        $this->max_file = "1"; // Maximum file size in MB
        $this->max_width = "800"; // Max width allowed for the large image
        $this->max_height = "400"; // Max height allowed for the large image
        $this->thumb_width = "375"; // Width of thumbnail image
        $this->thumb_height = "150"; // Height of thumbnail image
        $this->current_large_image_width = null;
        $this->current_large_image_height = null;
        
        // only assign a new timestamp if the session variable is empty
        if (! isset($_SESSION ['random_key']) || strlen($_SESSION ['random_key']) == 0)
        {
            $_SESSION ['random_key'] = strtotime(date('Y-m-d H:i:s')); // assign
            // the
            // timestamp
            // to the
            // session
            // variable
            $_SESSION ['user_file_ext'] = "";
        }
        
        $this->upload_dir = $this->config->app->uploadPath; // The directory for
        // the images to be
        // saved in
        $this->upload_path = $this->upload_dir . "/"; // The path to where the
        // image will be saved
        $large_image_prefix = "resize_"; // The prefix name to large image
        $thumb_image_prefix = "thumbnail_"; // The prefix name to the thumb
        // image
        $this->large_image_name = $large_image_prefix . $_SESSION ['random_key']; // New
        // name
        // of
        // the
        // large
        // image
        // (append
        // the
        // timestamp
        // to
        // the
        // filename)
        $this->thumb_image_name = $thumb_image_prefix . $_SESSION ['random_key']; // New
        // name
        // of
        // the
        // thumbnail
        // image
        // (append
        // the
        // timestamp
        // to
        // the
        // filename)
        

        // Only one of these image types should be allowed for upload
        // $allowed_image_types
        // =
        // array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
        $this->allowed_image_types = array (
                'image/pjpeg' => "jpg", 
                'image/jpeg' => "jpg", 
                'image/jpg' => "jpg" 
        );
        $allowed_image_ext = array_unique($this->allowed_image_types); // do not
        // change
        // this
        $image_ext = ""; // initialise variable, do not change this.
        foreach ( $allowed_image_ext as $mime_type => $ext )
        {
            $this->image_ext .= strtoupper($ext) . " ";
        }
        
        // using a session for scalability in the future... in case we decide to
        // try to allow .gif or .png files
        // this may require another field for the file extension to be added to
        // the database
        if (empty($_SESSION ['user_file_ext']))
        {
            $_SESSION ['user_file_ext'] = '.jpg';
        }
        
        // Image Locations
        $this->large_image_location = $this->upload_path . $this->large_image_name . $_SESSION ['user_file_ext'];
        $this->thumb_image_location = $this->upload_path . $this->thumb_image_name . $_SESSION ['user_file_ext'];
    }

    public function rebuild_logos ($level)
    {
        // update fields being used
        $field = "company_logo";
        $full_field = "full_company_logo";
        
        $result = array ();
        $dealer_company_id = Zend_Auth::getInstance()->getIdentity()->dealer_company_id;
        
        try
        {
            // get proper result
            if ($level == "report")
            {
                // check report table
                $session = new Zend_Session_Namespace('report');
                $report_id = $session->report_id;
                if ($report_id > 0)
                {
                    $table = new Proposalgen_Model_DbTable_Report();
                    $where = $table->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
                    $result = $table->fetchRow($where);
                    
                    // update fields being used
                    $field = "company_image_override";
                    $full_field = "full_company_image_override";
                }
            }
            
            if ($level == "user" || (isset($result) && empty($result [$full_field])))
            {
                // check user table
                $table = new Proposalgen_Model_DbTable_Users();
                $where = $table->getAdapter()->quoteInto('user_id = ?', $this->user_id, 'INTEGER');
                $result = $table->fetchRow($where);
                
                // update fields being used
                $field = "company_logo";
                $full_field = "full_company_logo";
            }
            
            if ($level == "dealer" || (isset($result) && empty($result [$full_field])))
            {
                // check dealer_company
                $table = new Proposalgen_Model_DbTable_DealerCompany();
                $where = $table->getAdapter()->quoteInto('dealer_company_id = ?', $dealer_company_id, 'INTEGER');
                $result = $table->fetchRow($where);
                
                // update fields being used
                $field = "company_logo";
                $full_field = "full_company_logo";
            }
            
            if ($level == "admin" || (isset($result) && empty($result [$full_field])))
            {
                // check master
                $table = new Proposalgen_Model_DbTable_DealerCompany();
                $where = $table->getAdapter()->quoteInto('company_name = ?', 'MASTER', 'INTEGER');
                $result = $table->fetchRow($where);
                
                // update fields being used
                $field = "company_logo";
                $full_field = "full_company_logo";
            }
            
            if (count($result) > 0)
            {
                if (! file_exists($this->large_image_location) && ! empty($result [$full_field]))
                {
                    $full_image = base64_decode($result [$full_field]);
                    $full_image = imagecreatefromstring($full_image);
                    imagejpeg($full_image, $this->large_image_location, 75);
                    
                    if (! file_exists($this->thumb_image_location) && ! empty($result [$field]))
                    {
                        $thumb_image = base64_decode($result [$field]);
                        $thumb_image = imagecreatefromstring($thumb_image);
                        imagejpeg($thumb_image, $this->thumb_image_location, 75);
                    }
                }
            }
        }
        catch ( Exception $e )
        {
        }
    }

    public function scale_image ($height, $width)
    {
        try
        {
            // Scale the image if it is greater than the width set above
            if ($height > $this->max_height || $width > $this->max_width)
            {
                $width_over = $width - $this->max_width;
                $height_over = $height - $this->max_height;
                
                $scaled_width = $this->max_width / $width;
                $scaled_height = $this->max_height / $height;
                
                if ($scaled_height < $scaled_width)
                {
                    $scale = $scaled_height;
                }
                else
                {
                    $scale = $scaled_width;
                }
                $uploaded = $this->resizeImage($this->large_image_location, $width, $height, $scale);
            }
            else
            {
                $scale = 1;
                $uploaded = $this->resizeImage($this->large_image_location, $width, $height, $scale);
            }
        }
        catch ( Exception $e )
        {
        }
    }

    /**
     * Allows the user to set the report settings in the override hierarchy
     * BOOKMARK: REPORT SETTINGS
     */
    public function reportsettingsAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->formTitle = 'Report Settings';
        $this->view->companyName = $this->getReportCompanyName();
        
        $this->regenerateMenu('leasing', 'settings');
        
        // get report id from session
        $session = new Zend_Session_Namespace('report');
        $report_id = $session->report_id;
        
        // Get Override Settings
        $report = Proposalgen_Model_Mapper_Report::getInstance()->find($report_id);
        $reportSettings = $report->getReportSettings(false);
        
        $user = Proposalgen_Model_User::getCurrentUser();
        $userSettings = $user->getReportSettings();
        // Set Gross Margin Pricing Config to COMP
        $userSettings ["gross_margin_pricing_config_id"] = 3;
        // Grab the settings form
        $form = new Proposalgen_Form_Settings_Report();
        
        $pricingConfigs = Proposalgen_Model_Mapper_PricingConfig::getInstance()->fetchAll();
        
        // Add all the pricing configs
        foreach ( $pricingConfigs as $pricingConfig )
        {
            $form->getElement('pricing_config_id')->addMultiOption($pricingConfig->getPricingConfigId(), ($pricingConfig->getPricingConfigId() !== 1) ? $pricingConfig->getConfigName() : "");
            $form->getElement('gross_margin_pricing_config_id')->addMultiOption($pricingConfig->getPricingConfigId(), ($pricingConfig->getPricingConfigId() !== 1) ? $pricingConfig->getConfigName() : "");
        }
        
        // Set form values based on the users selected settings
        foreach ( $reportSettings as $setting => $value )
        {
            $form->getElement($setting)->setValue((empty($value) ? "" : $value));
        }
        
        if ($this->_request->isPost())
        {
            // get form values
            $formData = $this->_request->getPost();
            
            if ($form->isValid($formData))
            {
                if (isset($formData ['save_settings']))
                {
                    $db->beginTransaction();
                    try
                    {
                        // Make all empty values = default setting
                        foreach ( $formData as $key => & $value )
                        {
                            $value = (empty($value)) ? $userSettings [$key] : $value;
                        }
                        
                        if ($formData ["pricing_config_id"] == 1)
                        {
                            $formData ["pricing_config_id"] = $userSettings ["pricing_config_id"];
                        }
                        
                        if ($formData ["gross_margin_pricing_config_id"] == 1)
                        {
                            $formData ["gross_margin_pricing_config_id"] = $userSettings ["gross_margin_pricing_config_id"];
                        }
                        
                        // $report->setReportEstimatedPageCoverageMono($formData
                        // ["estimated_page_coverage_mono"]);
                        // $report->setReportEstimatedPageCoverageColor($formData
                        // ["estimated_page_coverage_color"]);
                        $report->setReportActualPageCoverageMono($formData ["actual_page_coverage_mono"]);
                        $report->setReportActualPageCoverageColor($formData ["actual_page_coverage_color"]);
                        
                        $report->setReportMonthlyLeasePayment($formData ["monthly_lease_payment"]);
                        $report->setReportAverageNonLeasePrinterCost($formData ["default_printer_cost"]);
                        $report->setReportLeasedBwPerPage($formData ["leased_bw_per_page"]);
                        $report->setReportLeasedColorPerPage($formData ["leased_color_per_page"]);
                        $report->setReportMpsBwPerPage($formData ["mps_bw_per_page"]);
                        $report->setReportMpsColorPerPage($formData ["mps_color_per_page"]);
                        $report->setReportKilowattsPerHour($formData ["kilowatts_per_hour"]);
                        $report->setReportPricingConfigId($formData ["pricing_config_id"]);
                        $report->setReportGrossMarginPricingConfigId($formData ["gross_margin_pricing_config_id"]);
                        $report->setReportServiceCostPerPage($formData ["service_cost_per_page"]);
                        $report->setReportAdminChargePerPage($formData ["admin_charge_per_page"]);
                        $report->setReportPricingMargin($formData ["pricing_margin"]);
                        $report->setReportStage('finished');
                        
                        $report_date = new Zend_Date($formData ["report_date"]);
                        $report->setReportDate($report_date->toString('yyyy-MM-dd HH:ss'));
                        
                        // Save User
                        Proposalgen_Model_Mapper_Report::getInstance()->save($report);
                        
                        $db->commit();
                        $this->_redirect('/report');
                    }
                    catch ( Zend_Db_Exception $e )
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array (
                                "error" => "An error occured while saving your settings." 
                        ));
                    }
                    catch ( Exception $e )
                    {
                        $db->rollback();
                        $this->_helper->flashMessenger(array (
                                "error" => "An error occured while saving your settings." 
                        ));
                    }
                }
            }
            else
            {
                $this->_helper->flashMessenger(array (
                        "error" => "Please review the errors below." 
                ));
                $form->populate($formData);
            }
        }
        
        $defaultSettings = $userSettings;
        if ($defaultSettings ["pricing_config_id"] !== 1)
        {
            $defaultSettings ["pricing_config_id"] = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($defaultSettings ["pricing_config_id"])->getConfigName();
        }
        else
        {
            $defaultSettings ["pricing_config_id"] = "";
        }
        
        // Add form to page
        $form->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'forms/settings/report.phtml', 
                                'defaultSettings' => $defaultSettings 
                        ) 
                ) 
        ));
        $this->view->settingsForm = $form;
    }

    protected function getmodelsAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $terms = explode(" ", trim($_REQUEST ["searchText"]));
        $searchTerm = "%";
        foreach ( $terms as $term )
        {
            $searchTerm .= "$term%";
        }
        // Fetch Devices like term
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $sql = "SELECT concat(manufacturer_name, ' ', printer_model) as device_name, master_device_id, manufacturer_name, printer_model FROM manufacturer
        JOIN master_device on master_device.mastdevice_manufacturer = manufacturer.manufacturer_id
        WHERE concat(manufacturer_name, ' ', printer_model) LIKE '%$searchTerm%' AND manufacturer.is_deleted = 0 ORDER BY device_name ASC LIMIT 10;";
        
        $results = $db->fetchAll($sql);
        // $results is an array of device names
        $devices = array ();
        foreach ( $results as $row )
        {
            $deviceName = $row ["manufacturer_name"] . " " . $row ["printer_model"];
            $deviceName = ucwords(strtolower($deviceName));
            $devices [] = array (
                    "label" => $deviceName, 
                    "value" => $row ["master_device_id"], 
                    "manufacturer" => ucwords(strtolower($row ["manufacturer_name"])) 
            );
        }
        $lawl = Zend_Json::encode($devices);
        print $lawl;
    }

    public function getPricingMargin ($type, $dealer_id = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $master_margin = 0;
        $dealer_margin = 0;
        $user_margin = 0;
        $pricing_margin = 0;
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'u' => 'users' 
        ))
            ->joinLeft(array (
                'dc' => 'dealer_company' 
        ), 'dc.dealer_company_id = u.dealer_company_id')
            ->where('dc.company_name = "MASTER"');
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        if (count($result) > 0)
        {
            $master_margin = $result [0] ['dc_pricing_margin'];
        }
        
        $select = new Zend_Db_Select($db);
        if ($dealer_id > 0)
        {
            $select = $db->select()
                ->from(array (
                    'dc' => 'dealer_company' 
            ))
                ->where('dc.dealer_company_id = ' . $dealer_id);
        }
        else
        {
            $select = $db->select()
                ->from(array (
                    'u' => 'users' 
            ))
                ->joinLeft(array (
                    'dc' => 'dealer_company' 
            ), 'dc.dealer_company_id = u.dealer_company_id')
                ->where('u.user_id = ' . $this->user_id);
        }
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        if (count($result) > 0)
        {
            $dealer_margin = $result [0] ['dc_pricing_margin'];
        }
        
        $select = new Zend_Db_Select($db);
        $select = $db->select()
            ->from(array (
                'u' => 'users' 
        ))
            ->where('u.user_id = ?', $this->user_id);
        $stmt = $db->query($select);
        $result = $stmt->fetchAll();
        if (count($result) > 0)
        {
            $user_margin = $result [0] ['user_pricing_margin'];
        }
        
        if ($type == "master")
        {
            $pricing_margin = $master_margin;
        }
        else if ($type == "dealer")
        {
            $pricing_margin = $dealer_margin;
        }
        else
        {
            $pricing_margin = $user_margin;
        }
        return ($pricing_margin);
    }

    public function modificationwarningAction ()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->view->title = 'Report Modification Warning';
        
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            
            if (isset($formData ['chkUnderstand']))
            {
                $session = new Zend_Session_Namespace('report');
                $report_id = $session->report_id;
                
                // UPDATE REPORT DEVICE_MODIFIED FLAG TO 0
                $reportMapper = Proposalgen_Model_Mapper_Report::getInstance();
                $report = Proposalgen_Model_Mapper_Report::getInstance()->find($report_id);
                $report->setDevicesModified(false);
                $reportMapper->save($report);
                
                // redirect to correct location
                $this->_redirect('/survey');
            }
            else
            {
                $this->view->message = "You must check that you understand this warning before continuing.";
            }
        }
    }

    /**
     * Regenerates the menu as well as sets the current stage to where we are if
     * we are coming from the previous stage
     * This allows us to get back to the page we are on.
     * EG:
     * $this->regenerateMenu('hardware', 'verify');
     *
     * @param $previousstage string            
     * @param $newstage string            
     */
    public function regenerateMenu ($previousstage, $newstage)
    {
        $session = new Zend_Session_Namespace('report');
        if (isset($session->report_id))
        {
            
            $report = Proposalgen_Model_Mapper_Report::getInstance()->find($session->report_id);
            
            if ($report->getReportStage() === $previousstage)
            {
                
                $report->setReportStage($newstage);
                Proposalgen_Model_Mapper_Report::getInstance()->save($report);
            }
            $menu = new Custom_Report_Menu($report);
            $this->view->reportMenu = $menu;
        }
    }
}
