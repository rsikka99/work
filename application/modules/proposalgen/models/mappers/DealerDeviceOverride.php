<?php

class Proposalgen_Model_Mapper_DealerDeviceOverride extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_DealerDeviceOverride";
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
            $object = new Proposalgen_Model_DealerDeviceOverride();
            $object->setDealerCompanyId($row->dealer_company_id)
                ->setMasterDeviceId($row->master_device_id)
                ->setOverrideDevicePrice($row->override_device_price)
                ->setIsLeased($row->is_leased);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a dealer device override row", 0, $e);
        }
        return $object;
    }

    public function save (Proposalgen_Model_DealerDeviceOverride $object)
    {
        $primaryKey = false;
        try
        {
            $data ["dealer_company_id"] = $object->getDealerCompanyId();
            $data ["master_device_id"] = $object->getMasterDeviceId();
            $data ["override_device_price"] = $object->getOverrideDevicePrice();
            $data ["is_leased"] = $object->getIsLeased();
            
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