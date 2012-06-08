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
        if (! isset(self::$_instance))
        {
            $className = get_class();
            self::$_instance = new $className();
        }
        return self::$_instance;
    }

    /**
     * Counts how many csv rows have been uploaded for a given report
     *
     * @param int $reportId
     *            The report id to check
     * @param boolean $checkForBadData
     *            If set to true it will check to see if a report has bad uploaded
     *            data
     * @return int Returns the number of upload data collector rows for a given report
     */
    public function countUploadDataCollectorRowsForReport ($reportId, $checkForBadData = false)
    {
        if ($reportId instanceof Proposalgen_Model_Report)
        {
            $reportId = $reportId->getReportId();
        }
        
        $dbTable = $this->getDbTable();
        $db = $dbTable->getAdapter();
        $tableName = $dbTable->info('name');
        $result = 0;
        if ($checkForBadData)
        {
            $result = $db->fetchOne("SELECT COUNT(*) AS count FROM {$tableName} WHERE report_id = ? AND invalid_data = 1", $reportId);
        }
        else
        {
            $result = $db->fetchOne("SELECT COUNT(*) AS count FROM {$tableName} WHERE report_id = ?", $reportId);
        }
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row            
     * @return Proposalgen_Model_UploadDataCollectorRow
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_UploadDataCollectorRow();
            $object->setUploadDataCollectorId($row->id)
                ->setReportId($row->report_id)
                ->setDevicesPfId($row->devices_pf_id)
                ->setStartDate($row->startdate)
                ->setEndDate($row->enddate)
                ->setPrinterModelId($row->printermodelid)
                ->setIpAddress($row->ipaddress)
                ->setSerialNumber($row->serialnumber)
                ->setModelName($row->modelname)
                ->setManufacturer($row->manufacturer)
                ->setIsColor($row->is_color)
                ->setIsCopier($row->is_copier)
                ->setIsScanner($row->is_scanner)
                ->setIsFax($row->is_fax)
                ->setPpmBlack($row->ppm_black)
                ->setPpmColor($row->ppm_color)
                ->setDateIntroduction($row->date_introduction)
                ->setDiscoveryDate($row->discovery_date)
                ->setBlackProdCodeOem($row->black_prodcodeoem)
                ->setBlackYield($row->black_yield)
                ->setBlackProdCostOem($row->black_prodcostoem)
                ->setCyanProdCodeOem($row->cyan_prodcodeoem)
                ->setCyanYield($row->cyan_yield)
                ->setCyanProdCostOem($row->cyan_prodcostoem)
                ->setMagentaProdCodeOem($row->magenta_prodcodeoem)
                ->setMagentaYield($row->magenta_yield)
                ->setMagentaProdCostOem($row->magenta_prodcostoem)
                ->setYellowProdCodeOem($row->yellow_prodcodeoem)
                ->setYellowYield($row->yellow_yield)
                ->setYellowProdCostOem($row->yellow_prodcostoem)
                ->setDutyCycle($row->duty_cycle)
                ->setWattsPowerNormal($row->wattspowernormal)
                ->setWattsPowerIdle($row->wattspoweridle)
                ->setStartMeterLife($row->startmeterlife)
                ->setEndMeterLife($row->endmeterlife)
                ->setStartMeterBlack($row->startmeterblack)
                ->setEndMeterBlack($row->endmeterblack)
                ->setStartMeterColor($row->startmetercolor)
                ->setEndMeterColor($row->endmetercolor)
                ->setStartMeterPrintblack($row->startmeterprintblack)
                ->setEndMeterPrintblack($row->endmeterprintblack)
                ->setStartMeterPrintcolor($row->startmeterprintcolor)
                ->setEndMeterPrintcolor($row->endmeterprintcolor)
                ->setStartMeterCopyblack($row->startmetercopyblack)
                ->setEndMeterCopyblack($row->endmetercopyblack)
                ->setStartMeterCopycolor($row->startmetercopycolor)
                ->setEndMeterCopycolor($row->endmetercopycolor)
                ->setStartMeterFax($row->startmeterfax)
                ->setEndMeterFax($row->endmeterfax)
                ->setStartMeterScan($row->startmeterscan)
                ->setEndMeterScan($row->endmeterscan)
                ->setInvalidData($row->invalid_data)
                ->setIsExcluded($row->is_excluded);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map an upload_data_collector row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_UploadDataCollectorRow $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["id"] = $object->getUploadDataCollectorId();
            $data ["report_id"] = $object->getReportId();
            $data ["devices_pf_id"] = $object->getDevicesPfId();
            $data ["startdate"] = $object->getStartdate();
            $data ["enddate"] = $object->getEnddate();
            $data ["printermodelid"] = $object->getPrinterModelid();
            $data ["ipaddress"] = $object->getIpAddress();
            $data ["serialnumber"] = $object->getSerialNumber();
            $data ["modelname"] = $object->getModelName();
            $data ["manufacturer"] = $object->getManufacturer();
            $data ["is_color"] = $object->getIsColor();
            $data ["is_copier"] = $object->getIsCopier();
            $data ["is_scanner"] = $object->getIsScanner();
            $data ["is_fax"] = $object->getIsFax();
            $data ["ppm_black"] = $object->getPpmBlack();
            $data ["ppm_color"] = $object->getPpmColor();
            $data ["date_introduction"] = $object->getDateIntroduction();
            $data ["discovery_date"] = $object->getDiscoveryDate();
            $data ["black_prodcodeoem"] = $object->getBlackProdCodeOem();
            $data ["black_yield"] = $object->getBlackYield();
            $data ["black_prodcostoem"] = $object->getBlackProdCostOem();
            $data ["cyan_prodcodeoem"] = $object->getCyanProdCodeOem();
            $data ["cyan_yield"] = $object->getCyanYield();
            $data ["cyan_prodcostoem"] = $object->getCyanProdCostOem();
            $data ["magenta_prodcodeoem"] = $object->getMagentaProdCodeOem();
            $data ["magenta_yield"] = $object->getMagentaYield();
            $data ["magenta_prodcostoem"] = $object->getMagentaProdCostOem();
            $data ["yellow_prodcodeoem"] = $object->getYellowProdCodeOem();
            $data ["yellow_yield"] = $object->getYellowYield();
            $data ["yellow_prodcostoem"] = $object->getYellowProdCostOem();
            $data ["duty_cycle"] = $object->getDutyCycle();
            $data ["watts_power_normal"] = $object->getWattsPowerNormal();
            $data ["watts_power_idle"] = $object->getWattsPowerIdle();
            $data ["startmeterlife"] = $object->getStartMeterLife();
            $data ["endmeterlife"] = $object->getEndMeterLife();
            $data ["startmeterblack"] = $object->getStartMeterBlack();
            $data ["endmeterblack"] = $object->getEndMeterBlack();
            $data ["startmetercolor"] = $object->getStartMeterColor();
            $data ["endmetercolor"] = $object->getEndMeterColor();
            $data ["startmeterprintblack"] = $object->getStartMeterBlack();
            $data ["endmeterprintblack"] = $object->getEndMeterBlack();
            $data ["startmeterprintcolor"] = $object->getStartMeterPrintcolor();
            $data ["endmeterprintcolor"] = $object->getEndtMeterPrintcolor();
            $data ["startmetercopyblack"] = $object->getStartMeterCopyblack();
            $data ["endmetercopyblack"] = $object->getEndMeterCopyblack();
            $data ["startmetercopycolor"] = $object->getStartMeterCopycolor();
            $data ["startmetercopycolor"] = $object->getEndMeterCopycolor();
            $data ["startmeterscan"] = $object->getStartMeterScan();
            $data ["endmeterscan"] = $object->getEndMeterScan();
            $data ["startmeterfax"] = $object->getStartMeterFax();
            $data ["endmeterfax"] = $object->getEndMeterFax();
            $data ["invalid_data"] = $object->getInvalidData();
            $data ["is_excluded"] = $object->getIsExcluded();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }

    public function getExcludedAsDeviceInstance ($reportId)
    {
        $devices = array ();
        $results = $this->fetchAll(array (
                'is_excluded = 1 OR invalid_data = 1', 
                "report_id = ?" => $reportId 
        ));
        foreach ( $results as $result )
        {
            
            $startDate = new DateTime($result->StartDate);
            $endDate = new DateTime($result->EndDate);
            $discoveryDate = new DateTime($result->DiscoveryDate);
            
            $interval1 = $startDate->diff($endDate);
            $interval2 = $discoveryDate->diff($endDate);
            
            $days = $interval1;
            if ($interval1->days > $interval2->days && ! $interval2->invert)
            {
                $days = $interval2;
            }
            
            $device = new Proposalgen_Model_DeviceInstance();
            $masterDevice = new Proposalgen_Model_MasterDevice();
            $manufacturer = new Proposalgen_Model_Manufacturer();
            
            $manufacturer->setManufacturerName($result->Manufacturer);
            $masterDevice->setPrinterModel(trim(str_replace($manufacturer->getManufacturerName(), '', $result->ModelName)));
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
