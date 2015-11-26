<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services;

use Exception;
use MPSToolbox\Entities\ClientEntity;
use MPSToolbox\Entities\MasterDeviceEntity;
use MPSToolbox\Entities\RmsDeviceInstanceEntity;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Forms\ImportRmsCsvForm;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DeviceInstanceMeterMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsExcludedRowMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\RmsUploadRowMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMeterModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsDeviceModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsExcludedRowModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsProviderModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\RmsUploadRowModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\AbstractRmsUploadService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\FmAuditUploadService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\FmAuditVersionFourUploadService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\LexmarkRmsUploadService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\NerDataUploadService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\PrintAuditUploadService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\PrintFleetVersionThreeUploadService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\PrintFleetVersionTwoUploadService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\PrintTrackerUploadService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\UploadLineModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Services\RmsUpload\XeroxUploadService;
use Tangent\Logger\Logger;
use Zend_Db_Expr;
use Zend_Db_Table;

/**
 * Class RmsUploadService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services
 */
class RmsUploadService
{
    /**
     * @var int
     */
    protected $_dealerId;

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
     * @var RmsUploadModel
     */
    public $rmsUpload;

    /**
     * @var ImportRmsCsvForm
     */
    protected $_form;

    /**
     * @var string
     */
    public $errorMessages;

    /**
     * @var array
     */
    public $invalidRows = [];

    /** @var  RmsUploadRowMapper */
    private $rmsUploadRowMapper;
    /** @var  RmsExcludedRowMapper */
    private $rmsExcludedRowMapper;
    /** @var  DeviceInstanceMeterMapper */
    private $deviceInstanceMeterMapper;
    /** @var  RmsDeviceMapper */
    private $rmsDeviceMapper;


    /**
     * @param int                 $userId
     * @param int                 $dealerId
     * @param int                 $clientId
     * @param RmsUploadModel|null $rmsUpload
     */
    public function __construct ($userId, $dealerId, $clientId, $rmsUpload = null)
    {
        $this->_userId   = $userId;
        $this->_dealerId = $dealerId;
        $this->_clientId = $clientId;

        if ($rmsUpload instanceof RmsUploadModel)
        {
            $this->rmsUpload = $rmsUpload;
        }
    }

    /**
     * @return RmsUploadRowMapper
     */
    public function getRmsUploadRowMapper()
    {
        if (!$this->rmsUploadRowMapper) {
            $this->rmsUploadRowMapper  = RmsUploadRowMapper::getInstance();
            $this->rmsUploadRowMapper->clearItemCache();
        }
        return $this->rmsUploadRowMapper;
    }

    /**
     * @return RmsExcludedRowMapper
     */
    public function getRmsExcludedRowMapper()
    {
        if (!$this->rmsExcludedRowMapper) {
            $this->rmsExcludedRowMapper  = RmsExcludedRowMapper::getInstance();
            $this->rmsExcludedRowMapper->clearItemCache();
        }
        return $this->rmsExcludedRowMapper;
    }

    /**
     * @return DeviceInstanceMeterMapper
     */
    public function getDeviceInstanceMeterMapper()
    {
        if (!$this->deviceInstanceMeterMapper) {
            $this->deviceInstanceMeterMapper  = DeviceInstanceMeterMapper::getInstance();
            $this->deviceInstanceMeterMapper->clearItemCache();
        }
        return $this->deviceInstanceMeterMapper;
    }

    /**
     * @return RmsDeviceMapper
     */
    public function getRmsDeviceMapper()
    {
        if (!$this->rmsDeviceMapper) {
            $this->rmsDeviceMapper  = RmsDeviceMapper::getInstance();
            $this->rmsDeviceMapper->clearItemCache();
        }
        return $this->rmsDeviceMapper;
    }



