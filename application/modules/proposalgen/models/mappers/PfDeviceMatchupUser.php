<?php

class Proposalgen_Model_Mapper_PfDeviceMatchupUser extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_PFMatchupUser";
    static $_instance;

    /**
     *
     * @return Proposalgen_Model_Mapper_PfDeviceMatchupUser
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
     * @return Proposalgen_Model_PfDeviceMatchupUser
     */
    public function mapRowToObject (Zend_Db_Table_Row $row)
    {
        $object = null;
        try
        {
            $object                 = new Proposalgen_Model_PfDeviceMatchupUser();
            $object->devicesPfId    = $row->pf_device_id;
            $object->masterDeviceId = $row->master_device_id;
            $object->userId         = $row->user_id;
        }
        catch (Exception $e)
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
            $data ["pf_device_id"]     = $object->DevicesPfId;
            $data ["master_device_id"] = $object->masterDeviceId;
            $data ["user_id"]          = $object->userId;

            $primaryKey = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}
