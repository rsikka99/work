<?php
/**
 * Class Memjetoptimization_Model_Mapper_Device_Instance_Replacement_Master_Device
 */
class Memjetoptimization_Model_Mapper_Device_Instance_Replacement_Master_Device extends My_Model_Mapper_Abstract
{
    /**
     * /*
     * Column Definitions
     */
    public $col_deviceInstanceId = 'deviceInstanceId';
    public $col_masterDeviceId = 'masterDeviceId';
    public $col_memjetOptimizationId = 'memjetOptimizationId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Memjetoptimization_Model_DbTable_Device_Instance_Replacement_Master_Device';

    /**
     * Gets an instance of the mapper
     *
     * @return Memjetoptimization_Model_Mapper_Device_Instance_Replacement_Master_Device
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Memjetoptimization_Model_Device_Instance_Replacement_Master_Device to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Memjetoptimization_Model_Device_Instance_Replacement_Master_Device
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());
        // Insert the data
        $id = $this->getDbTable()->insert($data);
        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Memjetoptimization_Model_Device_Instance_Replacement_Master_Device to the database.
     *
     * @param $object     Memjetoptimization_Model_Device_Instance_Replacement_Master_Device
     *                    The Device_Instance_Master_Device model to save to the database
     * @param $primaryKey mixed
     *                    Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

        if ($primaryKey === null)
        {
            $primaryKey = $this->getPrimaryKeyValueForObject($object);
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_deviceInstanceId} = ?"     => $primaryKey[0],
                                                                "{$this->col_memjetOptimizationId} = ?" => $primaryKey[1]
                                                           ));
        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Memjetoptimization_Model_Device_Instance_Replacement_Master_Device or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Memjetoptimization_Model_Device_Instance_Replacement_Master_Device)
        {
            $id          = array($object->deviceInstanceId, $object->memjetOptimizationId);
            $whereClause = $this->getPrimaryKeyValueForObject($object);
        }
        else
        {
            $id          = array($object[0], $object[1]);
            $whereClause = array(
                "{$this->col_deviceInstanceId} = ?"     => $object[0],
                "{$this->col_memjetOptimizationId} = ?" => $object[1]
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);
        $this->deleteItemFromCache($id);

        return $rowsAffected;
    }

    /**
     * Finds a Device_Instance_Master_Device based on it's primaryKey
     *
     * @param array $id The id of the Device_Instance_Master_Device to find
     *
     * @return Memjetoptimization_Model_Device_Instance_Replacement_Master_Device
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Memjetoptimization_Model_Device_Instance_Replacement_Master_Device)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id[0], $id[1]);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new Memjetoptimization_Model_Device_Instance_Replacement_Master_Device($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Device_Instance_Master_Device
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return Memjetoptimization_Model_Device_Instance_Replacement_Master_Device
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Memjetoptimization_Model_Device_Instance_Replacement_Master_Device($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Device_Instance_Master_Devices
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL An SQL LIMIT offset.
     *
     * @return Memjetoptimization_Model_Device_Instance_Replacement_Master_Device []
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Memjetoptimization_Model_Device_Instance_Replacement_Master_Device($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Deletes all device instance replacements
     *
     * @param $memjetOptimizationId
     *
     * @return int
     */
    public function deleteAllDeviceInstanceReplacementsByMemjetOptimizationId ($memjetOptimizationId)
    {
        $rowsAffected = $this->getDbTable()->delete(array("{$this->col_memjetOptimizationId} = ?" => $memjetOptimizationId));

        return $rowsAffected;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param int $id
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return array(
            "{$this->col_deviceInstanceId} = ?"     => $id[0],
            "{$this->col_memjetOptimizationId} = ?" => $id[1]
        );
    }

    /**
     * @param Memjetoptimization_Model_Device_Instance_Replacement_Master_Device $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array($object->deviceInstanceId, $object->memjetOptimizationId);
    }

    /**
     * Returns an array of unique master device for the Memjet optimization.
     *
     * @param $memjetOptimizationId
     *
     * @return array
     */
    public function fetchUniqueReplacementDeviceInstancesForMemjetOptimization ($memjetOptimizationId)
    {
        $db                   = $this->getDbTable()->getDefaultAdapter();
        $memjetOptimizationId = $db->quote($memjetOptimizationId, 'INTEGER');

        $select = $db->select()
                     ->from("{$this->getTableName()}", new Zend_Db_Expr("DISTINCT {$this->col_masterDeviceId} "))
                     ->where("{$this->col_memjetOptimizationId} = ?", $memjetOptimizationId);

        $masterDeviceIds = array();
        foreach ($db->query($select)->fetchAll() as $row)
        {
            $masterDeviceIds [] = $row[$this->col_masterDeviceId];
        }

        return $masterDeviceIds;
    }

    /**
     * Takes in a Memjet optimization id, and a master device id, and returns the count of found devices
     *
     * @param $masterDeviceId         int
     * @param $memjetoptimizationId   int
     *
     * @return int The count of master devices in this table for a specific Memjet optimization id
     */
    public function countReplacementDevicesById ($memjetoptimizationId, $masterDeviceId)
    {
        return $this->count(array("{$this->col_memjetOptimizationId} = ?" => $memjetoptimizationId, "{$this->col_masterDeviceId} = ?" => $masterDeviceId));
    }
}