    /**
     * Process upload will take MPSToolbox\Legacy\Modules\ProposalGenerator\Forms\ImportRmsCsvForm values and process the upload,
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
        $this->_dealerId = $dealerId;
        $importSuccessful = false;

        $this->_fileName = $this->getForm()->getUploadedFilename();
        if ($this->getForm()->isValid($data) && $this->_fileName !== false)
        {
            $values               = $this->getForm()->getValues();
            $this->_rmsProviderId = $values ['rmsProviderId'];

            // Get the appropriate service based on the RMS provider
            switch ((int)$this->_rmsProviderId)
            {
                case RmsProviderModel::RMS_PROVIDER_PRINTFLEET_THREE:
                    $uploadProviderId = RmsProviderModel::RMS_PROVIDER_PRINTFLEET_THREE;
                    $uploadCsvService = new PrintFleetVersionThreeUploadService();
                    break;
                case RmsProviderModel::RMS_PROVIDER_PRINTFLEET_TWO:
                    $uploadProviderId = RmsProviderModel::RMS_PROVIDER_PRINTFLEET_TWO;
                    $uploadCsvService = new PrintFleetVersionTwoUploadService();
                    break;
                case RmsProviderModel::RMS_PROVIDER_FMAUDIT:
                    $uploadProviderId = RmsProviderModel::RMS_PROVIDER_FMAUDIT;
                    $uploadCsvService = new FmAuditUploadService();
                    break;
                case RmsProviderModel::RMS_PROVIDER_FMAUDIT_FOUR:
                    $uploadProviderId = RmsProviderModel::RMS_PROVIDER_FMAUDIT_FOUR;
                    $uploadCsvService = new FmAuditVersionFourUploadService();
                    break;
                case RmsProviderModel::RMS_PROVIDER_XEROX:
                    $uploadProviderId = RmsProviderModel::RMS_PROVIDER_XEROX;
                    $uploadCsvService = new XeroxUploadService();
                    break;
                case RmsProviderModel::RMS_PROVIDER_PRINT_AUDIT:
                    $uploadProviderId = RmsProviderModel::RMS_PROVIDER_PRINT_AUDIT;
                    $uploadCsvService = new PrintAuditUploadService();
                    break;
                case RmsProviderModel::RMS_PROVIDER_NER_DATA:
                    $uploadProviderId = RmsProviderModel::RMS_PROVIDER_NER_DATA;
                    $uploadCsvService = new NerDataUploadService();
                    break;
                case RmsProviderModel::RMS_PROVIDER_PRINT_TRACKER:
                    $uploadProviderId = RmsProviderModel::RMS_PROVIDER_PRINT_TRACKER;
                    $uploadCsvService = new PrintTrackerUploadService();
                    break;
                case RmsProviderModel::RMS_PROVIDER_LEXMARK:
                    $uploadProviderId = RmsProviderModel::RMS_PROVIDER_LEXMARK;
                    $uploadCsvService = new LexmarkRmsUploadService();
                    break;
                default :
                    $uploadCsvService    = null;
                    $uploadProviderId    = null;
                    $this->errorMessages = "Invalid RMS Provider Selected";
                    break;
            }

            if ($uploadCsvService instanceof AbstractRmsUploadService)
            {

                $db = Zend_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try
                {
                    /**
                     * Store our old upload in case we need it.
                     */
                    $oldRmsUpload = null;
                    if ($this->rmsUpload instanceof RmsUploadModel)
                    {
                        $oldRmsUpload = $this->rmsUpload;
                    }

                    $this->rmsUpload                  = new RmsUploadModel();
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
                        $this->invalidRows                = $uploadCsvService->invalidCsvLines;

