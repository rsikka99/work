<?php
class Hardwareoptimization_IndexController extends Tangent_Controller_Action
{
    /**
     * Users can upload/see uploaded data on this step
     */
    public function indexAction ()
    {
        // Mark the step we're on as active
        $this->setActiveReportStep(Proposalgen_Model_Assessment_Step::STEP_FLEETDATA_UPLOAD);

        $report               = $this->getReport();
        $form                 = new Proposalgen_Form_ImportRmsCsv(array('csv'), "1B", "8MB");
        $deviceInstanceMapper = Proposalgen_Model_Mapper_DeviceInstance::getInstance();
        $rmsExcludedRowMapper = Proposalgen_Model_Mapper_Rms_Excluded_Row::getInstance();

        $rmsUpload = $report->getRmsUpload();

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
                                $this->_flashMessenger->addMessage(array(
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


                                if ($rmsUpload instanceof Proposalgen_Model_Rms_Upload)
                                {
                                    /**
                                     * Delete all previously uploaded lines
                                     */
                                    $rmsUploadRowMapper->deleteAllForRmsUpload($rmsUpload->id);
                                    $rmsExcludedRowMapper->deleteAllForRmsUpload($report->id);

                                    Proposalgen_Model_Mapper_Rms_Upload::getInstance()->delete($rmsUpload);
                                }

                                $rmsUpload                  = new Proposalgen_Model_Rms_Upload();
                                $rmsUpload->uploadDate      = new Zend_Db_Expr('NOW()');
                                $rmsUpload->fileName        = basename($filename);
                                $rmsUpload->clientId        = $report->clientId;
                                $rmsUpload->rmsProviderId   = $uploadProviderId;
                                $rmsUpload->invalidRowCount = 0;
                                $rmsUpload->validRowCount   = 0;


                                /*
                                 * Process the new data
                                 */
                                $processCsvMessage = $uploadCsvService->processCsvFile($filename);
                                if ($processCsvMessage === true)
                                {

                                    /**
                                     * Save our upload object
                                     */
                                    $rmsUpload->invalidRowCount = count($uploadCsvService->invalidCsvLines);
                                    $rmsUpload->validRowCount   = count($uploadCsvService->validCsvLines);

                                    if ($rmsUpload->id > 0)
                                    {
                                        Proposalgen_Model_Mapper_Rms_Upload::getInstance()->save($rmsUpload);
                                    }
                                    else
                                    {
                                        Proposalgen_Model_Mapper_Rms_Upload::getInstance()->insert($rmsUpload);
                                    }

                                    $report->rmsUploadId = $rmsUpload->id;
                                    $report->setRmsUpload($rmsUpload);

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
                                        $rmsUploadRow                 = new Proposalgen_Model_Rms_Upload_Row($lineArray);
                                        $rmsUploadRow->fullDeviceName = "{$line->manufacturer} {$line->modelName}";
                                        $rmsUploadRow->rmsProviderId  = $uploadProviderId;

                                        // Lets make an attempt at finding the manufacturer
                                        $manufacturers = Proposalgen_Model_Mapper_Manufacturer::getInstance()->searchByName($rmsUploadRow->manufacturer);
                                        if ($manufacturers && count($manufacturers) > 0)
                                        {
                                            $rmsUploadRow->manufacturerId = $manufacturers[0]->id;
                                        }

                                        $rmsUploadRowMapper->insert($rmsUploadRow);


                                        /*
                                         * Save Device Instance
                                         */
                                        $deviceInstance                 = new Proposalgen_Model_DeviceInstance($lineArray);
                                        $deviceInstance->rmsUploadId    = $rmsUpload->id;
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
                                        $rmsExcludedRow->rmsUploadId      = $rmsUpload->id;
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

                                    $db->commit();
                                    $importSuccessful = true;
                                }
                                else
                                {
                                    $this->_flashMessenger->addMessage(array(
                                                                            'error' => "There was an error importing your file. $processCsvMessage"
                                                                       ));
                                }

                            }
                            catch (Exception $e)
                            {
                                My_Log::logException($e);
                                $db->rollBack();

                                $this->_flashMessenger->addMessage(array(
                                                                        'danger' => "There was an error parsing your file. If this continues to happen please reference this id when requesting support: " . My_Log::getUniqueId()
                                                                   ));
                            }
                        }

                        // Only when we are successful will we display a success message
                        if ($importSuccessful === true)
                        {
                            $this->_flashMessenger->addMessage(array(
                                                                    'success' => "Your file was imported successfully."
                                                               ));
                            $this->saveReport();
                            $this->gotoNextStep();
                        }
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage(array(
                                                                'danger' => "Upload Failed. Please try again."
                                                           ));
                    }
                }
                else
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => "Upload Failed. Please check the errors below."
                                                       ));
                }
            }
            else if (isset($values ["saveAndContinue"]))
            {
                $count = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->countRowsForRmsUpload($report->getRmsUpload()->id);
                if ($count < 2)
                {
                    $this->_flashMessenger->addMessage(array(
                                                            'danger' => "You must have at least 2 valid devices to continue."
                                                       ));
                }
                else
                {
                    // Call the base controller to send us to the next logical step in the proposal.
                    $this->gotoNextStep();
                }
            }
        }

        $this->view->form = $form;

        $this->view->rmsUpload = $rmsUpload;
//        if($rmsUpload instanceof Proposalgen_Model_Rms_Upload_Row)
//        {
//            $this->view->populateGrid = true;
//        }

        $navigationButtons          = ($rmsUpload instanceof Proposalgen_Model_Rms_Upload) ? Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK_NEXT : Proposalgen_Form_Assessment_Navigation::BUTTONS_BACK;
        $this->view->navigationForm = new Proposalgen_Form_Assessment_Navigation($navigationButtons);

    }
}