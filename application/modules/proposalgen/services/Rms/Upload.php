<?php
class Proposalgen_Service_Rms_Upload
{
    /**
     * @var int
     */
    protected $_userId;

    /**
     * @var string
     */
    protected $_fileName;

    /**
     * @var int
     */
    protected $_clientId;

    /**
     * @var int
     */
    protected $_rmsProviderId;

    /**
     * @var Proposalgen_Model_Rms_Upload
     */
    public $rmsUpload;

    /**
     * @var Proposalgen_Form_ImportRmsCsv
     */
    protected $_form;

    /**
     * @var string
     */
    public $errorMessages;


    /**
     * @param int      $userId
     * @param int      $clientId
     * @param int|null $rmsUploadId
     */
    public function __construct ($userId, $clientId, $rmsUploadId = null)
    {
        $this->_userId   = $userId;
        $this->_clientId = $clientId;

        if ($rmsUploadId > 0)
        {
            $this->_rmsUpload = Proposalgen_Model_Mapper_Rms_Upload::getInstance()->find($rmsUploadId);
        }
    }

    /**
     * Process upload will take Proposalgen_Form_ImportRmsCsv values and process the upload,
     * the success is measured by the bool value returned. Error messages will be saved inside
     * the local member $errorMessages.
     *
     * @param $data
     *
     * @return bool success
     */
    public function processUpload ($data)
    {
        $importSuccessful = false;

        $this->_fileName = $this->getForm()->getUploadedFilename();
        if ($this->getForm()->isValid($data) && $this->_fileName !== false)
        {
            $values               = $this->getForm()->getValues();
            $this->_rmsProviderId = $values ['rmsProviderId'];

            // Get the appropriate service based on the rms provider
            switch ((int)$this->_rmsProviderId)
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
                    $uploadCsvService    = null;
                    $uploadProviderId    = null;
                    $this->errorMessages = "Invalid RMS Provider Selected";
                    break;
            }

            if ($uploadCsvService instanceof Proposalgen_Service_Rms_Upload_Abstract)
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try
                {
                    $rmsUploadRowMapper        = Proposalgen_Model_Mapper_Rms_Upload_Row::getInstance();
                    $rmsExcludedRowMapper      = Proposalgen_Model_Mapper_Rms_Excluded_Row::getInstance();
                    $deviceInstanceMeterMapper = Proposalgen_Model_Mapper_DeviceInstanceMeter::getInstance();
                    $rmsDeviceMapper           = Proposalgen_Model_Mapper_Rms_Device::getInstance();


                    if ($this->rmsUpload instanceof Proposalgen_Model_Rms_Upload)
                    {
                        /**
                         * Delete all previously uploaded lines
                         */
                        $rmsUploadRowMapper->deleteAllForRmsUpload($this->rmsUpload->id);
                        $rmsExcludedRowMapper->deleteAllForRmsUpload($this->rmsUpload->id);
                        Proposalgen_Model_Mapper_Rms_Upload::getInstance()->delete($this->rmsUpload);
                    }

                    $this->rmsUpload                  = new Proposalgen_Model_Rms_Upload();
                    $this->rmsUpload->uploadDate      = new Zend_Db_Expr('NOW()');
                    $this->rmsUpload->fileName        = basename($this->_fileName);
                    $this->rmsUpload->clientId        = $this->_clientId;
                    $this->rmsUpload->rmsProviderId   = $uploadProviderId;
                    $this->rmsUpload->invalidRowCount = 0;
                    $this->rmsUpload->validRowCount   = 0;


                    /*
                     * Process the new data
                     */
                    $processCsvMessage = $uploadCsvService->processCsvFile($this->_fileName);
                    if ($processCsvMessage === true)
                    {

                        /**
                         * Save our upload object
                         */
                        $this->rmsUpload->invalidRowCount = count($uploadCsvService->invalidCsvLines);
                        $this->rmsUpload->validRowCount   = count($uploadCsvService->validCsvLines);

                        if ($this->rmsUpload->id > 0)
                        {
                            Proposalgen_Model_Mapper_Rms_Upload::getInstance()->save($this->rmsUpload);
                        }
                        else
                        {
                            Proposalgen_Model_Mapper_Rms_Upload::getInstance()->insert($this->rmsUpload);
                        }


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
                            $deviceInstance->rmsUploadId    = $this->rmsUpload->id;
                            $deviceInstance->rmsUploadRowId = $rmsUploadRow->id;
                            Proposalgen_Model_Mapper_DeviceInstance::getInstance()->insert($deviceInstance);

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
                            $rmsExcludedRow->rmsUploadId      = $this->rmsUpload->id;
                            $rmsExcludedRow->reason           = $line->validationErrorMessage;

                            // Set values that are none existent in $line
                            $rmsExcludedRow->rmsProviderId = $uploadProviderId;

                            Proposalgen_Model_Mapper_Rms_Excluded_Row::getInstance()->insert($rmsExcludedRow);
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
                        $this->errorMessages = "There was an error importing your file. $processCsvMessage";
                    }
                }
                catch (Exception $e)
                {
                    My_Log::logException($e);
                    $db->rollBack();
                    $this->errorMessages = "There was an error parsing your file. If this continues to happen please reference this id when requesting support: " . My_Log::getUniqueId();
                }
            }
        }

        return $importSuccessful;
    }

    /**
     * @return Proposalgen_Form_ImportRmsCsv
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new Proposalgen_Form_ImportRmsCsv(array('csv'), "1B", "8MB");
        }

        return $this->_form;
    }
}