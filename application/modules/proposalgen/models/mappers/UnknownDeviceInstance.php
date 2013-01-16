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
            $object = new Proposalgen_Model_UnknownDeviceInstance();
            $object->setId($row->id);
            $object->setUserId($row->user_id);
            $object->setReportId($row->report_id);
            $object->setUploadDataCollectorRowId($row->upload_data_collector_row_id);
            $object->setPrinterModelid($row->printermodelid);
            $object->setMpsMonitorStartdate($row->mps_monitor_startdate);
            $object->setMpsMonitorEnddate($row->mps_monitor_enddate);
            $object->setMpsDiscoveryDate($row->mps_discovery_date);
            $object->setInstallDate($row->install_date);
            $object->setDeviceManufacturer($row->device_manufacturer);
            $object->setPrinterModel($row->printer_model);
            $object->setPrinterSerialNumber($row->printer_serial_number);
            $object->setTonerConfig($row->toner_config);
            $object->setIsCopier($row->is_copier);
            $object->setIsFax($row->is_fax);
            $object->setIsDuplex($row->is_duplex);
            $object->setIsScanner($row->is_scanner);
            $object->setJitSuppliesSupported($row->jit_supplies_supported);
            $object->setWattsPowerNormal($row->watts_power_normal);
            $object->setWattsPowerIdle($row->watts_power_idle);
            $object->setDevicePrice($row->cost);
            $object->setLaunchDate($row->launch_date);
            $object->setDateCreated($row->date_created);
            $object->setBlackTonerSKU($row->black_toner_sku);
            $object->setBlackTonerPrice($row->black_toner_cost);
            $object->setBlackTonerYield($row->black_toner_yield);
            $object->setCyanTonerSKU($row->cyan_toner_sku);
            $object->setCyanTonerPrice($row->cyan_toner_cost);
            $object->setCyanTonerYield($row->cyan_toner_yield);
            $object->setMagentaTonerSKU($row->magenta_toner_sku);
            $object->setMagentaTonerPrice($row->magenta_toner_cost);
            $object->setMagentaTonerYield($row->magenta_toner_yield);
            $object->setYellowTonerSKU($row->yellow_toner_sku);
            $object->setYellowTonerPrice($row->yellow_toner_cost);
            $object->setYellowTonerYield($row->yellow_toner_yield);
            $object->setThreeColorTonerSKU($row ['three_color_toner_sku']);
            $object->setThreeColorTonerPrice($row ['three_color_toner_cost']);
            $object->setThreeColorTonerYield($row ['three_color_toner_yield']);
            $object->setFourColorTonerSKU($row ['four_color_toner_sku']);
            $object->setFourColorTonerPrice($row ['four_color_toner_cost']);
            $object->setFourColorTonerYield($row ['four_color_toner_yield']);
            $object->setBlackCompSKU($row->black_comp_sku);
            $object->setBlackCompPrice($row->black_comp_cost);
            $object->setBlackCompYield($row->black_comp_yield);
            $object->setCyanCompSKU($row->cyan_comp_sku);
            $object->setCyanCompPrice($row->cyan_comp_cost);
            $object->setCyanCompYield($row->cyan_comp_yield);
            $object->setMagentaCompSKU($row->magenta_comp_sku);
            $object->setMagentaCompPrice($row->magenta_comp_cost);
            $object->setMagentaCompYield($row->magenta_comp_yield);
            $object->setYellowCompSKU($row->yellow_comp_sku);
            $object->setYellowCompPrice($row->yellow_comp_cost);
            $object->setYellowCompYield($row->yellow_comp_yield);
            $object->setThreeColorCompSKU($row ['three_color_comp_sku']);
            $object->setThreeColorCompPrice($row ['three_color_comp_cost']);
            $object->setThreeColorCompYield($row ['three_color_comp_yield']);
            $object->setFourColorCompSKU($row ['four_color_comp_sku']);
            $object->setFourColorCompPrice($row ['four_color_comp_cost']);
            $object->setFourColorCompYield($row ['four_color_comp_yield']);
            $object->setStartMeterLife($row->start_meter_life);
            $object->setEndMeterLife($row->end_meter_life);
            $object->setStartMeterBlack($row->start_meter_black);
            $object->setEndMeterBlack($row->end_meter_black);
            $object->setStartMeterColor($row->start_meter_color);
            $object->setEndMeterColor($row->end_meter_color);
            $object->setStartMeterPrintblack($row->start_meter_printblack);
            $object->setEndMeterPrintblack($row->end_meter_printblack);
            $object->setStartMeterPrintcolor($row->start_meter_printcolor);
            $object->setEndMeterPrintcolor($row->end_meter_printcolor);
            $object->setStartMeterCopyblack($row->start_meter_copyblack);
            $object->setEndMeterCopyblack($row->end_meter_copyblack);
            $object->setStartMeterCopycolor($row->start_meter_copycolor);
            $object->setEndMeterCopycolor($row->end_meter_copycolor);
            $object->setStartMeterFax($row->start_meter_fax);
            $object->setEndMeterFax($row->end_meter_fax);
            $object->setStartMeterScan($row->start_meter_scan);
            $object->setEndMeterScan($row->end_meter_scan);
            $object->setIsExcluded($row->is_excluded);
            $object->setIsLeased($row->is_leased);
            $object->setIpAddress($row->ip_address);
            $object->setServiceCostPerPage($row->service_cost_per_page);
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
                        "life"       => Proposalgen_Model_Meter::METER_TYPE_LIFE,
                        "color"      => Proposalgen_Model_Meter::METER_TYPE_COLOR,
                        "copycolor"  => Proposalgen_Model_Meter::METER_TYPE_COPY_COLOR,
                        "printcolor" => Proposalgen_Model_Meter::METER_TYPE_PRINT_COLOR,
                        "black"      => Proposalgen_Model_Meter::METER_TYPE_BLACK,
                        "copyblack"  => Proposalgen_Model_Meter::METER_TYPE_COPY_BLACK,
                        "printblack" => Proposalgen_Model_Meter::METER_TYPE_PRINT_BLACK,
                        "scan"       => Proposalgen_Model_Meter::METER_TYPE_SCAN,
                        "fax"        => Proposalgen_Model_Meter::METER_TYPE_FAX
                    );
                    foreach ($meterColumns as $meterColumn => $meterType)
                    {
                        $newMeter             = new Proposalgen_Model_Meter();
                        $newMeter->meterType  = $meterType;
                        $newMeter->startMeter = $row ["start_meter_" . $meterColumn];
                        $newMeter->endMeter   = $row ["end_meter_" . $meterColumn];
                        $meters [$meterType]  = $newMeter;
                    }

                    $masterDevice = new Proposalgen_Model_MasterDevice();
                    $masterDevice->setId(null)
                        ->setManufacturer($manufacturer)
                        ->setPrinterModel($row->printer_model)
                        ->setTonerConfig($tonerConfig)
                        ->setTonerConfigId($tonerConfig->tonerConfigId)
                        ->setIsCopier($row->is_copier)
                        ->setIsFax($row->is_fax)
                        ->setIsScanner($row->is_scanner)
                        ->setIsDuplex($row->is_duplex)
                        ->setIsReplacementDevice(0)
                        ->setWattsPowerNormal($row->watts_power_normal)
                        ->setWattsPowerIdle($row->watts_power_idle)
                        ->setCost($row->cost)
                        ->setLaunchDate($row->launch_date)
                        ->setDateCreated($row->date_created)
                        ->setServiceCostPerPage($row->service_cost_per_page)
                        ->setIsLeased($row->is_leased)
                        ->setToners($toners);

                    if ($masterDevice->getIsLeased())
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
                        $masterDevice->setLeasedTonerYield($smallestYield);
                    }


                    $device->DeviceInstanceId      = null;
                    $device->ReportId              = $row->report_id;
                    $device->UploadDataCollectorId = $row->upload_data_collector_row_id;
                    $device->SerialNumber          = $row->printer_serial_number;
                    $device->MPSMonitorStartDate   = $row->mps_monitor_startdate;
                    $device->MPSMonitorEndDate     = $row->mps_monitor_enddate;
                    $device->MPSDiscoveryDate      = $row->mps_discovery_date;
                    $device->IsExcluded            = $row->is_excluded;
                    $device->IsUnknown             = true;
                    $device->JitSuppliesSupported  = $row->jit_supplies_supported;
                    $device->IpAddress             = $row->ip_address;

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
        $primaryKey = 0;
        try
        {
            $data ["id"]                           = $object->getId();
            $data ["user_id"]                      = $object->getUserId();
            $data ["report_id"]                    = $object->getReportId();
            $data ["upload_data_collector_row_id"] = $object->getUploadDataCollectorRowId();
            $data ["printermodelid"]               = $object->getPrinterModelid();
            $data ["mps_monitor_startdate"]        = $object->getMpsMonitorStartdate();
            $data ["mps_monitor_enddate"]          = $object->getMpsMonitorEnddate();
            $data ["mps_discovery_date"]           = $object->getMpsDiscoveryDate();
            $data ["install_date"]                 = $object->getInstallDate();
            $data ["device_manufacturer"]          = $object->getDeviceManufacturer();
            $data ["printer_model"]                = $object->getPrinterModel();
            $data ["printer_serial_number"]        = $object->getPrinterSerialNumber();
            $data ["toner_config_id"]              = $object->getTonerConfig();
            $data ["is_copier"]                    = $object->getIsCopier();
            $data ["is_fax"]                       = $object->getIsFax();
            $data ["is_duplex"]                    = $object->getIsDuplex();
            $data ["is_scanner"]                   = $object->getIsScanner();
            $data ["jit_supplies_supported"]       = $object->getJitSuppliesSupported();
            $data ["watts_power_normal"]           = $object->getWattsPowerNormal();
            $data ["watts_power_idle"]             = $object->getWattsPowerIdle();
            $data ["cost"]                         = $object->getDevicePrice();
            $data ["launch_date"]                  = $object->getLaunchDate();
            $data ["date_created"]                 = $object->getDateCreated();
            $data ["black_toner_sku"]              = $object->getBlackTonerSKU();
            $data ["black_toner_cost"]             = $object->getBlackTonerPrice();
            $data ["black_toner_yield"]            = $object->getBlackTonerYield();
            $data ["cyan_toner_sku"]               = $object->getCyanTonerSKU();
            $data ["cyan_toner_cost"]              = $object->getCyanTonerPrice();
            $data ["cyan_toner_yield"]             = $object->getCyanTonerYield();
            $data ["magenta_toner_sku"]            = $object->getMagentaTonerSKU();
            $data ["magenta_toner_cost"]           = $object->getMagentaTonerPrice();
            $data ["magenta_toner_yield"]          = $object->getMagentaTonerYield();
            $data ["yellow_toner_sku"]             = $object->getYellowTonerSKU();
            $data ["yellow_toner_cost"]            = $object->getYellowTonerPrice();
            $data ["yellow_toner_yield"]           = $object->getYellowTonerYield();
            $data ["three_color_toner_sku"]        = $object->getThreeColorTonerSKU();
            $data ["three_color_toner_sku"]        = $object->getThreeColorTonerSKU();
            $data ["three_color_toner_cost"]       = $object->getThreeColorTonerPrice();
            $data ["three_color_toner_yield"]      = $object->getThreeColorTonerYield();
            $data ["four_color_toner_sku"]         = $object->getFourColorTonerSKU();
            $data ["four_color_toner_cost"]        = $object->getFourColorTonerPrice();
            $data ["four_color_toner_yield"]       = $object->getFourColorTonerYield();

            $data ["black_comp_sku"]         = $object->getBlackCompSKU();
            $data ["black_comp_cost"]        = $object->getBlackCompPrice();
            $data ["black_comp_yield"]       = $object->getBlackCompYield();
            $data ["cyan_comp_sku"]          = $object->getCyanCompSKU();
            $data ["cyan_comp_cost"]         = $object->getCyanCompPrice();
            $data ["cyan_comp_yield"]        = $object->getCyanCompYield();
            $data ["magenta_comp_sku"]       = $object->getMagentaCompSKU();
            $data ["magenta_comp_cost"]      = $object->getMagentaCompPrice();
            $data ["magenta_comp_yield"]     = $object->getMagentaCompYield();
            $data ["yellow_comp_sku"]        = $object->getYellowCompSKU();
            $data ["yellow_comp_cost"]       = $object->getYellowCompPrice();
            $data ["yellow_comp_yield"]      = $object->getYellowCompYield();
            $data ["three_color_comp_sku"]   = $object->getThreeColorCompSKU();
            $data ["three_color_comp_sku"]   = $object->getThreeColorCompSKU();
            $data ["three_color_comp_cost"]  = $object->getThreeColorCompPrice();
            $data ["three_color_comp_yield"] = $object->getThreeColorCompYield();
            $data ["four_color_comp_sku"]    = $object->getFourColorCompSKU();
            $data ["four_color_comp_cost"]   = $object->getFourColorCompPrice();
            $data ["four_color_comp_yield"]  = $object->getFourColorCompYield();

            $data ["start_meter_life"]       = $object->getStartMeterLife();
            $data ["end_meter_life"]         = $object->getEndMeterLife();
            $data ["start_meter_black"]      = $object->getStartMeterBlack();
            $data ["end_meter_black"]        = $object->getEndMeterBlack();
            $data ["start_meter_color"]      = $object->getStartMeterColor();
            $data ["end_meter_color"]        = $object->getEndMeterColor();
            $data ["start_meter_printblack"] = $object->getStartMeterBlack();
            $data ["end_meter_printblack"]   = $object->getEndMeterBlack();
            $data ["start_meter_printcolor"] = $object->getStartMeterPrintcolor();
            $data ["end_meter_printcolor"]   = $object->getEndMeterPrintcolor();
            $data ["start_meter_copyblack"]  = $object->getStartMeterCopyblack();
            $data ["end_meter_copyblack"]    = $object->getEndMeterCopyblack();
            $data ["start_meter_copycolor"]  = $object->getStartMeterCopycolor();
            $data ["start_meter_copycolor"]  = $object->getEndMeterCopycolor();
            $data ["start_meter_fax"]        = $object->getStartMeterFax();
            $data ["end_meter_fax"]          = $object->getEndMeterFax();
            $data ["start_meter_scan"]       = $object->getStartMeterScan();
            $data ["end_meter_scan"]         = $object->getEndMeterScan();
            $data ["is_excluded"]            = $object->getIsExcluded();
            $data ["is_leased"]              = $object->getIsLeased();
            $data ["service_cost_per_page"]  = $object->getServiceCostPerPage();

            $primaryKey = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}