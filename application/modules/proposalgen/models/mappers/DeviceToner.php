<?php
/**
 * Class Proposalgen_Model_Mapper_DeviceToner
 */
class Proposalgen_Model_Mapper_DeviceToner extends Tangent_Model_Mapper_Abstract
{
    protected $_defaultDbTableClassName = "Proposalgen_Model_DbTable_DeviceToner";
    static $_instance;
    /*
 * Column Definitions
 */
    public $col_tonerId = 'toner_id';
    public $col_masterDeviceId = 'master_device_id';

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
     * @return Proposalgen_Model_DeviceToner[]
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

    /**
     * Finds a Device Toner based on it's primaryKey
     *
     * @param $id int[]
     *            The id of the Device TOner to find
     *
     * @return Proposalgen_Model_DeviceToner
     */
    public function find ($id)
    {
        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id[0], $id[1]);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new Proposalgen_Model_DeviceToner($row->toArray());

        // Save the object into the cache

        return $object;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Proposalgen_Model_DeviceToner or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_DeviceToner)
        {
            $whereClause = array(
                "{$this->col_tonerId} = ?" => $object->tonerId,
                "{$this->col_masterDeviceId} = ?" => $object->masterDeviceId,

            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_tonerId} = ?" => $object[0],
                "{$this->col_masterDeviceId} = ?" => $object[1],
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }
}