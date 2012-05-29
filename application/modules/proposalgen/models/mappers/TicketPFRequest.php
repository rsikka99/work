<?php

class Proposalgen_Model_Mapper_TicketPFRequest extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_TicketPFRequests";
    static $_instance;

    /**
     * @return Tangent_Model_Mapper_Abstract
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
     * @param Zend_Db_Table_Row $row
     * @return The appropriate Proposalgen_Model
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_TicketPFRequest();
            $object->setTicketId($row->ticket_id)
            	->setDevicePfId($row->devices_pf_id)
            	->setUserId($row->user_id)
            	->setDeviceManufacturer($row->device_manufacturer)
            	->setPrinterModel($row->printer_model)
            	->setLaunchDate($row->launch_date)
            	->setDevicePrice($row->device_price)
            	->setServiceCostPerPage($row->service_cost_per_page)
            	->setTonerConfig($row->toner_config)
            	->setIsCopier($row->is_copier)
            	->setIsFax($row->is_fax)
            	->setIsDuplex($row->is_duplex)
            	->setIsScanner($row->is_scanner)
            	->setPpmBlack($row->PPM_black)
            	->setPpmColor($row->PPM_color)
            	->setDutyCycle($row->duty_cycle)
            	->setWattsPowerNormal($row->watts_power_normal)
            	->setWattsPowerIdle($row->watts_power_idle);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a ticket printer request row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * @param unknown_type $object
     */
    public function save (Proposalgen_Model_TicketPrinterRequest $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["ticket_id"] = $object->getTicketId();
            $data ["devices_pf_id"] = $object->getDevicesPfId();
            $data ["user_id"] = $object->getUserId();
        	$data ["device_manufacturer"] = $object->getDeviceManufacturer();
        	$data ["printer_model"] = $object->getPrinterModel();
        	$data ["launch_date"] = $object->getLaunchDate();
        	$data ["device_price"] = $object->getDevicePrice();
        	$data ["service_cost_per_page"] = $object->getServiceCostPerPage();
        	$data ["toner_config"] = $object->getTonerConfig();
        	$data ["is_copier"] = $object->getIsCopier();
        	$data ["is_fax"] = $object->getIsFax();
        	$data ["is_duplex"] = $object->getIsDuplex();
        	$data ["is_scanner"] = $object->getIsScanner();
        	$data ["PPM_black"] = $object->getPpmBlack();
        	$data ["PPM_color"] = $object->getPpmColor();
        	$data ["duty_cycle"] = $object->getDutyCycle();
        	$data ["watts_power_normal"] = $object->getWattsPowerNormal();
        	$data ["watts_power_idle"] = $object->getWattsPowerIdle();
            
            $primaryKey = $this->saveRow($data);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }
        return $primaryKey;
    }
}
?>