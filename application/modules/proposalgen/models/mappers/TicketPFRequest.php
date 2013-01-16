<?php
class Proposalgen_Model_Mapper_TicketPFRequest extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TicketPFRequest";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_TicketPFRequest
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
     * @param mixed $primaryKey
     *
     * @return Proposalgen_Model_TicketPFRequest
     */
    public function find ($primaryKey)
    {
        return parent::find($primaryKey);
    }


    /**
     * Maps a database row object to an Proposalgen_Model
     *
     * @param Zend_Db_Table_Row $row
     *
     * @throws Exception
     * @return Proposalgen_Model_TicketPFRequest
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                     = new Proposalgen_Model_TicketPFRequest();
            $object->ticketId           = $row->ticket_id;
            $object->devicePfId         = $row->pf_device_id;
            $object->userId             = $row->user_id;
            $object->deviceManufacturer = $row->manufacturer;
            $object->printerModel       = $row->printer_model;
            $object->launchDate         = $row->launch_date;
            $object->devicePrice        = $row->cost;
            $object->serviceCostPerPage = $row->service_cost_per_page;
            $object->tonerConfig        = $row->toner_config;
            $object->isCopier           = $row->is_copier;
            $object->isFax              = $row->is_fax;
            $object->isDuplex           = $row->is_duplex;
            $object->isScanner          = $row->is_scanner;
            $object->ppmBlack           = $row->PPM_black;
            $object->ppmColor           = $row->PPM_color;
            $object->dutyCycle          = $row->duty_cycle;
            $object->wattsPowerNormal   = $row->watts_power_normal;
            $object->wattsPowerIdle     = $row->watts_power_idle;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a ticket printer request row", 0, $e);
        }

        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     *
     * @param Proposalgen_Model_TicketPFRequest $object
     *
     * @throws Exception
     * @return string
     */
    public function save ($object)
    {
        try
        {
            $data ["ticket_id"]             = $object->TicketId;
            $data ["pf_device_id"]          = $object->DevicesPfId;
            $data ["user_id"]               = $object->UserId;
            $data ["manufacturer"]          = $object->DeviceManufacturer;
            $data ["printer_model"]         = $object->PrinterModel;
            $data ["launch_date"]           = $object->LaunchDate;
            $data ["cost"]                  = $object->DevicePrice;
            $data ["service_cost_per_page"] = $object->ServiceCostPerPage;
            $data ["toner_config"]          = $object->TonerConfig;
            $data ["is_copier"]             = $object->IsCopier;
            $data ["is_fax"]                = $object->IsFax;
            $data ["is_duplex"]             = $object->IsDuplex;
            $data ["is_scanner"]            = $object->IsScanner;
            $data ["PPM_black"]             = $object->PpmBlack;
            $data ["PPM_color"]             = $object->PpmColor;
            $data ["duty_cycle"]            = $object->DutyCycle;
            $data ["watts_power_normal"]    = $object->WattsPowerNormal;
            $data ["watts_power_idle"]      = $object->WattsPowerIdle;
            $primaryKey                     = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}