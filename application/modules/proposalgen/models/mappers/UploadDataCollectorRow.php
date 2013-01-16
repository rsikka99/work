<?php

class Proposalgen_Model_Mapper_UploadDataCollectorRow extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_UploadDataCollectorRow";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_UploadDataCollectorRow
     */
    public static function getInstance ()
    {
        if (!isset(self::$_instance))
        {
            $className       = get_class();
            self::$_instance = new $className();
        }

        return self::$_instance;
    }

    /**
     * Counts how many csv rows have been uploaded for a given report
     *
     * @param int     $reportId
     *            The report id to check
     * @param boolean $checkForBadData
     *            If set to true it will check to see if a report has bad uploaded
     *            data
     *
     * @return int Returns the number of upload data collector rows for a given report
     */
    public function countUploadDataCollectorRowsForReport ($reportId, $checkForBadData = false)
    {
        if ($reportId instanceof Proposalgen_Model_Report)
        {
            $reportId = $reportId->getId();
        }

        $dbTable   = $this->getDbTable();
        $db        = $dbTable->getAdapter();
        $tableName = $dbTable->info('name');

        if ($checkForBadData)
        {
            $result = $db->fetchOne("SELECT COUNT(*) AS count FROM {$tableName} WHERE report_id = ? AND invalid_data = 1", $reportId);
        }
        else
        {
            $result = $db->fetchOne("SELECT COUNT(*) AS count FROM {$tableName} WHERE report_id = ?", $reportId);
        }

        return $result;
    }

    /**
     * Deletes all upload data collector rows for a report
     *
     * @param number $reportId
     * @return int
     */
    public function deleteAllRowsForReport ($reportId)
    {
        return $this->delete(array(
                                  'report_id = ?' => $reportId
                             ));
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row
     *
     * @throws Exception
     * @return Proposalgen_Model_UploadDataCollectorRow
     */
    public function mapRowToObject ($row)
    {
        if (is_array($row))
        {
            $row = new ArrayObject($row, ArrayObject::ARRAY_AS_PROPS);
        }

        $object = null;
        try
        {
            $object                        = new Proposalgen_Model_UploadDataCollectorRow();
            $object->uploadDataCollectorId = $row->id;
            $object->reportId              = $row->report_id;
            $object->devicesPfId           = $row->devices_pf_id;
            $object->StartDate             = $row->startdate;
            $object->EndDate               = $row->enddate;
            $object->PrinterModelId        = $row->printermodelid;
            $object->ipAddress             = $row->ipaddress;
            $object->serialNumber          = $row->serialnumber;
            $object->modelName             = $row->modelname;
            $object->manufacturer          = $row->manufacturer;
            $object->isColor               = $row->is_color;
            $object->isCopier              = $row->is_copier;
            $object->isScanner             = $row->is_scanner;
            $object->isFax                 = $row->is_fax;
            $object->ppmBlack              = $row->ppm_black;
            $object->ppmColor              = $row->ppm_color;
            $object->dateIntroduction      = $row->date_introduction;
            $object->discoveryDate         = $row->discovery_date;
            $object->blackProdCodeOem      = $row->black_prodcodeoem;
            $object->blackYield            = $row->black_yield;
            $object->blackProdCostOem      = $row->black_prodcostoem;
            $object->cyanProdCodeOem       = $row->cyan_prodcodeoem;
            $object->cyanYield             = $row->cyan_yield;
            $object->cyanProdCostOem       = $row->cyan_prodcostoem;
            $object->magentaProdCodeOem    = $row->magenta_prodcodeoem;
            $object->magentaYield          = $row->magenta_yield;
            $object->magentaProdCostOem    = $row->magenta_prodcostoem;
            $object->yellowProdCodeOem     = $row->yellow_prodcodeoem;
            $object->yellowYield           = $row->yellow_yield;
            $object->yellowProdCostOem     = $row->yellow_prodcostoem;
            $object->dutyCycle             = $row->duty_cycle;
            $object->wattsPowerNormal      = $row->wattspowernormal;
            $object->wattsPowerIdle        = $row->wattspoweridle;
            $object->startMeterLife        = $row->startmeterlife;
            $object->endMeterLife          = $row->endmeterlife;
            $object->startMeterBlack       = $row->startmeterblack;
            $object->endMeterBlack         = $row->endmeterblack;
            $object->startMeterColor       = $row->startmetercolor;
            $object->endMeterColor         = $row->endmetercolor;
            $object->startMeterPrintBlack  = $row->startmeterprintblack;
            $object->endMeterPrintBlack    = $row->endmeterprintblack;
            $object->startMeterPrintColor  = $row->startmeterprintcolor;
            $object->endMeterPrintColor    = $row->endmeterprintcolor;
            $object->startMeterCopyBlack   = $row->startmetercopyblack;
            $object->endMeterCopyBlack     = $row->endmetercopyblack;
            $object->startMeterCopyColor   = $row->startmetercopycolor;
            $object->endMeterCopyColor     = $row->endmetercopycolor;
            $object->startMeterFax         = $row->startmeterfax;
            $object->endMeterFax           = $row->endmeterfax;
            $object->startMeterScan        = $row->startmeterscan;
            $object->endMeterScan          = $row->endmeterscan;
            $object->invalidData           = (int)$row->invalid_data;
            $object->isExcluded            = (int)$row->is_excluded;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map an upload_data_collector row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_UploadDataCollectorRow $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data       = $this->mapObjectToRow($object);
            $primaryKey = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }

    /**
     * Maps an object into an array for the database
     *
     * @param Proposalgen_Model_UploadDataCollectorRow $object
     *
     * @return array
     */
    public function mapObjectToRow (Proposalgen_Model_UploadDataCollectorRow $object)
    {
        $data                          = array();
        $data ["id"]                   = $object->uploadDataCollectorId;
        $data ["report_id"]            = $object->reportId;
        $data ["devices_pf_id"]        = $object->devicesPfId;
        $data ["startdate"]            = $object->startDate;
        $data ["enddate"]              = $object->endDate;
        $data ["printermodelid"]       = $object->printerModelId;
        $data ["ipaddress"]            = $object->ipAddress;
        $data ["serialnumber"]         = $object->serialNumber;
        $data ["modelname"]            = $object->modelName;
        $data ["manufacturer"]         = $object->manufacturer;
        $data ["is_color"]             = $object->isColor;
        $data ["is_copier"]            = $object->isCopier;
        $data ["is_scanner"]           = $object->isScanner;
        $data ["is_fax"]               = $object->isFax;
        $data ["ppm_black"]            = $object->ppmBlack;
        $data ["ppm_color"]            = $object->ppmColor;
        $data ["date_introduction"]    = $object->dateIntroduction;
        $data ["discovery_date"]       = $object->discoveryDate;
        $data ["black_prodcodeoem"]    = $object->blackProdCodeOem;
        $data ["black_yield"]          = $object->blackYield;
        $data ["black_prodcostoem"]    = $object->blackProdCostOem;
        $data ["cyan_prodcodeoem"]     = $object->cyanProdCodeOem;
        $data ["cyan_yield"]           = $object->cyanYield;
        $data ["cyan_prodcostoem"]     = $object->cyanProdCostOem;
        $data ["magenta_prodcodeoem"]  = $object->magentaProdCodeOem;
        $data ["magenta_yield"]        = $object->magentaYield;
        $data ["magenta_prodcostoem"]  = $object->magentaProdCostOem;
        $data ["yellow_prodcodeoem"]   = $object->yellowProdCodeOem;
        $data ["yellow_yield"]         = $object->yellowYield;
        $data ["yellow_prodcostoem"]   = $object->yellowProdCostOem;
        $data ["duty_cycle"]           = $object->dutyCycle;
        $data ["wattspowernormal"]     = $object->wattsPowerNormal;
        $data ["wattspoweridle"]       = $object->wattsPowerIdle;
        $data ["startmeterlife"]       = $object->startMeterLife;
        $data ["endmeterlife"]         = $object->endMeterLife;
        $data ["startmeterblack"]      = $object->startMeterBlack;
        $data ["endmeterblack"]        = $object->endMeterBlack;
        $data ["startmetercolor"]      = $object->startMeterColor;
        $data ["endmetercolor"]        = $object->endMeterColor;
        $data ["startmeterprintblack"] = $object->startMeterBlack;
        $data ["endmeterprintblack"]   = $object->endMeterBlack;
        $data ["startmeterprintcolor"] = $object->startMeterPrintColor;
        $data ["endmeterprintcolor"]   = $object->endMeterPrintColor;
        $data ["startmetercopyblack"]  = $object->startMeterCopyBlack;
        $data ["endmetercopyblack"]    = $object->endMeterCopyBlack;
        $data ["startmetercopycolor"]  = $object->startMeterCopyColor;
        $data ["startmetercopycolor"]  = $object->endMeterCopyColor;
        $data ["startmeterscan"]       = $object->startMeterScan;
        $data ["endmeterscan"]         = $object->endMeterScan;
        $data ["startmeterfax"]        = $object->startMeterFax;
        $data ["endmeterfax"]          = $object->endMeterFax;
        $data ["invalid_data"]         = (int)$object->invalidData;
        $data ["is_excluded"]          = (int)$object->isExcluded;

        return $data;
    }

    public function getExcludedAsDeviceInstance ($reportId)
    {
        $devices = array();
        $results = $this->fetchAll(array(
                                        'is_excluded = 1 OR invalid_data = 1',
                                        "report_id = ?" => $reportId
                                   ));
        foreach ($results as $result)
        {

            $startDate     = new DateTime($result->StartDate);
            $endDate       = new DateTime($result->EndDate);
            $discoveryDate = new DateTime($result->DiscoveryDate);

            $interval1 = $startDate->diff($endDate);
            $interval2 = $discoveryDate->diff($endDate);

            $days = $interval1;
            if ($interval1->days > $interval2->days && !$interval2->invert)
            {
                $days = $interval2;
            }

            $device       = new Proposalgen_Model_DeviceInstance();
            $masterDevice = new Proposalgen_Model_MasterDevice();
            $manufacturer = new Proposalgen_Model_Manufacturer();

            $manufacturer->fullname = $result->Manufacturer;
            $manufacturer->displayname = $result->Manufacturer;
            $masterDevice->setPrinterModel(trim(str_replace($result->Manufacturer, '', $result->ModelName)));
            $masterDevice->setManufacturer($manufacturer);
            $device->setMasterDevice($masterDevice);

            $device->setDeviceName($result->ModelName);
            $device->setIpAddress($result->IpAddress);
            $device->setSerialNumber($result->SerialNumber);

            //device must be at least 4 days old
            if ($days->days < 4)
            {
                $device->setExclusionReason('Insufficient Monitor Data');
            }
            else if ($result->IsExcluded == true)
            {
                $device->setIsExcluded(true);
            }
            else if (strlen($result->Manufacturer) == 0)
            {
                $device->setExclusionReason('Missing mfg information');
            }
            else if (strlen($result->ModelName) == 0)
            {
                $device->setExclusionReason('Missing model information');
            }
            else
            {
                $device->setExclusionReason('Incompatible with PrintIQ ');
            }
            $devices [] = $device;
        }

        return $devices;
    }
}
