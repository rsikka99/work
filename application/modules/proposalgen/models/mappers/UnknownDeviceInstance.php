<?php

class Proposalgen_Model_Mapper_UnknownDeviceInstance extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_UnknownDeviceInstance";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_UnknownDeviceInstance
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
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row            
     * @return Proposalgen_Model_UnknownDeviceInstance
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_UnknownDeviceInstance();
            $object->setId($row->id)
                ->setUserId($row->user_id)
                ->setReportId($row->report_id)
                ->setUploadDataCollectorRowId($row->upload_data_collector_id)
                ->setPrinterModelid($row->printermodelid)
                ->setMpsMonitorStartdate($row->mps_monitor_startdate)
                ->setMpsMonitorEnddate($row->mps_monitor_enddate)
                ->setMpsDiscoveryDate($row->mps_discovery_date)
                ->setInstallDate($row->install_date)
                ->setDeviceManufacturer($row->device_manufacturer)
                ->setPrinterModel($row->printer_model)
                ->setPrinterSerialNumber($row->printer_serial_number)
                ->setTonerConfig($row->toner_config)
                ->setIsCopier($row->is_copier)
                ->setIsFax($row->is_fax)
                ->setIsDuplex($row->is_duplex)
                ->setIsScanner($row->is_scanner)
                ->setJitSuppliesSupported($row->jit_supplies_supported)
                ->setWattsPowerNormal($row->watts_power_normal)
                ->setWattsPowerIdle($row->watts_power_idle)
                ->setDevicePrice($row->device_price)
                ->setLaunchDate($row->launch_date)
                ->setDateCreated($row->date_created)
                ->setBlackTonerSKU($row->black_toner_SKU)
                ->setBlackTonerPrice($row->black_toner_price)
                ->setBlackTonerYield($row->black_toner_yield)
                ->setCyanTonerSKU($row->cyan_toner_SKU)
                ->setCyanTonerPrice($row->cyan_toner_price)
                ->setCyanTonerYield($row->cyan_toner_yield)
                ->setMagentaTonerSKU($row->magenta_toner_SKU)
                ->setMagentaTonerPrice($row->magenta_toner_price)
                ->setMagentaTonerYield($row->magenta_toner_yield)
                ->setYellowTonerSKU($row->yellow_toner_SKU)
                ->setYellowTonerPrice($row->yellow_toner_price)
                ->setYellowTonerYield($row->yellow_toner_yield)
                ->setThreeColorTonerSKU($row ['3color_toner_SKU'])
                ->setThreeColorTonerPrice($row ['3color_toner_price'])
                ->setThreeColorTonerYield($row ['3color_toner_yield'])
                ->setFourColorTonerSKU($row ['4color_toner_SKU'])
                ->setFourColorTonerPrice($row ['4color_toner_price'])
                ->setFourColorTonerYield($row ['4color_toner_yield'])
                ->setBlackCompSKU($row->black_comp_SKU)
                ->setBlackCompPrice($row->black_comp_price)
                ->setBlackCompYield($row->black_comp_yield)
                ->setCyanCompSKU($row->cyan_comp_SKU)
                ->setCyanCompPrice($row->cyan_comp_price)
                ->setCyanCompYield($row->cyan_comp_yield)
                ->setMagentaCompSKU($row->magenta_comp_SKU)
                ->setMagentaCompPrice($row->magenta_comp_price)
                ->setMagentaCompYield($row->magenta_comp_yield)
                ->setYellowCompSKU($row->yellow_comp_SKU)
                ->setYellowCompPrice($row->yellow_comp_price)
                ->setYellowCompYield($row->yellow_comp_yield)
                ->setThreeColorCompSKU($row ['3color_comp_SKU'])
                ->setThreeColorCompPrice($row ['3color_comp_price'])
                ->setThreeColorCompYield($row ['3color_comp_yield'])
                ->setFourColorCompSKU($row ['4color_comp_SKU'])
                ->setFourColorCompPrice($row ['4color_comp_price'])
                ->setFourColorCompYield($row ['4color_comp_yield'])
                ->setStartMeterLife($row->start_meter_life)
                ->setEndMeterLife($row->end_meter_life)
                ->setStartMeterBlack($row->start_meter_black)
                ->setEndMeterBlack($row->end_meter_black)
                ->setStartMeterColor($row->start_meter_color)
                ->setEndMeterColor($row->end_meter_color)
                ->setStartMeterPrintblack($row->start_meter_printblack)
                ->setEndMeterPrintblack($row->end_meter_printblack)
                ->setStartMeterPrintcolor($row->start_meter_printcolor)
                ->setEndMeterPrintcolor($row->end_meter_printcolor)
                ->setStartMeterCopyblack($row->start_meter_copyblack)
                ->setEndMeterCopyblack($row->end_meter_copyblack)
                ->setStartMeterCopycolor($row->start_meter_copycolor)
                ->setEndMeterCopycolor($row->end_meter_copycolor)
                ->setStartMeterFax($row->start_meter_fax)
                ->setEndMeterFax($row->end_meter_fax)
                ->setStartMeterScan($row->start_meter_scan)
                ->setEndMeterScan($row->end_meter_scan)
                ->setIsExcluded($row->is_excluded)
                ->setIsLeased($row->is_leased)
                ->setIpAddress($row->ip_address)
                ->setServiceCostPerPage($row->service_cost_per_page);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map an unknown device instance row", 0, $e);
        }
        return $object;
    }

    /**
     * Fetches all unknown devices and puts them in the same format as normal devices
     *
     * @param unknown_type $reportId            
     */
    public function fetchAllUnknownDevicesAsKnownDevices ($reportId, $whereClause)
    {
        $deviceList = array ();
        try
        {
            $unknowndevices = $this->getDbTable()->fetchAll($whereClause);
            if ($unknowndevices)
            {
                foreach ( $unknowndevices as $row )
                {
                    $device = new Proposalgen_Model_DeviceInstance();
                    
                    $manufacturer = new Proposalgen_Model_Manufacturer();
                    $manufacturer->setManufacturerName($row->device_manufacturer);
                    
                    $tonerConfigMapper = Proposalgen_Model_Mapper_TonerConfig::getInstance();
                    $tonerConfig = $tonerConfigMapper->find($row->toner_config_id);
                    
                    // Get toner list based on the config
                    $toners = $this->getUnknownDeviceToners($row, $manufacturer);
                    
                    $meters = array ();
                    // Form a list of meters
                    $meterColumns = array (
                            "life" => Proposalgen_Model_Meter::METER_TYPE_LIFE, 
                            "color" => Proposalgen_Model_Meter::METER_TYPE_COLOR, 
                            "copycolor" => Proposalgen_Model_Meter::METER_TYPE_COPY_COLOR, 
                            "printcolor" => Proposalgen_Model_Meter::METER_TYPE_PRINT_COLOR, 
                            "black" => Proposalgen_Model_Meter::METER_TYPE_BLACK, 
                            "copyblack" => Proposalgen_Model_Meter::METER_TYPE_COPY_BLACK, 
                            "printblack" => Proposalgen_Model_Meter::METER_TYPE_PRINT_BLACK, 
                            "scan" => Proposalgen_Model_Meter::METER_TYPE_SCAN, 
                            "fax" => Proposalgen_Model_Meter::METER_TYPE_FAX 
                    );
                    foreach ( $meterColumns as $meterColumn => $meterType )
                    {
                        $newMeter = new Proposalgen_Model_Meter();
                        $newMeter->setMeterType($meterType)
                            ->setStartMeter($row ["start_meter_" . $meterColumn])
                            ->setEndMeter($row ["end_meter_" . $meterColumn]);
                        $meters [$meterType] = $newMeter;
                    }
                    
                    $masterdevice = new Proposalgen_Model_MasterDevice();
                    $masterdevice->setId(null)
                        ->setManufacturer($manufacturer)
                        ->setPrinterModel($row->printer_model)
                        ->setTonerConfig($tonerConfig)
                        ->setTonerConfigId($tonerConfig->TonerConfigId)
                        ->setIsCopier($row->is_copier)
                        ->setIsFax($row->is_fax)
                        ->setIsScanner($row->is_scanner)
                        ->setIsDuplex($row->is_duplex)
                        ->setIsReplacementDevice(0)
                        ->setWattsPowerNormal($row->watts_power_normal)
                        ->setWattsPowerIdle($row->watts_power_idle)
                        ->setDevicePrice($row->device_price)
                        ->setLaunchDate($row->launch_date)
                        ->setDateCreated($row->date_created)
                        ->setServiceCostPerPage($row->service_cost_per_page)
                        ->setIsLeased($row->is_leased)
                        ->setToners($toners);
                    
                    if ($masterdevice->getIsLeased())
                    {
                        $smallestYield = null;
                        foreach ( $toners as $tonersbytype )
                        {
                            foreach ( $tonersbytype as $tonersbycolor )
                            {
                                foreach ( $tonersbycolor as $toner )
                                {
                                    // Ensure toner yield > 0 and pick the smallest possible yield
                                    if (($toner->getTonerYield() > 0 && $toner->getTonerYield() < $smallestYield) || is_null($smallestYield))
                                    {
                                        $smallestYield = $toner->getTonerYield();
                                    }
                                }
                            }
                        }
                    }
                    
                    $masterdevice->setLeasedTonerYield($smallestYield);
                    
                    $device->setDeviceInstanceId(null)
                        ->setReportId($row->report_id)
                        ->setMasterDevice($masterdevice)
                        ->setUploadDataCollectorId($row->upload_data_collector_id)
                        ->setSerialNumber($row->printer_serial_number)
                        ->setMPSMonitorStartDate($row->mps_monitor_startdate)
                        ->setMPSMonitorEndDate($row->mps_monitor_enddate)
                        ->setMPSDiscoveryDate($row->mps_discovery_date)
                        ->setIsExcluded($row->is_excluded)
                        ->setMeters($meters)
                        ->setIsUnknown(true)
                        ->setJitSuppliesSupported($row->jit_supplies_supported)
                        ->setIpAddress($row->ip_address);
                    
                    $deviceList [] = $device;
                }
            }
        }
        catch ( Exception $e )
        {
            throw new Exception("There was an error getting all unknown devices as devices for a report", 0, $e);
        }
        return $deviceList;
    }

    public function getUnknownDeviceToners ($row, $manufacturer)
    {
        $toners = array ();
        $oemtoners = array ();
        $comptoners = array ();
        $hasValidCOMPToners = false;
        
        switch ($row->toner_config_id)
        {
            case Proposalgen_Model_TonerConfig::BLACK_ONLY :
                $oemtoners [Proposalgen_Model_TonerColor::BLACK] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                break;
            case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED :
                $oemtoners [Proposalgen_Model_TonerColor::BLACK] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                $oemtoners [Proposalgen_Model_TonerColor::CYAN] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::CYAN, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                $oemtoners [Proposalgen_Model_TonerColor::MAGENTA] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::MAGENTA, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                $oemtoners [Proposalgen_Model_TonerColor::YELLOW] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::YELLOW, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                break;
            case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED :
                $oemtoners [Proposalgen_Model_TonerColor::BLACK] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                $oemtoners [Proposalgen_Model_TonerColor::THREE_COLOR] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::THREE_COLOR, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                break;
            case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED :
                $oemtoners [Proposalgen_Model_TonerColor::FOUR_COLOR] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::FOUR_COLOR, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                break;
            default :
                throw new Exception("Unknown Device has an unknown or invalid TonerConfig");
                break;
        }
        $toners [Proposalgen_Model_PartType::OEM] = $oemtoners;
        
        switch ($row->toner_config_id)
        {
            case Proposalgen_Model_TonerConfig::BLACK_ONLY :
                if ($this->validateCOMPTonerFields($row, "black_comp"))
                {
                    $comptoners [Proposalgen_Model_TonerColor::BLACK] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $hasValidCOMPToners = true;
                }
                break;
            case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED :
                if ($this->validateCOMPTonerFields($row, "black_comp") && $this->validateCOMPTonerFields($row, "cyan_comp") && $this->validateCOMPTonerFields($row, "magenta_comp") && $this->validateCOMPTonerFields($row, "yellow_comp"))
                {
                    $comptoners [Proposalgen_Model_TonerColor::BLACK] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $comptoners [Proposalgen_Model_TonerColor::CYAN] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::CYAN, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $comptoners [Proposalgen_Model_TonerColor::MAGENTA] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::MAGENTA, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $comptoners [Proposalgen_Model_TonerColor::YELLOW] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::YELLOW, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $hasValidCOMPToners = true;
                }
                break;
            case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED :
                if ($this->validateCOMPTonerFields($row, "black_comp") && $this->validateCOMPTonerFields($row, "3color_comp"))
                {
                    $comptoners [Proposalgen_Model_TonerColor::BLACK] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $comptoners [Proposalgen_Model_TonerColor::THREE_COLOR] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::THREE_COLOR, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $hasValidCOMPToners = true;
                }
                break;
            case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED :
                if ($this->validateCOMPTonerFields($row, "4color_comp"))
                {
                    $comptoners [Proposalgen_Model_TonerColor::FOUR_COLOR] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::FOUR_COLOR, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $hasValidCOMPToners = true;
                }
                break;
            default :
                throw new Exception("Unknown Device has an unknown or invalid TonerConfig");
                break;
        }
        
        if ($hasValidCOMPToners)
        {
            $toners [Proposalgen_Model_PartType::COMP] = $comptoners;
        }
        return $toners;
    }

    public function validateCOMPTonerFields ($row, $rowName)
    {
        $valid = false;
        if (! is_null($row [$rowName . "_SKU"]) && ! is_null($row [$rowName . "_price"]) && ! is_null($row [$rowName . "_yield"]))
        {
            if (strlen($row [$rowName . "_SKU"]) > 0 && $row [$rowName . "_price"] > 0 && $row [$rowName . "_yield"] > 0)
            {
                $valid = true;
            }
        }
        
        return $valid;
    }

    public function mapUnknownDeviceToner ($row, $color, $manufacturer, $partType, $colorSuffix = "") //$tonerSKU, $tonerPrice, $tonerYield, $partTypeId, $manufacturerId, $tonerColorId)
    {
        switch ($color)
        {
            case Proposalgen_Model_TonerColor::BLACK :
                $colortext = "black";
                break;
            case Proposalgen_Model_TonerColor::CYAN :
                $colortext = "cyan";
                break;
            case Proposalgen_Model_TonerColor::MAGENTA :
                $colortext = "magenta";
                break;
            case Proposalgen_Model_TonerColor::YELLOW :
                $colortext = "yellow";
                break;
            case Proposalgen_Model_TonerColor::THREE_COLOR :
                $colortext = "3color";
                break;
            case Proposalgen_Model_TonerColor::FOUR_COLOR :
                $colortext = "4color";
                break;
        }
        $colortext .= $colorSuffix;
        $partTypeMapper = Proposalgen_Model_Mapper_PartType::getInstance();
        $manufacturerMapper = Proposalgen_Model_Mapper_Manufacturer::getInstance();
        $tonerColorMapper = Proposalgen_Model_Mapper_TonerColor::getInstance();
        
        $toner = new Proposalgen_Model_Toner();
        if ($row->is_excluded)
        {
            $toner->setTonerSKU("INVALID")
                ->setTonerPrice(1)
                ->setTonerYield(100)
                ->setPartType($partTypeMapper->find($partType))
                ->setTonerColor($tonerColorMapper->find($color));
        }
        else
        {
            if (isset($row [$colortext . "_SKU"]))
            {
                $toner->setTonerSKU($row [$colortext . "_SKU"])
                    ->setTonerPrice($row [$colortext . "_price"])
                    ->setTonerYield($row [$colortext . "_yield"])
                    ->setPartType($partTypeMapper->find($partType))
                    ->setManufacturer($manufacturer)
                    ->setTonerColor($tonerColorMapper->find($color));
            }
        }
        return $toner;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param unknown_type $object            
     */
    public function save (Proposalgen_Model_UnknownDeviceInstance $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["id"] = $object->getId();
            $data ["user_id"] = $object->getUserId();
            $data ["report_id"] = $object->getReportId();
            $data ["upload_data_collector_row_id"] = $object->getUploadDataCollectorRowId();
            $data ["printermodelid"] = $object->getDataCollectorModelid();
            $data ["mps_monitor_startdate"] = $object->getMpsMonitorStartdate();
            $data ["mps_monitor_enddate"] = $object->getMpsMonitorEnddate();
            $data ["mps_discovery_date"] = $object->getMpsDiscoveryDate();
            $data ["install_date"] = $object->getInstallDate();
            $data ["device_manufacturer"] = $object->getDeviceManufacturer();
            $data ["printer_model"] = $object->getPrinterModel();
            $data ["printer_serial_number"] = $object->getPrinterSerialNumber();
            $data ["toner_config_id"] = $object->getTonerConfig();
            $data ["is_copier"] = $object->getIsCopier();
            $data ["is_fax"] = $object->getIsFax();
            $data ["is_duplex"] = $object->getIsDuplex();
            $data ["is_scanner"] = $object->getIsScanner();
            $data ["jit_supplies_supported"] = $object->getJitSuppliesSupported();
            $data ["watts_power_normal"] = $object->getWattsPowerNormal();
            $data ["watts_power_idle"] = $object->getWattsPowerIdle();
            $data ["device_price"] = $object->getDevicePrice();
            $data ["launch_date"] = $object->getLaunchDate();
            $data ["date_created"] = $object->getDateCreated();
            $data ["black_toner_SKU"] = $object->getBlackTonerSKU();
            $data ["black_toner_price"] = $object->getBlackTonerPrice();
            $data ["black_toner_yield"] = $object->getBlackTonerYield();
            $data ["cyan_toner_SKU"] = $object->getCyanTonerSKU();
            $data ["cyan_toner_price"] = $object->getCyanTonerPrice();
            $data ["cyan_toner_yield"] = $object->getCyanTonerYield();
            $data ["magenta_toner_SKU"] = $object->getMagentaTonerSKU();
            $data ["magenta_toner_price"] = $object->getMagentaTonerPrice();
            $data ["magenta_toner_yield"] = $object->getMagentaTonerYield();
            $data ["yellow_toner_SKU"] = $object->getYellowTonerSKU();
            $data ["yellow_toner_price"] = $object->getYellowTonerPrice();
            $data ["yellow_toner_yield"] = $object->getYellowTonerYield();
            $data ["3color_toner_SKU"] = $object->getThreeColorTonerSKU();
            $data ["3color_toner_SKU"] = $object->getThreeColorTonerSKU();
            $data ["3color_toner_price"] = $object->getThreeColorTonerPrice();
            $data ["3color_toner_yield"] = $object->getThreeColorTonerYield();
            $data ["4color_toner_SKU"] = $object->getFourColorTonerSKU();
            $data ["4color_toner_price"] = $object->getFourColorTonerPrice();
            $data ["4color_toner_yield"] = $object->getFourColorTonerYield();
            
            $data ["black_comp_SKU"] = $object->getBlackCompSKU();
            $data ["black_comp_price"] = $object->getBlackCompPrice();
            $data ["black_comp_yield"] = $object->getBlackCompYield();
            $data ["cyan_comp_SKU"] = $object->getCyanCompSKU();
            $data ["cyan_comp_price"] = $object->getCyanCompPrice();
            $data ["cyan_comp_yield"] = $object->getCyanCompYield();
            $data ["magenta_comp_SKU"] = $object->getMagentaCompSKU();
            $data ["magenta_comp_price"] = $object->getMagentaCompPrice();
            $data ["magenta_comp_yield"] = $object->getMagentaCompYield();
            $data ["yellow_comp_SKU"] = $object->getYellowCompSKU();
            $data ["yellow_comp_price"] = $object->getYellowCompPrice();
            $data ["yellow_comp_yield"] = $object->getYellowCompYield();
            $data ["3color_comp_SKU"] = $object->getThreeColorCompSKU();
            $data ["3color_comp_SKU"] = $object->getThreeColorCompSKU();
            $data ["3color_comp_price"] = $object->getThreeColorCompPrice();
            $data ["3color_comp_yield"] = $object->getThreeColorCompYield();
            $data ["4color_comp_SKU"] = $object->getFourColorCompSKU();
            $data ["4color_comp_price"] = $object->getFourColorCompPrice();
            $data ["4color_comp_yield"] = $object->getFourColorCompYield();
            
            $data ["start_meter_life"] = $object->getStartMeterLife();
            $data ["end_meter_life"] = $object->getEndMeterLife();
            $data ["start_meter_black"] = $object->getStartMeterBlack();
            $data ["end_meter_black"] = $object->getEndMeterBlack();
            $data ["start_meter_color"] = $object->getStartMeterColor();
            $data ["end_meter_color"] = $object->getEndMeterColor();
            $data ["start_meter_printblack"] = $object->getStartMeterBlack();
            $data ["end_meter_printblack"] = $object->getEndMeterBlack();
            $data ["start_meter_printcolor"] = $object->getStartMeterPrintcolor();
            $data ["end_meter_printcolor"] = $object->getEndtMeterPrintcolor();
            $data ["start_meter_copyblack"] = $object->getStartMeterCopyblack();
            $data ["end_meter_copyblack"] = $object->getEndMeterCopyblack();
            $data ["start_meter_copycolor"] = $object->getStartMeterCopycolor();
            $data ["start_meter_copycolor"] = $object->getEndMeterCopycolor();
            $data ["start_meter_fax"] = $object->getStartMeterFax();
            $data ["end_meter_fax"] = $object->getEndMeterFax();
            $data ["start_meter_scan"] = $object->getStartMeterScan();
            $data ["end_meter_scan"] = $object->getEndMeterScan();
            $data ["is_excluded"] = $object->getIsExcluded();
            $data ["is_leased"] = $object->getLoginRestrictedUntilDate();
            $data ["service_cost_per_page"] = $object->getServiceCostPerPage();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}