                        if ($this->rmsUpload->validRowCount < 2)
                        {
                            $db->rollBack();
                            $this->errorMessages = "Your file had less than 2 valid rows in it. We require that you have 2 or more valid rows to upload a file";
                        }
                        else
                        {

                            if ($oldRmsUpload instanceof RmsUploadModel)
                            {
                                /**
                                 * Delete all previously uploaded lines
                                 */
                                $this->getRmsUploadRowMapper()->deleteAllForRmsUpload($oldRmsUpload->id);
                                $this->getRmsExcludedRowMapper()->deleteAllForRmsUpload($oldRmsUpload->id);
                                RmsUploadMapper::getInstance()->delete($oldRmsUpload);
                            }


                            if ($this->rmsUpload->id > 0)
                            {
                                RmsUploadMapper::getInstance()->save($this->rmsUpload);
                            }
                            else
                            {
                                RmsUploadMapper::getInstance()->insert($this->rmsUpload);
                            }


                            /**
                             * Valid Lines
                             */
                            /**
                             * @var DeviceInstanceModel[]
                             */
                            $deviceInstances = [];

                            foreach ($uploadCsvService->validCsvLines as $line)
                            {
                                $deviceInstances[] = $this->rmsUploadLine($line, $uploadProviderId);
                            }

                            $this->mapDeviceInstances($deviceInstances);

                            /**
                             * Invalid Lines
                             */
                            foreach ($uploadCsvService->invalidCsvLines as $line)
                            {

                                $rmsExcludedRow = new RmsExcludedRowModel($line->toArray());

                                // Set values that have different names than in $line
                                $rmsExcludedRow->manufacturerName = $line->manufacturer;
                                $rmsExcludedRow->rmsUploadId      = $this->rmsUpload->id;
                                $rmsExcludedRow->reason           = $line->validationErrorMessage;

                                // Set values that are none existent in $line
                                $rmsExcludedRow->rmsProviderId = $uploadProviderId;
                                RmsExcludedRowMapper::getInstance()->insert($rmsExcludedRow);
                            }

                            $db->commit();

                            /**
                             * @var $deviceInstance DeviceInstanceModel
                             */
                            foreach ($deviceInstances as $deviceInstance)
                            {
                                $deviceInstance             = DeviceInstanceMapper::getInstance()->find($deviceInstance->id);
                                $deviceInstanceMasterDevice = DeviceInstanceMasterDeviceMapper::getInstance()->find($deviceInstance->id);

                                if ($deviceInstanceMasterDevice instanceof DeviceInstanceMasterDeviceModel)
                                {
                                    $masterDevice                             = $deviceInstanceMasterDevice->getMasterDevice();
                                    if ($masterDevice instanceof MasterDeviceModel) {
                                        $deviceInstance->isLeased = $masterDevice->isLeased($this->_dealerId);
                                        $deviceInstance->compatibleWithJitProgram = $masterDevice->isJitCompatible($this->_dealerId);

                                        if ($deviceInstance->rmsDeviceInstanceId) {
                                            /** @var RmsDeviceInstanceEntity $rmsDeviceInstanceEntity */
                                            $rmsDeviceInstanceEntity = RmsDeviceInstanceEntity::find($deviceInstance->rmsDeviceInstanceId);
                                            $rmsDeviceInstanceEntity->setMasterDevice(MasterDeviceEntity::find($masterDevice->id));
                                            $rmsDeviceInstanceEntity->save();
                                        }

                                    }
                                    DeviceInstanceMapper::getInstance()->save($deviceInstance);
                                }
                            }

                            $importSuccessful = true;
                        }
                    }
                    else
                    {
                        $db->rollBack();
                        $this->errorMessages = "There was an error importing your file. $processCsvMessage";
                    }
                }
                catch (Exception $e)
                {
                    Logger::logException($e);
                    $this->errorMessages = "There was an error parsing your file. If this continues to happen please reference this id when requesting support: " . Logger::getUniqueId();
                    $db->rollBack();
                }
            }
        }

        return $importSuccessful;
    }

    /*
    * Perform Mapping
    */
    public function mapDeviceInstances($deviceInstances) {
        $deviceMappingService = new DeviceMappingService();
        $deviceMappingService->mapDevices($deviceInstances, $this->_userId, true);
    }

    /*
     * Convert line into device instance, upload row, and meters
     */
    protected function rmsUploadLine(UploadLineModel $line, $uploadProviderId) {
        $lineArray = $line->toArray();

        /*
         * Check and insert RMS device
         */
        if (strlen($line->rmsModelId) > 0)
        {
            $rmsDevice = $this->getRmsDeviceMapper()->find([$uploadProviderId, $line->rmsModelId]);
            if ($rmsDevice instanceof RmsDeviceModel)
            {
                if ($rmsDevice->isGeneric)
                {
                    $line->rmsModelId = null;
                }
                $lineArray = $line->toArray();
            }
            else
            {
                $rmsDevice                = new RmsDeviceModel($lineArray);
                $rmsDevice->rmsProviderId = $uploadProviderId;
                $rmsDevice->dateCreated   = new Zend_Db_Expr("NOW()");
                $rmsDevice->userId        = $this->_userId;
                $this->getRmsDeviceMapper()->insert($rmsDevice);
            }

        }

        /*
         * Save RMS Upload Row
         */
        $rmsUploadRow                 = new RmsUploadRowModel($lineArray);
        $rmsUploadRow->fullDeviceName = "{$line->manufacturer} {$line->modelName}";
        $rmsUploadRow->rmsProviderId  = $uploadProviderId;

        // Lets make an attempt at finding the manufacturer
        $manufacturers = ManufacturerMapper::getInstance()->searchByName($rmsUploadRow->manufacturer);
        if ($manufacturers && count($manufacturers) > 0)
        {
            $rmsUploadRow->manufacturerId = $manufacturers[0]->id;
        }

        try
        {
            $this->getRmsUploadRowMapper()->insert($rmsUploadRow);
        }
        catch (Exception $e)
        {
            if (isset($rmsDevice) && $rmsDevice instanceof RmsDeviceModel)
            {
                Logger::crit(print_r($rmsDevice->toArray(), true));
            }
            Logger::crit(print_r($rmsUploadRow->toArray(), true));
            throw $e;
        }


        /**/
        $rmsDeviceInstance = RmsDeviceInstanceEntity::findOne($this->rmsUpload->clientId, $line->ipAddress, $line->serialNumber, $line->assetId);
        if (!$rmsDeviceInstance) {
            $rmsDeviceInstance = new RmsDeviceInstanceEntity();
            $rmsDeviceInstance->setClient(ClientEntity::find($this->rmsUpload->clientId));
            $rmsDeviceInstance->setIpAddress(''.$line->ipAddress);
            $rmsDeviceInstance->setSerialNumber(''.$line->serialNumber);
            $rmsDeviceInstance->setAssetId(''.$line->assetId);
            $rmsDeviceInstance->setFullDeviceName($line->manufacturer.' '.$line->modelName);
            $rmsDeviceInstance->setRawDeviceName($line->rawDeviceName);
            $rmsDeviceInstance->setLocation($line->location);
            $rmsDeviceInstance->setManufacturer($line->manufacturer);
            $rmsDeviceInstance->setModelName($line->modelName);
            $rmsDeviceInstance->setReportDate(new \DateTime($line->monitorEndDate));
            $rmsDeviceInstance->save();
        }

        /*
         * Save Device Instance
         */
        $deviceInstance                 = new DeviceInstanceModel($lineArray);
        $deviceInstance->rmsUploadId    = $this->rmsUpload->id;
        $deviceInstance->rmsUploadRowId = $rmsUploadRow->id;
        $deviceInstance->rmsDeviceInstanceId = $rmsDeviceInstance->getId();
        DeviceInstanceMapper::getInstance()->insert($deviceInstance);

        $deviceInstances[] = $deviceInstance;

        /*
         * Save Meters
         */

        $meter                   = new DeviceInstanceMeterModel();
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

        $this->getDeviceInstanceMeterMapper()->insert($meter);

        return $deviceInstance;
    }

    /**
     * @return ImportRmsCsvForm
     */
    public function getForm ()
    {
        if (!isset($this->_form))
        {
            $this->_form = new ImportRmsCsvForm($this->_dealerId, ['csv'], "1B", "8MB");
        }

        return $this->_form;
    }

    /**
     * Getter for _fileName
     *
     * @return string
     */
    public function getFileName ()
    {
        return $this->_fileName;
    }

    public function fromRealtime($from, $until) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "select * from rms_realtime r join rms_device_instances i on r.rmsDeviceInstanceId=i.id where clientId={$this->_clientId} and (scanDate='{$from}' or scanDate='{$until}') order by scanDate";

        $st = $db->query($sql);
        $rmsProviderId = null;
        $data=[];
        foreach ($st->fetchAll() as $line) {
            $id = $line['rmsDeviceInstanceId'];
            $data[$id][$line['scanDate']] = $line;
            $rmsProviderId = $line['rmsProviderId'];
        }
        $invalid = 0;
        foreach ($data as $id=>$row) {
            if (count($row)!=2) {
                $invalid++;
                unset($data[$id]);
            }
        }
        if (count($data)==0) {
            throw new \InvalidArgumentException('No data found');
        }

        $this->rmsUpload = new RmsUploadModel();
        $this->rmsUpload->clientId = $this->_clientId;
        $this->rmsUpload->fileName = 'Real Time Data from '.(date('d-M-Y',strtotime($from))).' until '.(date('d-M-Y',strtotime($until)));
        $this->rmsUpload->invalidRowCount = $invalid;
        $this->rmsUpload->validRowCount = count($data);
        $this->rmsUpload->uploadDate = date('Y-m-d H:i:s');
        $this->rmsUpload->rmsProviderId = $rmsProviderId;

        RmsUploadMapper::getInstance()->insert($this->rmsUpload);

        $di = [];

        foreach (array_values($data) as $i=>$row) {
            $from = array_shift($row);
            $until = array_shift($row);
            $line = new UploadLineModel();

            $masterDevice = null;
            if ($from['masterDeviceId']) {
                $masterDevice = MasterDeviceMapper::getInstance()->find($from['masterDeviceId']);
            }

            $line->isManaged = null;
            $line->managementProgram = null;
            $line->rmsVendorName = null;
            $line->rmsReportVersion = null;
            $line->rmsDeviceId = null;
            $line->rmsModelId = $from['modelName'];
            $line->assetId = $from['assetId'];
            $line->monitorStartDate= $from['scanDate'];
            $line->monitorEndDate= $until['scanDate'];
            $line->adoptionDate = null;
            $line->cost = null;
            $line->discoveryDate = null;
            $line->launchDate = $masterDevice ? $masterDevice->launchDate : null;
            $line->leasedTonerYield = null;
            $line->ipAddress = $from['ipAddress'];
            $line->isColor = $masterDevice ? $masterDevice->isColor : null;
            $line->isCopier = $masterDevice ? $masterDevice->isCopier : null;
            $line->isFax = $masterDevice ? $masterDevice->isFax : null;
            $line->isA3 = $masterDevice ? $masterDevice->isA3 : null;
            $line->isDuplex = $masterDevice ? $masterDevice->isDuplex : null;
            $line->manufacturer = $from['manufacturer'];
            $line->rawDeviceName = $from['rawDeviceName'];
            $line->modelName = $from['modelName'];
            $line->ppmBlack = $masterDevice ? $masterDevice->ppmBlack : null;
            $line->ppmColor = $masterDevice ? $masterDevice->ppmColor : null;
            $line->partsCostPerPage = null;
            $line->laborCostPerPage = null;
            $line->serialNumber= $from['serialNumber'];
            $line->wattsPowerNormal = $masterDevice ? $masterDevice->wattsPowerNormal : null;
            $line->wattsPowerIdle = $masterDevice ? $masterDevice->wattsPowerIdle : null;
            $line->startMeterBlack = $from['lifeCountBlack'];
            $line->endMeterBlack = $until['lifeCountBlack'];
            $line->startMeterColor = $from['lifeCountColor'];
            $line->endMeterColor = $until['lifeCountColor'];
            $line->startMeterLife = $from['lifeCount'];
            $line->endMeterLife = $until['lifeCount'];
            $line->startMeterPrintBlack = $from['printCountBlack'];
            $line->endMeterPrintBlack = $until['printCountBlack'];
            $line->startMeterPrintColor = $from['printCountColor'];
            $line->endMeterPrintColor = $until['printCountColor'];
            $line->startMeterCopyBlack = $from['copyCountBlack'];
            $line->endMeterCopyBlack = $until['copyCountBlack'];
            $line->startMeterCopyColor = $from['copyCountColor'];
            $line->endMeterCopyColor = $until['copyCountColor'];
            $line->startMeterScan = $from['scanCount'];
            $line->endMeterScan = $until['scanCount'];
            $line->startMeterFax = $from['faxCount'];
            $line->endMeterFax= $until['faxCount'];
            $line->startMeterPrintA3Black = null;
            $line->endMeterPrintA3Black = null;
            $line->startMeterPrintA3Color = null;
            $line->endMeterPrintA3Color = null;
            $line->reportsTonerLevels = !empty($from['tonerLevelBlack']);
            $line->tonerLevelBlack = $until['tonerLevelBlack'];
            $line->tonerLevelCyan = $until['tonerLevelCyan'];
            $line->tonerLevelMagenta = $until['tonerLevelMagenta'];
            $line->tonerLevelYellow = $until['tonerLevelYellow'];
            $line->isValid = 1;
            $line->validationErrorMessage = '';
            $line->hasCompleteInformation = '0';
            $line->csvLineNumber = $i+1;
            $line->tonerConfigId = $masterDevice ? $masterDevice->tonerConfigId : null;
            $line->pageCoverageMonochrome = null;
            $line->pageCoverageColor = null;
            $line->pageCoverageCyan = null;
            $line->pageCoverageMagenta = null;
            $line->pageCoverageYellow = null;
            $line->location = $from['location'];

            $instance = $this->rmsUploadLine($line, $rmsProviderId);
            if ($masterDevice) $instance->setMasterDevice($masterDevice);

            $di[] = $instance;
        }
        $this->mapDeviceInstances($di);
    }

}