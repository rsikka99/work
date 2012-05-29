<?php

class Proposalgen_Model_Mapper_DeviceToner extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_DeviceToner";
    static $_instance;

    /**
     *
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
     * Maps a database row object to an Application_Model
     *
     * @param Zend_Db_Table_Row $row            
     * @return The appropriate Application_Model
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object = new Proposalgen_Model_DeviceToner();
            $object->setTonerId($row->toner_id)->setMasterDeviceId($row->master_device_id);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a device toner row", 0, $e);
        }
        return $object;
    }

    public function save (Proposalgen_Model_DeviceToner $object)
    {
        $primaryKey = false;
        try
        {
            $data ["toner_id"] = $object->getTonerId();
            $data ["master_device_id"] = $object->getMasterDeviceId();
            
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