<?php

/**
 * Class Proposalgen_Service_Rms_Upload
 */
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
     * @param int                               $userId
     * @param int                               $clientId
     * @param Proposalgen_Model_Rms_Upload|null $rmsUpload
     */
    public function __construct ($userId, $clientId, $rmsUpload = null)
    {
        $this->_userId   = $userId;
        $this->_clientId = $clientId;

        if ($rmsUpload instanceof Proposalgen_Model_Rms_Upload)
        {
            $this->rmsUpload = $rmsUpload;
        }
    }

    /**
     * Process upload will take Proposalgen_Form_ImportRmsCsv values and process the upload,
     * the success is measured by the bool value returned. Error messages will be saved inside
     * the local member $errorMessages.
     *
     * @param $data
     *
     * @param $dealerId
     *
     * @return bool success
     */
    public function processUpload ($data, $dealerId)
    {
        $importSuccessful = false;

        $this->_fileName = $this->getForm()->getUploadedFilename();
        if ($this->getForm()->isValid($data) && $this->_fileName !== false)
        {
            $values               = $this->getForm()->getValues();
            $this->_rmsProviderId = $values ['rmsProviderId'];

            // Get the appropriate service based on the RMS provider
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
                case Proposalgen_Model_Rms_Provider::RMS_PROVIDER_PRINT_AUDIT:
                    $uploadProviderId = Proposalgen_Model_Rms_Provider::RMS_PROVIDER_PRINT_AUDIT;
                    $uploadCsvService = new Proposalgen_Service_Rms_Upload_PrintAudit();
                    break;
                case Proposalgen_Model_Rms_Provider::RMS_PROVIDER_NER_DATA:
                    $uploadProviderId = Proposalgen_Model_Rms_Provider::RMS_PROVIDER_NER_DATA;
                    $uploadCsvService = new Proposalgen_Service_Rms_Upload_NerData();
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

                    /**
                     * Store our old upload in case we need it.
                     */
                    $oldRmsUpload = null;
                    if ($this->rmsUpload instanceof Proposalgen_Model_Rms_Upload)
                    {
                        $oldRmsUpload = $this->rmsUpload;
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

                        if ($this->rmsUpload->validRowCount < 2)
                        {
                            $this->errorMessages = "Your file had less than 2 valid rows in it. We require that you have 2 or more valid rows to upload a file";
                        }
                        else
                        {

                            if ($oldRmsUpload instanceof Proposalgen_Model_Rms_Upload)
                            {
                                /**
                                 * Delete all previously uploaded lines
                                 */
                                $rmsUploadRowMapper->deleteAllForRmsUpload($oldRmsUpload->id);
                                $rmsExcludedRowMapper->deleteAllForRmsUpload($oldRmsUpload->id);
                                Proposalgen_Model_Mapper_Rms_Upload::getInstance()->delete($oldRmsUpload);
                            }


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
                                 * Check and insert RMS device
                                 */
                                if ($line->rmsModelId > 0)
                                {
                                    $rmsDevice = $rmsDeviceMapper->find(array($uploadProviderId, $line->rmsModelId));
                                    if ($rmsDevice instanceof Proposalgen_Model_Rms_Device)
                                    {
                                        if ($rmsDevice->isGeneric)
                                        {
                                            $line->rmsModelId = null;
                                        }
                                        $lineArray = $line->toArray();
                                    }
                                    else
                                    {
                                        $rmsDevice                = new Proposalgen_Model_Rms_Device($lineArray);
                                        $rmsDevice->rmsProviderId = $uploadProviderId;
                                        $rmsDevice->dateCreated   = new Zend_Db_Expr("NOW()");
                                        $rmsDevice->userId        = $this->_userId;
                                        $rmsDeviceMapper->insert($rmsDevice);
                                    }

                                }

                                /*
                                 * Save RMS Upload Row
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

                                $defaultDatabaseField = new Zend_Db_Expr('NULL');

                                // Life Meter
                                if ($line->endMeterLife > 0)
                                {
                                    $meter->startMeterLife = $line->startMeterLife;
                                    $meter->endMeterLife   = $line->endMeterLife;
                                }
                                else if ($line->endMeterBlack > 0 || $line->endMeterColor > 0)
                                {
                                    $meter->startMeterLife = $line->startMeterBlack + $line->startMeterColor;
                                    $meter->endMeterLife   = $line->endMeterBlack + $line->endMeterColor;
                                }
                                else
                                {
                                    $meter->startMeterLife = $defaultDatabaseField;
                                    $meter->endMeterLife   = $defaultDatabaseField;
                                }

                                //  Black
                                if ($line->endMeterBlack > 0)
                                {
                                    $meter->startMeterBlack = $line->startMeterBlack;
                                    $meter->endMeterBlack   = $line->endMeterBlack;
                                }
                                // If we don't have a meter lets try creating it
                                else if ($line->endMeterLife > 0)
                                {
                                    $endMeterBlack = $line->endMeterLife - $line->endMeterPrintColor - $line->endMeterCopyColor - $line->endMeterPrintA3Color;
                                    if ($endMeterBlack > 0)
                                    {
                                        $meter->startMeterBlack = $line->startMeterLife - $line->startMeterPrintColor - $line->startMeterCopyColor - $line->startMeterPrintA3Color;
                                        $meter->endMeterBlack   = $endMeterBlack;
                                    }
                                    else
                                    {
                                        $meter->startMeterBlack = $defaultDatabaseField;
                                        $meter->endMeterBlack   = $defaultDatabaseField;
                                    }
                                }
                                else
                                {
                                    $meter->startMeterBlack = $defaultDatabaseField;
                                    $meter->endMeterBlack   = $defaultDatabaseField;
                                }

                                //  Color
                                if ($line->endMeterColor > 0)
                                {
                                    $meter->startMeterColor = $line->startMeterColor;
                                    $meter->endMeterColor   = $line->endMeterColor;
                                }
                                else
                                {
                                    $meter->startMeterColor = $defaultDatabaseField;
                                    $meter->endMeterColor   = $defaultDatabaseField;
                                }

                                // Print Black
                                if ($line->endMeterPrintBlack > 0)
                                {
                                    $meter->startMeterPrintBlack = $line->startMeterPrintBlack;
                                    $meter->endMeterPrintBlack   = $line->endMeterPrintBlack;
                                }
                                else
                                {
                                    $meter->startMeterPrintBlack = $defaultDatabaseField;
                                    $meter->endMeterPrintBlack   = $defaultDatabaseField;
                                }

                                // Print Color
                                if ($line->endMeterPrintColor > 0)
                                {
                                    $meter->startMeterPrintColor = $line->startMeterPrintColor;
                                    $meter->endMeterPrintColor   = $line->endMeterPrintColor;
                                }
                                else
                                {
                                    $meter->startMeterPrintColor = $defaultDatabaseField;
                                    $meter->endMeterPrintColor   = $defaultDatabaseField;
                                }

                                // Copy Black
                                if ($line->endMeterCopyBlack > 0)
                                {
                                    $meter->startMeterCopyBlack = $line->startMeterCopyBlack;
                                    $meter->endMeterCopyBlack   = $line->endMeterCopyBlack;
                                }
                                else
                                {
                                    $meter->startMeterCopyBlack = $defaultDatabaseField;
                                    $meter->endMeterCopyBlack   = $defaultDatabaseField;
                                }

                                // Copy Color
                                if ($line->endMeterCopyColor > 0)
                                {
                                    $meter->startMeterCopyColor = $line->startMeterCopyColor;
                                    $meter->endMeterCopyColor   = $line->endMeterCopyColor;
                                }
                                else
                                {
                                    $meter->startMeterCopyColor = $defaultDatabaseField;
                                    $meter->endMeterCopyColor   = $defaultDatabaseField;
                                }

                                // Fax
                                if ($line->endMeterFax > 0)
                                {
                                    $meter->startMeterFax = $line->startMeterFax;
                                    $meter->endMeterFax   = $line->endMeterFax;
                                }
                                else
                                {
                                    $meter->startMeterFax = $defaultDatabaseField;
                                    $meter->endMeterFax   = $defaultDatabaseField;
                                }

                                // Scan
                                if ($line->endMeterScan > 0)
                                {
                                    $meter->startMeterScan = $line->startMeterScan;
                                    $meter->endMeterScan   = $line->endMeterScan;
                                }
                                else
                                {
                                    $meter->startMeterScan = $defaultDatabaseField;
                                    $meter->endMeterScan   = $defaultDatabaseField;
                                }

                                // Print A3 Black
                                if ($line->endMeterPrintA3Black > 0)
                                {
                                    $meter->startMeterPrintA3Black = $line->startMeterPrintA3Black;
                                    $meter->endMeterPrintA3Black   = $line->endMeterPrintA3Black;
                                }
                                else
                                {
                                    $meter->startMeterPrintA3Black = $defaultDatabaseField;
                                    $meter->endMeterPrintA3Black   = $defaultDatabaseField;
                                }

                                // Print A3 Color
                                if ($line->endMeterPrintA3Color > 0)
                                {
                                    $meter->startMeterPrintA3Color = $line->startMeterPrintA3Color;
                                    $meter->endMeterPrintA3Color   = $line->endMeterPrintA3Color;
                                }
                                else
                                {
                                    $meter->startMeterPrintA3Color = $defaultDatabaseField;
                                    $meter->endMeterPrintA3Color   = $defaultDatabaseField;
                                }

                                $deviceInstanceMeterMapper->insert($meter);
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

                            /**
                             * @var $deviceInstance Proposalgen_Model_DeviceInstance
                             */
                            foreach ($deviceInstances as $deviceInstance)
                            {
                                $deviceInstance             = Proposalgen_Model_Mapper_DeviceInstance::getInstance()->find($deviceInstance->id);
                                $deviceInstanceMasterDevice = Proposalgen_Model_Mapper_Device_Instance_Master_Device::getInstance()->find($deviceInstance->id);

                                if ($deviceInstanceMasterDevice instanceof Proposalgen_Model_Device_Instance_Master_Device)
                                {
                                    $masterDevice                             = $deviceInstanceMasterDevice->getMasterDevice();
                                    $deviceInstance->isLeased                 = $masterDevice->isLeased;
                                    $deviceInstance->compatibleWithJitProgram = $masterDevice->isJitCompatible($dealerId);
                                    Proposalgen_Model_Mapper_DeviceInstance::getInstance()->save($deviceInstance);
                                }
                            }


                            $db->commit();

                            $importSuccessful = true;
                        }
                    }
                    else
                    {
                        $this->errorMessages = "There was an error importing your file. $processCsvMessage";
                    }
                }
                catch (Exception $e)
                {
                    Tangent_Log::logException($e);
                    $db->rollBack();
                    $this->errorMessages = "There was an error parsing your file. If this continues to happen please reference this id when requesting support: " . Tangent_Log::getUniqueId();
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