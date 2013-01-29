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
                                        if ($line->rmsModelId > 0)
                                        {
                                            $rmsDevice = $rmsDeviceMapper->find(array($uploadProviderId, $line->rmsModelId));
                                            if (!$rmsDevice instanceof Proposalgen_Model_Rms_Device)
                                            {
                                                $rmsDevice                = new Proposalgen_Model_Rms_Device($lineArray);
                                                $rmsDevice->rmsProviderId = $uploadProviderId;
                                                $rmsDevice->dateCreated   = new Zend_Db_Expr("NOW()");
                                                $rmsDevice->userId        = $this->_userId;
                                                $rmsDeviceMapper->insert($rmsDevice);
                                            }
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
    public function setMappedToAction ()
    {
        $db                               = Zend_Db_Table_Abstract::getDefaultAdapter();
        $deviceInstanceIds                = $this->_getParam('deviceInstanceIds', false);
        $masterDeviceId                   = $this->_getParam('masterDeviceId', false);
        $errorMessage                     = null;
        $deviceInstanceMasterDeviceMapper = Proposalgen_Model_Mapper_Device_Instance_Master_Device::getInstance();
        $successMessage                   = "Device mapped successfully";

        if ($deviceInstanceIds !== false && $masterDeviceId !== false)
        {
            $masterDevice = Proposalgen_Model_Mapper_MasterDevice::getInstance()->find($masterDeviceId);
            if ($masterDevice instanceof Proposalgen_Model_MasterDevice || $masterDeviceId == 0)
            {
                $deviceInstanceIds = explode(',', $deviceInstanceIds);

                $db->beginTransaction();
                try
                {
                    if ($masterDeviceId == 0)
                    {
                        $successMessage = "Device unmapped successfully";
                        // Delete mapping
                        foreach ($deviceInstanceIds as $deviceInstanceId)
                        {
                            $deviceInstanceMasterDeviceMapper->delete($deviceInstanceId);
                        }
                    }
                    else
                    {
                        foreach ($deviceInstanceIds as $deviceInstanceId)
                        {
                            $deviceInstanceMasterDevice = $deviceInstanceMasterDeviceMapper->find($deviceInstanceId);
                            if ($deviceInstanceMasterDevice instanceof Proposalgen_Model_Device_Instance_Master_Device)
                            {
                                $deviceInstanceMasterDevice->masterDeviceId = $masterDeviceId;
                                $deviceInstanceMasterDeviceMapper->save($deviceInstanceMasterDevice);
                            }
                            else
                            {
                                $deviceInstanceMasterDevice                   = new Proposalgen_Model_Device_Instance_Master_Device();
                                $deviceInstanceMasterDevice->deviceInstanceId = $deviceInstanceId;
                                $deviceInstanceMasterDevice->masterDeviceId   = $masterDeviceId;
                                $deviceInstanceMasterDeviceMapper->insert($deviceInstanceMasterDevice);

                            }
                        }
                    }

                    $db->commit();
                }
                catch (Exception $e)
                {
                    $db->rollBack();
                    My_Log::logException($e);
                    $errorMessage = "An error occurred while mapping";
                }
            }
            else
            {
                // Invalid Master Device
                $errorMessage = "Invalid master device selected";
            }

        }

        if ($errorMessage !== null)
        {
            $this->getResponse()->setHttpResponseCode(500);
            $this->_helper->json(array("error" => true, "message" => $errorMessage));
        }
        else
        {
            $this->_helper->json(array("success" => true, "message" => $successMessage));
        }
    }

    public function removedeviceAction ()
    {
        // TODO Code remove device action
    }

    public function deviceleasingAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Report_Step::STEP_FLEETDATA_SUMMARY);

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
        // TODO: Code the device leasing list action
    }

    public function deviceleasingexcludedlistAction ()
    {
        // TODO: Code the get leasing excluded list?
    }

    public function setleasedAction ()
    {
        // TODO: Code the set leased action
    }

    public function setexcludedAction ()
    {
        // TODO: Code the set excluded action
    }

    /**
     * Allows the user to set the report settings for a report
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

    /**
     * This is where a user can modify the properties of an rms upload row in a way that will make it valid
     */
    public function adddeviceAction()
    {
        $form = new Proposalgen_Form_Fleet_AddDevice();

        // TODO: Code add device


        $this->view->form = $form;
    }
}