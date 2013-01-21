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
        if (!isset(self::$_instance))
        {
            $className       = get_class();
            self::$_instance = new $className();
        }

        return self::$_instance;
    }

    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row
     *
     * @throws Exception
     * @return Proposalgen_Model_UnknownDeviceInstance
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                           = new Proposalgen_Model_UnknownDeviceInstance();
            $object->id                       = $row->id;
            $object->userId                   = $row->user_id;
            $object->reportId                 = $row->report_id;
            $object->uploadDataCollectorRowId = $row->upload_data_collector_row_id;
            $object->printerModelId           = $row->printermodelid;
            $object->mpsMonitorStartDate      = $row->mps_monitor_startdate;
            $object->mpsMonitorEndDate        = $row->mps_monitor_enddate;
            $object->mpsDiscoveryDate         = $row->mps_discovery_date;
            $object->installDate              = $row->install_date;
            $object->deviceManufacturer       = $row->device_manufacturer;
            $object->printerModel             = $row->printer_model;
            $object->printerSerialNumber      = $row->printer_serial_number;
            $object->tonerConfigId            = $row->toner_config;
            $object->isCopier                 = $row->is_copier;
            $object->isFax                    = $row->is_fax;
            $object->isDuplex                 = $row->is_duplex;
            $object->isScanner                = $row->is_scanner;
            $object->wattsPowerNormal         = $row->jit_supplies_supported;
            $object->wattsPowerIdle           = $row->watts_power_normal;
            $object->cost              = $row->watts_power_idle;
            $object->launchDate               = $row->cost;
            $object->dateCreated              = $row->launch_date;
            $object->blackTonerSku            = $row->date_created;
            $object->blackTonerPrice          = $row->black_toner_sku;
            $object->blackTonerYield          = $row->black_toner_cost;
            $object->cyanTonerSku             = $row->black_toner_yield;
            $object->cyanTonerPrice           = $row->cyan_toner_sku;
            $object->cyanTonerYield           = $row->cyan_toner_cost;
            $object->magentaTonerSku          = $row->cyan_toner_yield;
            $object->magentaTonerPrice        = $row->magenta_toner_sku;
            $object->magentaTonerYield        = $row->magenta_toner_cost;
            $object->yellowTonerSku           = $row->magenta_toner_yield;
            $object->yellowTonerPrice         = $row->yellow_toner_sku;
            $object->yellowTonerYield         = $row->yellow_toner_cost;
            $object->threeColorTonerSku       = $row->yellow_toner_yield;
            $object->threeColorTonerPrice     = $row->three_color_toner_sku;
            $object->threeColorTonerYield     = $row->three_color_toner_cost;
            $object->fourColorTonerSku        = $row->three_color_toner_yield;
            $object->fourColorTonerPrice      = $row->four_color_toner_sku;
            $object->fourColorTonerYield      = $row->four_color_toner_cost;
            $object->blackCompSku             = $row->four_color_toner_yield;
            $object->blackCompPrice           = $row->black_comp_sku;
            $object->blackCompYield           = $row->black_comp_cost;
            $object->cyanCompSku              = $row->black_comp_yield;
            $object->cyanCompPrice            = $row->cyan_comp_sku;
            $object->cyanCompYield            = $row->cyan_comp_cost;
            $object->magentaCompSku           = $row->cyan_comp_yield;
            $object->magentaCompPrice         = $row->magenta_comp_sku;
            $object->magentaCompYield         = $row->magenta_comp_cost;
            $object->yellowCompSku            = $row->magenta_comp_yield;
            $object->yellowCompPrice          = $row->yellow_comp_sku;
            $object->yellowCompYield          = $row->yellow_comp_cost;
            $object->threeColorCompSku        = $row->yellow_comp_yield;
            $object->threeColorCompPrice      = $row->three_color_comp_sku;
            $object->threeColorCompYield      = $row->three_color_comp_cost;
            $object->fourColorCompSku         = $row->three_color_comp_yield;
            $object->fourColorCompPrice       = $row->four_color_comp_sku;
            $object->fourColorCompYield       = $row->four_color_comp_cost;
            $object->startMeterLife           = $row->four_color_comp_yield;
            $object->endMeterLife             = $row->start_meter_life;
            $object->startMeterBlack          = $row->end_meter_life;
            $object->endMeterBlack            = $row->start_meter_black;
            $object->startMeterColor          = $row->end_meter_black;
            $object->endMeterColor            = $row->start_meter_color;
            $object->startMeterPrintBlack     = $row->end_meter_color;
            $object->endMeterPrintBlack       = $row->start_meter_printblack;
            $object->startMeterPrintColor     = $row->end_meter_printblack;
            $object->endMeterPrintColor       = $row->start_meter_printcolor;
            $object->startMeterCopyBlack      = $row->end_meter_printcolor;
            $object->endMeterCopyBlack        = $row->start_meter_copyblack;
            $object->startMeterCopyColor      = $row->end_meter_copyblack;
            $object->endMeterCopyColor        = $row->start_meter_copycolor;
            $object->startMeterFax            = $row->end_meter_copycolor;
            $object->endMeterFax              = $row->start_meter_fax;
            $object->startMeterScan           = $row->end_meter_fax;
            $object->endMeterScan             = $row->start_meter_scan;
            $object->jitSuppliesSupported     = $row->end_meter_scan;
            $object->isExcluded               = $row->is_excluded;
            $object->isLeased                 = $row->is_leased;
            $object->ipAddress                = $row->ip_address;
            $object->dutyCycle                = $row->duty_cycle;
            $object->ppmBlack                 = $row->ppm_black;
            $object->ppmColor                 = $row->ppm_color;
            $object->serviceCostPerPage       = $row->service_cost_per_page;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map an unknown device instance row", 0, $e);
        }

        return $object;
    }

    /**
     * Fetches all unknown devices and puts them in the same format as normal devices
     *
     * @param int          $reportId
     * @param array|string $whereClause
     *
     * @throws Exception
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function fetchAllUnknownDevicesAsKnownDevices ($reportId, $whereClause)
    {
        $deviceList = array();
        try
        {
            $unknownDevices = $this->getDbTable()->fetchAll($whereClause);
            if ($unknownDevices)
            {
                foreach ($unknownDevices as $row)
                {
                    $device = new Proposalgen_Model_DeviceInstance();

                    $manufacturer              = new Proposalgen_Model_Manufacturer();
                    $manufacturer->fullname    = $row->device_manufacturer;
                    $manufacturer->displayname = $row->device_manufacturer;

                    $tonerConfigMapper = Proposalgen_Model_Mapper_TonerConfig::getInstance();
                    $tonerConfig       = $tonerConfigMapper->find($row->toner_config_id);

                    // Get toner list based on the config
                    $toners = $this->getUnknownDeviceToners($row, $manufacturer);

                    $meters = array();
                    // Form a list of meters
                    $meterColumns = array(
                        "life"       => Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_LIFE,
                        "color"      => Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COLOR,
                        "copycolor"  => Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COPY_COLOR,
                        "printcolor" => Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_PRINT_COLOR,
                        "black"      => Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_BLACK,
                        "copyblack"  => Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_COPY_BLACK,
                        "printblack" => Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_PRINT_BLACK,
                        "scan"       => Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_SCAN,
                        "fax"        => Proposalgen_Model_DeviceInstanceMeter::METER_TYPE_FAX
                    );
                    foreach ($meterColumns as $meterColumn => $meterType)
                    {
                        $newMeter             = new Proposalgen_Model_DeviceInstanceMeter();
                        $newMeter->meterType  = $meterType;
                        $newMeter->startMeter = $row ["start_meter_" . $meterColumn];
                        $newMeter->endMeter   = $row ["end_meter_" . $meterColumn];
                        $meters [$meterType]  = $newMeter;
                    }

                    $masterDevice                      = new Proposalgen_Model_MasterDevice();
                    $masterDevice->id                  = null;
                    $masterDevice->modelName        = $row->printer_model;
                    $masterDevice->tonerConfigId       = $tonerConfig->tonerConfigId;
                    $masterDevice->isCopier            = $row->is_copier;
                    $masterDevice->isFax               = $row->is_fax;
                    $masterDevice->isScanner           = $row->is_scanner;
                    $masterDevice->isDuplex            = $row->is_duplex;
                    $masterDevice->isReplacementDevice = 0;
                    $masterDevice->wattsPowerNormal    = $row->watts_power_normal;
                    $masterDevice->wattsPowerIdle      = $row->watts_power_idle;
                    $masterDevice->cost                = $row->cost;
                    $masterDevice->launchDate          = $row->launch_date;
                    $masterDevice->dateCreated         = $row->date_created;
                    $masterDevice->serviceCostPerPage  = $row->service_cost_per_page;
                    $masterDevice->isLeased            = $row->is_leased;

                    $masterDevice->setTonerConfig($tonerConfig);
                    $masterDevice->setManufacturer($manufacturer);
                    $masterDevice->setToners($toners);

                    if ($masterDevice->isLeased)
                    {
                        $smallestYield = null;
                        foreach ($toners as $tonersByType)
                        {
                            foreach ($tonersByType as $tonersByColor)
                            {
                                /* @var $toner Proposalgen_Model_Toner */
                                foreach ($tonersByColor as $toner)
                                {
                                    // Ensure toner yield > 0 and pick the smallest possible yield
                                    if (($toner->yield > 0 && $toner->yield < $smallestYield) || is_null($smallestYield))
                                    {
                                        $smallestYield = $toner->yield;
                                    }
                                }
                            }
                        }
                        $masterDevice->leasedTonerYield = $smallestYield;
                    }


                    $device->id                    = null;
                    $device->reportId              = $row->report_id;
                    $device->rmsUploadRowId = $row->upload_data_collector_row_id;
                    $device->serialNumber          = $row->printer_serial_number;
                    $device->mpsMonitorStartDate   = $row->mps_monitor_startdate;
                    $device->mpsMonitorEndDate     = $row->mps_monitor_enddate;
                    $device->mpsDiscoveryDate      = $row->mps_discovery_date;
                    $device->isExcluded            = $row->is_excluded;
                    $device->isUnknown             = true;
                    $device->reportsTonerLevels  = $row->jit_supplies_supported;
                    $device->ipAddress             = $row->ip_address;

                    $device->setMeters($meters);
                    $device->setMasterDevice($masterDevice);

                    $deviceList [] = $device;
                }
            }
        }
        catch (Exception $e)
        {
            throw new Exception("There was an error getting all unknown devices as devices for a report", 0, $e);
        }

        return $deviceList;
    }

    /**
     * Turns an unknown device row into a set of toners
     *
     * @param $row
     * @param $manufacturer
     *
     * @return Proposalgen_Model_Toner[][][]
     * @throws Exception
     */
    public function getUnknownDeviceToners ($row, $manufacturer)
    {
        $toners                   = array();
        $oemToners                = array();
        $compatibleToners         = array();
        $hasValidCompatibleToners = false;

        switch ($row->toner_config_id)
        {
            case Proposalgen_Model_TonerConfig::BLACK_ONLY :
                $oemToners [Proposalgen_Model_TonerColor::BLACK] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                break;
            case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED :
                $oemToners [Proposalgen_Model_TonerColor::BLACK] []   = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                $oemToners [Proposalgen_Model_TonerColor::CYAN] []    = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::CYAN, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                $oemToners [Proposalgen_Model_TonerColor::MAGENTA] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::MAGENTA, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                $oemToners [Proposalgen_Model_TonerColor::YELLOW] []  = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::YELLOW, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                break;
            case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED :
                $oemToners [Proposalgen_Model_TonerColor::BLACK]       = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                $oemToners [Proposalgen_Model_TonerColor::THREE_COLOR] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::THREE_COLOR, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                break;
            case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED :
                $oemToners [Proposalgen_Model_TonerColor::FOUR_COLOR] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::FOUR_COLOR, $manufacturer, Proposalgen_Model_PartType::OEM, "_toner");
                break;
            default :
                throw new Exception("Unknown Device has an unknown or invalid TonerConfig");
                break;
        }
        $toners [Proposalgen_Model_PartType::OEM] = $oemToners;

        switch ($row->toner_config_id)
        {
            case Proposalgen_Model_TonerConfig::BLACK_ONLY :
                if ($this->validateCOMPTonerFields($row, "black_comp"))
                {
                    $compatibleToners [Proposalgen_Model_TonerColor::BLACK] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $hasValidCompatibleToners                                  = true;
                }
                break;
            case Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED :
                if ($this->validateCOMPTonerFields($row, "black_comp") && $this->validateCOMPTonerFields($row, "cyan_comp") && $this->validateCOMPTonerFields($row, "magenta_comp") && $this->validateCOMPTonerFields($row, "yellow_comp"))
                {
                    $compatibleToners [Proposalgen_Model_TonerColor::BLACK] []   = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $compatibleToners [Proposalgen_Model_TonerColor::CYAN] []    = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::CYAN, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $compatibleToners [Proposalgen_Model_TonerColor::MAGENTA] [] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::MAGENTA, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $compatibleToners [Proposalgen_Model_TonerColor::YELLOW] []  = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::YELLOW, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $hasValidCompatibleToners                                    = true;
                }
                break;
            case Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED :
                if ($this->validateCOMPTonerFields($row, "black_comp") && $this->validateCOMPTonerFields($row, "three_color_comp"))
                {
                    $compatibleToners [Proposalgen_Model_TonerColor::BLACK]       = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::BLACK, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $compatibleToners [Proposalgen_Model_TonerColor::THREE_COLOR] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::THREE_COLOR, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $hasValidCompatibleToners                                     = true;
                }
                break;
            case Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED :
                if ($this->validateCOMPTonerFields($row, "four_color_comp"))
                {
                    $compatibleToners [Proposalgen_Model_TonerColor::FOUR_COLOR] = $this->mapUnknownDeviceToner($row, Proposalgen_Model_TonerColor::FOUR_COLOR, $manufacturer, Proposalgen_Model_PartType::COMP, "_comp");
                    $hasValidCompatibleToners                                    = true;
                }
                break;
            default :
                throw new Exception("Unknown Device has an unknown or invalid TonerConfig");
                break;
        }

        if ($hasValidCompatibleToners)
        {
            $toners [Proposalgen_Model_PartType::COMP] = $compatibleToners;
        }

        return $toners;
    }

    public function validateCOMPTonerFields ($row, $rowName)
    {
        $valid = false;
        if (!is_null($row [$rowName . "_sku"]) && !is_null($row [$rowName . "_cost"]) && !is_null($row [$rowName . "_yield"]))
        {
            if (strlen($row [$rowName . "_sku"]) > 0 && $row [$rowName . "_cost"] > 0 && $row [$rowName . "_yield"] > 0)
            {
                $valid = true;
            }
        }

        return $valid;
    }

    public function mapUnknownDeviceToner ($row, $color, $manufacturer, $partType, $colorSuffix = "") //$tonerSKU, $tonerPrice, $tonerYield, $partTypeId, $manufacturerId, $tonerColorId)
    {
        $colorText = '';
        switch ($color)
        {
            case Proposalgen_Model_TonerColor::BLACK :
                $colorText = "black";
                break;
            case Proposalgen_Model_TonerColor::CYAN :
                $colorText = "cyan";
                break;
            case Proposalgen_Model_TonerColor::MAGENTA :
                $colorText = "magenta";
                break;
            case Proposalgen_Model_TonerColor::YELLOW :
                $colorText = "yellow";
                break;
            case Proposalgen_Model_TonerColor::THREE_COLOR :
                $colorText = "three_color";
                break;
            case Proposalgen_Model_TonerColor::FOUR_COLOR :
                $colorText = "four_color";
                break;
        }
        $colorText .= $colorSuffix;
        $partTypeMapper   = Proposalgen_Model_Mapper_PartType::getInstance();
        $tonerColorMapper = Proposalgen_Model_Mapper_TonerColor::getInstance();

        $toner = new Proposalgen_Model_Toner();
        if ($row->is_excluded)
        {
            $toner->sku   = "INVALID";
            $toner->cost  = 1;
            $toner->yield = 100;
            $toner->setPartType($partTypeMapper->find($partType));
            $toner->setTonerColor($tonerColorMapper->find($color));
        }
        else
        {
            if (isset($row [$colorText . "_sku"]))
            {
                $toner->sku   = $row [$colorText . "_sku"];
                $toner->cost  = $row [$colorText . "_cost"];
                $toner->yield = $row [$colorText . "_yield"];
                $toner->setPartType($partTypeMapper->find($partType));
                $toner->setManufacturer($manufacturer);
                $toner->setTonerColor($tonerColorMapper->find($color));
            }
        }

        return $toner;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_UnknownDeviceInstance $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["id"]                           = $object->id;
            $data ["user_id"]                      = $object->userId;
            $data ["report_id"]                    = $object->reportId;
            $data ["upload_data_collector_row_id"] = $object->uploadDataCollectorRowId;
            $data ["printermodelid"]               = $object->printerModelId;
            $data ["mps_monitor_startdate"]        = $object->mpsMonitorStartDate;
            $data ["mps_monitor_enddate"]          = $object->mpsMonitorEndDate;
            $data ["mps_discovery_date"]           = $object->mpsDiscoveryDate;
            $data ["install_date"]                 = $object->installDate;
            $data ["device_manufacturer"]          = $object->deviceManufacturer;
            $data ["printer_model"]                = $object->printerModel;
            $data ["printer_serial_number"]        = $object->printerSerialNumber;
            $data ["toner_config_id"]              = $object->tonerConfigId;
            $data ["is_copier"]                    = $object->isCopier;
            $data ["is_fax"]                       = $object->isFax;
            $data ["is_duplex"]                    = $object->isDuplex;
            $data ["is_scanner"]                   = $object->isScanner;
            $data ["watts_power_normal"]           = $object->wattsPowerNormal;
            $data ["watts_power_idle"]             = $object->wattsPowerIdle;
            $data ["cost"]                         = $object->cost;
            $data ["launch_date"]                  = $object->launchDate;
            $data ["date_created"]                 = $object->dateCreated;
            $data ["black_toner_sku"]              = $object->blackTonerSku;
            $data ["black_toner_cost"]             = $object->blackTonerPrice;
            $data ["black_toner_yield"]            = $object->blackTonerYield;
            $data ["cyan_toner_sku"]               = $object->cyanTonerSku;
            $data ["cyan_toner_cost"]              = $object->cyanTonerPrice;
            $data ["cyan_toner_yield"]             = $object->cyanTonerYield;
            $data ["magenta_toner_sku"]            = $object->magentaTonerSku;
            $data ["magenta_toner_cost"]           = $object->magentaTonerPrice;
            $data ["magenta_toner_yield"]          = $object->magentaTonerYield;
            $data ["yellow_toner_sku"]             = $object->yellowTonerSku;
            $data ["yellow_toner_cost"]            = $object->yellowTonerPrice;
            $data ["yellow_toner_yield"]           = $object->yellowTonerYield;
            $data ["three_color_toner_sku"]        = $object->threeColorTonerSku;
            $data ["three_color_toner_cost"]       = $object->threeColorTonerPrice;
            $data ["three_color_toner_yield"]      = $object->threeColorTonerYield;
            $data ["four_color_toner_sku"]         = $object->fourColorTonerSku;
            $data ["four_color_toner_cost"]        = $object->fourColorTonerPrice;
            $data ["four_color_toner_yield"]       = $object->fourColorTonerYield;
            $data ["black_comp_sku"]               = $object->blackCompSku;
            $data ["black_comp_cost"]              = $object->blackCompPrice;
            $data ["black_comp_yield"]             = $object->blackCompYield;
            $data ["cyan_comp_sku"]                = $object->cyanCompSku;
            $data ["cyan_comp_cost"]               = $object->cyanCompPrice;
            $data ["cyan_comp_yield"]              = $object->cyanCompYield;
            $data ["magenta_comp_sku"]             = $object->magentaCompSku;
            $data ["magenta_comp_cost"]            = $object->magentaCompPrice;
            $data ["magenta_comp_yield"]           = $object->magentaCompYield;
            $data ["yellow_comp_sku"]              = $object->yellowCompSku;
            $data ["yellow_comp_cost"]             = $object->yellowCompPrice;
            $data ["yellow_comp_yield"]            = $object->yellowCompYield;
            $data ["three_color_comp_sku"]         = $object->threeColorCompSku;
            $data ["three_color_comp_cost"]        = $object->threeColorCompPrice;
            $data ["three_color_comp_yield"]       = $object->threeColorCompYield;
            $data ["four_color_comp_sku"]          = $object->fourColorCompSku;
            $data ["four_color_comp_cost"]         = $object->fourColorCompPrice;
            $data ["four_color_comp_yield"]        = $object->fourColorCompYield;
            $data ["start_meter_life"]             = $object->startMeterLife;
            $data ["end_meter_life"]               = $object->endMeterLife;
            $data ["start_meter_black"]            = $object->startMeterBlack;
            $data ["end_meter_black"]              = $object->endMeterBlack;
            $data ["start_meter_color"]            = $object->startMeterColor;
            $data ["end_meter_color"]              = $object->endMeterColor;
            $data ["start_meter_printblack"]       = $object->startMeterPrintBlack;
            $data ["end_meter_printblack"]         = $object->endMeterPrintBlack;
            $data ["start_meter_printcolor"]       = $object->startMeterPrintColor;
            $data ["end_meter_printcolor"]         = $object->endMeterPrintColor;
            $data ["start_meter_copyblack"]        = $object->startMeterCopyBlack;
            $data ["end_meter_copyblack"]          = $object->endMeterCopyBlack;
            $data ["start_meter_copycolor"]        = $object->startMeterCopyColor;
            $data ["end_meter_copycolor"]          = $object->endMeterCopyColor;
            $data ["start_meter_fax"]              = $object->startMeterFax;
            $data ["end_meter_fax"]                = $object->endMeterFax;
            $data ["start_meter_scan"]             = $object->startMeterScan;
            $data ["end_meter_scan"]               = $object->endMeterScan;
            $data ["jit_supplies_supported"]       = $object->jitSuppliesSupported;
            $data ["is_excluded"]                  = $object->isExcluded;
            $data ["is_leased"]                    = $object->isLeased;
            $data ["ip_address"]                   = $object->ipAddress;
            $data ["duty_cycle"]                   = $object->dutyCycle;
            $data ["ppm_black"]                    = $object->ppmBlack;
            $data ["ppm_color"]                    = $object->ppmColor;
            $data ["service_cost_per_page"]        = $object->serviceCostPerPage;
            $primaryKey                            = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}