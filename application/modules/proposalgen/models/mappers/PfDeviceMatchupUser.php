<?php

class Proposalgen_Model_Mapper_PfDeviceMatchupUser extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_PFMatchupUsers";
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
            $object = new Proposalgen_Model_PfDeviceMatchupUser();
            $object->setDevicesPfId($row->devices_pf_id)
                ->setMasterDeviceId($row->master_device_id)
                ->setUserId($row->user_id);
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to map a pf device match up user row", 0, $e);
        }
        return $object;
    }

    public function save (Proposalgen_Model_UserDeviceOverride $object)
    {
        $primaryKey = false;
        try
        {
            $data ["devices_pf_id"] = $object->getDevicesPfId();
            $data ["master_device_id"] = $object->getMasterDeviceId();
            $data ["user_id"] = $object->getUserId();
            
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