<?php
/**
 * Class Proposalgen_Model_Mapper_DeviceToner
 */
class Proposalgen_Model_Mapper_DeviceToner extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_DeviceToner";
    static $_instance;

    /**
     * @return Proposalgen_Model_Mapper_DeviceToner
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
     * @return Proposalgen_Model_DeviceToner
     */
    public function mapRowToObject ($row)
    {
        $object = null;
        try
        {
            $object                 = new Proposalgen_Model_DeviceToner();
            $object->tonerId        = $row->toner_id;
            $object->masterDeviceId = $row->master_device_id;
        }
        catch (Exception $e)
        {
            throw new Exception("Failed to map a device toner row", 0, $e);
        }

        return $object;
    }

    /**
     * @param Proposalgen_Model_DeviceToner $object
     *
     * @return string
     * @throws Exception
     */
    public function save ($object)
    {
        try
        {
            $data ["toner_id"]         = $object->tonerId;
            $data ["master_device_id"] = $object->masterDeviceId;
            $primaryKey                = $this->saveRow($data);
        }
        catch (Exception $e)
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.", 0, $e);
        }

        return $primaryKey;
    }

    /**
     * Gets all the toners for a master device
     *
     * @param $masterDeviceId
     *
     * @return Proposalgen_Model_Toner[]
     */
    public function getDeviceToners ($masterDeviceId)
    {
        $deviceToners = $this->fetchAll(array('master_device_id = ?' => $masterDeviceId));
        $entries      = array();
        foreach ($deviceToners as $deviceToner)
        {
            $tonerId = $deviceToner->tonerId;
            $toner   = Proposalgen_Model_Mapper_Toner::getInstance()->find($tonerId);

            $object = new Proposalgen_Model_Toner($toner->toArray());

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Fetches all device toner entries for a toner id
     *
     * @param int $tonerId The toner id to lookup
     *
     * @return Proposalgen_Model_DeviceToner[]
     */
    public function fetchDeviceTonersByTonerId ($tonerId)
    {
        return $this->fetchAll(array('toner_id = ?' => $tonerId));
    }
}