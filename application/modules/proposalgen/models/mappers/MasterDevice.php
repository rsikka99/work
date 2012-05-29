<?php

class Proposalgen_Model_Mapper_MasterDevice extends Tangent_Model_Mapper_Abstract
{
    
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_MasterDevice";
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

    static $_masterDevices = array();
    public function find($id)
    {
        if (!array_key_exists($id, self::$_masterDevices))
        {
            self::$_masterDevices[$id] = parent::find($id);
        }
        return self::$_masterDevices[$id];
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
            $object = new Proposalgen_Model_MasterDevice();
            $object->setMasterDeviceId($row->master_device_id)
                ->setManufacturerId($row->mastdevice_manufacturer)
                ->setPrinterModel($row->printer_model)
                ->setTonerConfigId($row->toner_config_id)
                ->setIsCopier($row->is_copier)
                ->setIsFax($row->is_fax)
                ->setIsScanner($row->is_scanner)
                ->setIsDuplex($row->is_duplex)
                ->setIsReplacementDevice($row->is_replacement_device)
                ->setWattsPowerNormal($row->watts_power_normal)
                ->setWattsPowerIdle($row->watts_power_idle)
                ->setDevicePrice($row->device_price)
                ->setServiceCostPerPage($row->service_cost_per_page)
                ->setLaunchDate($row->launch_date)
                ->setDateCreated($row->date_created)
                ->setDutyCycle($row->duty_cycle)
                ->setPPMBlack($row->PPM_black)
                ->setPPMColor($row->PPM_color)
                ->setIsLeased($row->is_leased)
                ->setLeasedTonerYield($row->leased_toner_yield);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a master device instance row", 0, $e);
        }
        return $object;
    }

    /**
     * Saved an Proposalgen_Model_ object to the database
     * @param unknown_type $object
     */
    public function save (Proposalgen_Model_MasterDevice $object)
    {
        $primaryKey = 0;
        try
        {
            $data ["master_device_id"] = $object->getMasterDeviceId();
            $data ["mastdevice_manufacturer"] = $object->getManufacturerId();
            $data ["printer_model"] = $object->getPrinterModel();
            $data ["toner_config_id"] = $object->getTonerConfigId();
            $data ["is_copier"] = $object->getIsCopier();
            $data ["is_fax"] = $object->getIsFax();
            $data ["is_scanner"] = $object->getIsScanner();
            $data ["is_duplex"] = $object->getIsDuplex();
            $data ["is_replacement_device"] = $object->getIsReplacementDevice();
            $data ["watts_power_normal"] = $object->getWattsPowerNormal();
            $data ["watts_power_idle"] = $object->getWattsPowerIdle();
            $data ["device_price"] = $object->getDevicePrice();
            $data ["service_cost_per_page"] = $object->getServiceCostPerPage();
            $data ["launch_date"] = $object->getLaunchDate();
            $data ["date_created"] = $object->getDateCreated();
            $data["duty_cycle"] = $object->getDutyCycle();
            $data["PPM_black"] = $object->getPPMBlack();
            $data["PPM_color"] = $object->getPPMColor();    
            $data["is_leased"] = $object->getIsLeased();
            $data["leased_toner_yield"] = $object->getLeasedTonerYield();          
            
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