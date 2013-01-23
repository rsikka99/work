<?php
class Proposalgen_Model_Mapper_UserDeviceOverride extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_UserDeviceOverride";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_UserDeviceOverride
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
     * @return Proposalgen_Model_UserDeviceOverride
     * @throws Exception
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                         = new Proposalgen_Model_UserDeviceOverride();
            $object->setUserId              = $row->user_id;
            $object->setMasterDeviceId      = $row->master_device_id;
            $object->setOverrideDevicePrice = $row->price;
            $object->setIsLeased            = $row->is_leased;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a user device override row", 0, $e);
        }

        return $object;
    }

    /**
     * @param Proposalgen_Model_UserDeviceOverride $object
     *
     * @return string
     * @throws Exception
     */
    public function save ($object)
    {
        try
        {
            $data ["user_id"]          = $object->userId;
            $data ["master_device_id"] = $object->masterDeviceId;
            $data ["price"]            = $object->overrideDevicePrice;
            $data ["is_leased"]        = $object->isLeased;
            $primaryKey                = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }
}