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

        $report               = $this->getReport();
        $form                 = new Proposalgen_Form_ImportRmsCsv(array('csv'), "1B", "8MB");
        $deviceInstanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
        $rmsExcludedRowMapper = Proposalgen_Model_Mapper_Rms_Excluded_Row::getInstance();

        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            if (isset($values ["goBack"]))
            {
                $this->gotoPreviousStep();
            }
            else if (isset($values ["performUpload"]))
            {
                /*
                 * Handle Upload
                 */
                if ($form->isValid($values))
                {
                    $filename = $form->getUploadedFilename();
                    if ($filename !== false)
                    {
                        /*
                         * Process the csv file
                         */
                        $formData         = $form->getValues();
                        $importSuccessful = false;

                        // Get the appropriate service based on the rms provider
                        switch ((int)$formData ["rmsProviderId"])
                        {
                            case Proposalgen_Model_Rms_Provider::RMS_PROVIDER_PRINTFLEET :
                                $uploadProviderId = Proposalgen_Model_Rms_Provider::RMS_PROVIDER_PRINTFLEET;
                                $uploadCsvService = new Proposalgen_Service_Rms_Upload_PrintFleet();
                                break;
                            case Proposalgen_Model_Rms_Provider::RMS_PROVIDER_FMAUDIT :
                                $uploadProviderId = Proposalgen_Model_Rms_Provider::RMS_PROVIDER_FMAUDIT;
                                $uploadCsvService = new Proposalgen_Service_Rms_Upload_FmAudit();
                                break;
                            case Proposalgen_Model_Rms_Provider::RMS_PROVIDER_XEROX:
                                $uploadProviderId = Proposalgen_Model_Rms_Provider::RMS_PROVIDER_XEROX;
                                $uploadCsvService = new Proposalgen_Service_Rms_Upload_Xerox();
                                break;
                            default :
                                $uploadCsvService = null;
                                $uploadProviderId = null;
                                $this->_helper->flashMessenger(array(
                                                                    'success' => "Invalid RMS Provider Selected"
                                                               ));
                                break;
                        }

                        if ($uploadCsvService instanceof Proposalgen_Service_Rms_Upload_Abstract)
                        {
                            $db = Zend_Db_Table::getDefaultAdapter();
                            $db->beginTransaction();
                            try
                            {
                                $rmsUploadRowMapper        = Proposalgen_Model_Mapper_Rms_Upload_Row::getInstance();
                                $deviceInstanceMeterMapper = Proposalgen_Model_Mapper_DeviceInstanceMeter::getInstance();
                                $rmsDeviceMapper           = Proposalgen_Model_Mapper_Rms_Device::getInstance();

                                /*
                                 * Delete all previously uploaded lines
                                 */
                                $rmsUploadRowMapper->deleteAllForReport($report->id);
                                $rmsExcludedRowMapper->deleteAllForReport($report->id);


                                /*
                                 * Process the new data
                                 */
                                $processCsvMessage = $uploadCsvService->processCsvFile($filename);
                                if ($processCsvMessage === true)
                                {
                                    /**
                                     * Valid Lines
                                     */
                                    /**
                                     * @var Proposalgen_Model_DeviceInstance[]
                                     */
                                    $deviceInstances = array();
                                    foreach ($uploadCsvService->validCsvLines as $line)
                                    {
                                        /*
                                         * Convert line into device instance, upload row, and meters
                                         */
                                        $lineArray = $line->toArray();

                                        /*
                                         * Check and insert rms device
                                         */
                                        $rmsDevice = $rmsDeviceMapper->find(array($uploadProviderId, $line->rmsModelId));
                                        if (!$rmsDevice instanceof Proposalgen_Model_Rms_Device)
                                        {
                                            $rmsDevice                = new Proposalgen_Model_Rms_Device($lineArray);
                                            $rmsDevice->rmsProviderId = $uploadProviderId;
                                            $rmsDevice->dateCreated   = new Zend_Db_Expr("NOW()");
                                            $rmsDevice->userId        = $this->_userId;
                                            $rmsDeviceMapper->insert($rmsDevice);
                                        }

                                        /*
                                         * Save Rms Upload Row
                                         */
                                        $rmsUploadRow                = new Proposalgen_Model_Rms_Upload_Row($lineArray);
                                        $rmsUploadRow->rmsProviderId = $uploadProviderId;
                                        $rmsUploadRowMapper->insert($rmsUploadRow);

                                        /*
                                         * Save Device Instance
                                         */
                                        $deviceInstance                 = new Proposalgen_Model_DeviceInstance($lineArray);
                                        $deviceInstance->reportId       = $report->id;
                                        $deviceInstance->rmsUploadRowId = $rmsUploadRow->id;
                                        $deviceInstanceMapper->insert($deviceInstance);

                                        $deviceInstances[] = $deviceInstance;

                                        /*
                                         * Save Meters
                                         */

                                        $meter                   = new Proposalgen_Model_DeviceInstanceMeter();
                                        $meter->deviceInstanceId = $deviceInstance->id;
                                        $meter->monitorStartDate = $line->monitorStartDate;
                                        $meter->monitorEndDate   = $line->monitorEndDate;

                                        // Black Meter
                                        $meter->meterType  = Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_BLACK;
                                        $meter->startMeter = $line->startMeterBlack;
                                        $meter->endMeter   = $line->endMeterBlack;
                                        $deviceInstanceMeterMapper->insert($meter);

                                        // Color Meter
                                        if ($line->endMeterColor > 0)
                                        {
                                            $meter->meterType  = Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COLOR;
                                            $meter->startMeter = $line->startMeterColor;
                                            $meter->endMeter   = $line->endMeterColor;
                                            $deviceInstanceMeterMapper->insert($meter);
                                        }

                                        // Life Meter
                                        if ($line->endMeterLife > 0)
                                        {
                                            $meter->meterType  = Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_LIFE;
                                            $meter->startMeter = $line->startMeterLife;
                                            $meter->endMeter   = $line->endMeterLife;
                                            $deviceInstanceMeterMapper->insert($meter);
                                        }

                                        // Print Black
                                        if ($line->endMeterPrintBlack > 0)
                                        {
                                            $meter->meterType  = Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_PRINT_BLACK;
                                            $meter->startMeter = $line->startMeterPrintBlack;
                                            $meter->endMeter   = $line->endMeterPrintBlack;
                                            $deviceInstanceMeterMapper->insert($meter);
                                        }

                                        // Print Color
                                        if ($line->endMeterPrintColor > 0)
                                        {
                                            $meter->meterType  = Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_PRINT_COLOR;
                                            $meter->startMeter = $line->startMeterPrintColor;
                                            $meter->endMeter   = $line->endMeterPrintColor;
                                            $deviceInstanceMeterMapper->insert($meter);
                                        }


                                        // Copy Black
                                        if ($line->endMeterCopyBlack > 0)
                                        {
                                            $meter->meterType  = Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COPY_BLACK;
                                            $meter->startMeter = $line->startMeterCopyBlack;
                                            $meter->endMeter   = $line->endMeterCopyBlack;
                                            $deviceInstanceMeterMapper->insert($meter);
                                        }

                                        // Copy Color
                                        if ($line->endMeterCopyColor > 0)
                                        {
                                            $meter->meterType  = Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COPY_COLOR;
                                            $meter->startMeter = $line->startMeterCopyColor;
                                            $meter->endMeter   = $line->endMeterCopyColor;
                                            $deviceInstanceMeterMapper->insert($meter);
                                        }

                                        // Scan Meter
                                        if ($line->endMeterScan > 0)
                                        {
                                            $meter->meterType  = Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_SCAN;
                                            $meter->startMeter = $line->startMeterScan;
                                            $meter->endMeter   = $line->endMeterScan;
                                            $deviceInstanceMeterMapper->insert($meter);
                                        }

                                        // Fax Meter
                                        if ($line->endMeterFax > 0)
                                        {
                                            $meter->meterType  = Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_FAX;
                                            $meter->startMeter = $line->startMeterFax;
                                            $meter->endMeter   = $line->endMeterFax;
                                            $deviceInstanceMeterMapper->insert($meter);
                                        }
                                    }

                                    /**
                                     * Invalid Lines
                                     */
                                    foreach ($uploadCsvService->invalidCsvLines as $line)
                                    {
                                        $rmsExcludedRow = new Proposalgen_Model_Rms_Excluded_Row($line->toArray());

                                        // Set values that have different names than in $line
                                        $rmsExcludedRow->manufacturerName = $line->manufacturer;
                                        $rmsExcludedRow->reportId         = $report->id;
                                        $rmsExcludedRow->reason           = $line->validationErrorMessage;

                                        // Set values that are none existent in $line
                                        $rmsExcludedRow->rmsProviderId = $uploadProviderId;

                                        $rmsExcludedRowMapper->insert($rmsExcludedRow);
                                    }


                                    /*
                                     * Perform Mapping
                                     */
                                    $deviceMappingService = new Proposalgen_Service_DeviceMapping();

                                    $deviceMappingService->mapDevices($deviceInstances, $this->_userId, true);


                                }
                                else
                                {
                                    $this->_helper->flashMessenger(array(
                                                                        'error' => "There was an error importing your file. $processCsvMessage"
                                                                   ));
                                }
                                $db->commit();
                                $importSuccessful = true;
                            }
                            catch (Exception $e)
                            {
                                My_Log::logException($e);
                                $db->rollBack();

                                $this->_helper->flashMessenger(array(
                                                                    'danger' => "There was an error parsing your file. If this continues to happen please reference this id when requesting support: " . My_Log::getUniqueId()
                                                               ));
                            }
                        }

                        // Only when we are successful will we display a success message
                        if ($importSuccessful === true)
                        {
                            $this->_helper->flashMessenger(array(
                                                                'success' => "Your file was imported successfully."
                                                           ));
                        }
                    }
                    else
                    {
                        $this->_helper->flashMessenger(array(
                                                            'danger' => "Upload Failed. Please try again."
                                                       ));
                    }
                }
                else
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => "Upload Failed. Please check the errors below."
                                                   ));
                }
            }
            else if (isset($values ["saveAndContinue"]))
            {
                $count = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->countRowsForReport($report->id);
                if ($count < 2)
                {
                    $this->_helper->flashMessenger(array(
                                                        'danger' => "You must have at least 2 valid devices to continue."
                                                   ));
                }
                else
                {
                    // Every time we save anything related to a report, we should save it (updates the modification date)
                    $this->saveReport();

                    // Call the base controller to send us to the next logical step in the proposal.
                    $this->gotoNextStep();
                }
            }
        }

        $this->view->form = $form;

        $this->view->deviceInstanceCount = $deviceInstanceMapper->countRowsForReport($report->id);
        $this->view->rmsExcludedRowCount = $rmsExcludedRowMapper->countRowsForReport($report->id);
        $this->view->hasPreviousUpload   = ($this->view->deviceInstanceCount > 0 || $this->view->rmsExcludedRowCount > 0);

    }

    /**
     * This handles the mapping of devices to our master devices
     */
    public function devicemappingAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_FLEETDATA_MAPDEVICES);

        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['saveAndContinue']))
            {
                // Every time we save anything related to a report, we should save it (updates the modification date)
                $this->saveReport();

                // Call the base controller to send us to the next logical step in the proposal.
                $this->gotoNextStep();
            }
            else if (isset($postData['goBack']))
            {
                // Call the base controller to send us to the next logical step in the proposal.
                $this->gotoPreviousStep();
            }
        }


    }

    /**
     * Generates a list of devices that were not mapped automatically
     */
    public function devicemappinglistAction ()
    {
        $jqGrid                  = new Tangent_Service_JQGrid();
        $mapDeviceInstanceMapper = Proposalgen_Model_Mapper_Map_Device_Instance::getInstance();

        /*
         * Grab the incoming parameters
         */
        $jqGridParameters = array(
            'sidx' => $this->_getParam('sidx', 'manufacturer'),
            'sord' => $this->_getParam('sord', 'ASC'),
            'page' => $this->_getParam('page', 1),
            'rows' => $this->_getParam('rows', 10)
        );

        // Set up validation arrays
        $blankModel  = new Proposalgen_Model_Map_Device_Instance();
        $sortColumns = array_keys($blankModel->toArray());

        $jqGrid->parseJQGridPagingRequest($jqGridParameters);
        $jqGrid->setValidSortColumns($sortColumns);


        if ($jqGrid->sortingIsValid())
        {
            $jqGrid->setRecordCount($mapDeviceInstanceMapper->fetchAllForReport($this->getReport()->id, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), null, null, true));

            // Validate current page number since we don't want to be out of bounds
            if ($jqGrid->getCurrentPage() < 1)
            {
                $jqGrid->setCurrentPage(1);
            }
            else if ($jqGrid->getCurrentPage() > $jqGrid->calculateTotalPages())
            {
                $jqGrid->setCurrentPage($jqGrid->calculateTotalPages());
            }

            // Return a small subset of the results based on the jqGrid parameters
            $startRecord = $jqGrid->getRecordsPerPage() * ($jqGrid->getCurrentPage() - 1);
            $jqGrid->setRows($mapDeviceInstanceMapper->fetchAllForReport($this->getReport()->id, $jqGrid->getSortColumn(), $jqGrid->getSortDirection(), $jqGrid->getRecordsPerPage(), $startRecord));

            // Send back jqGrid json data
            $this->_helper->json($jqGrid->createPagerResponseArray());
        }
        else
        {
            $this->_response->setHttpResponseCode(500);
            $this->_helper->json(array(
                                      'error' => 'Sorting parameters are invalid'
                                 ));
        }
    }

    /**
     * Handles mapping a device
     *
     * @throws Exception
     */
    public function setmappedtoAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db = Zend_Db_Table::getDefaultAdapter();

        // get params
        $devices_pf_id    = $this->_getParam('devices_pf_id', 0);
        $master_device_id = $this->_getParam('master_device_id', 0);

        $db->beginTransaction();
        try
        {
            // add pf_device_matchup_users record
            $pf_device_matchup_usersTable = new Proposalgen_Model_DbTable_PFMatchupUsers();
            if ($devices_pf_id > 0)
            {
                $where  = $pf_device_matchup_usersTable->getAdapter()->quoteInto('pf_device_id = ' . $devices_pf_id . ' AND user_id = ' . $this->_userId, null);
                $result = $pf_device_matchup_usersTable->fetchRow($where);

                $pf_device_matchup_usersData = array(
                    'master_device_id' => $master_device_id
                );

                if ($result && count($result->toArray()) > 0)
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
                    $pf_device_matchup_usersData ['pf_device_id'] = $devices_pf_id;
                    $pf_device_matchup_usersData ['user_id']      = $this->_userId;
                    $pf_device_matchup_usersTable->insert($pf_device_matchup_usersData);
                }
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            My_Log::logException($e);
            $db->rollback();
            $this->_helper->json(array("success" => false, "message" => "Error mapping device"));
        }
        $this->_helper->json(array("success" => true, "message" => "Device mapped successfully"));
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

    public function removedeviceAction ()
    {
        // disable the default layout
        $db = Zend_Db_Table::getDefaultAdapter();

        // get devices_pf_id
        $devices_pf_id = $this->_getParam('key', null);

        $jsonResponse = array();

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
            $jsonResponse["success"] = "Device was removed successfully";
        }
        catch (Exception $e)
        {
            $db->rollback();
            My_Log::logException($e);
            $uid                   = My_Log::getUniqueId();
            $jsonResponse["error"] = "There was an error removing the device. Please try again. If this continues to happen please reference #{$uid} in your support request.";
        }

        $this->_helper->json($jsonResponse);
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
        /*
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
        */
        if ($this->getRequest()->isPost())
        {
            $postData = $this->getRequest()->getPost();
            if (isset($postData['saveAndContinue']))
            {
                // Every time we save anything related to a report, we should save it (updates the modification date)
                $this->saveReport();

                // Call the base controller to send us to the next logical step in the proposal.
                $this->gotoNextStep();
            }
            else if (isset($postData['goBack']))
            {
                // Call the base controller to send us to the next logical step in the proposal.
                $this->gotoPreviousStep();
            }
        }
    }

    public function deviceleasinglistAction ()
    {
        // disable the default layout
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
                            $ampv = number_format($unknown_device_instance [0]->getAverageMonthlyPageCount());
                        }
                    }
                    else if ($result [$key] ['di_device_instance_id'] > 0)
                    {
                        $mapped_to    = $result [$key] ['manufacturer_name'] . ' ' . $result [$key] ['printer_model'];
                        $mapped_to_id = $result [$key] ['master_device_id'];
                        $is_excluded  = $result [$key] ['di_is_excluded'];

                        // get average monthly page volume
                        $device_instanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
                        $device_instance       = $device_instanceMapper->fetchRow('id = ' . $result [$key] ['di_device_instance_id']);

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
            My_Log::logException($e);
        }

        // encode user data to return to the client:
        $this->_helper->json($formData);
    }

    public function deviceleasingexcludedlistAction ()
    {
        // disable the default layout
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
        $this->_helper->json($formData);
    }

    public function setleasedAction ()
    {
        // disable the default layout
        $this->_helper->layout->disableLayout();
        $db            = Zend_Db_Table::getDefaultAdapter();
        $devices_pf_id = $this->_getParam('id', false);
        $value         = $this->_getParam('mode', 0);
        $jsonResponse  = array();

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
            $jsonResponse["success"] = "The device is now marked as leased";
        }
        catch (Exception $e)
        {
            $db->rollBack();
            My_Log::logException($e);
            My_Log::getUniqueId();
            $jsonResponse["error"] = "There was an error setting the device to leased.";
        }
        $this->_helper->json($jsonResponse);
    }

    public function setexcludedAction ()
    {
        // disable the default layout
        $db                       = Zend_Db_Table::getDefaultAdapter();
        $upload_data_collector_id = $this->_getParam('id', false);
        $value                    = $this->_getParam('mode', 0);
        $jsonResponse             = array();
        $updateData               = array(
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
            $jsonResponse["success"] = "The device is now excluded";
        }
        catch (Exception $e)
        {
            $db->rollBack();
            My_Log::logException($e);
            My_Log::getUniqueId();
            $jsonResponse["error"] = "There was an error setting the device to excluded.";
        }

        $this->_helper->json($jsonResponse);
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