<?php

/**
 * Class Hardwareoptimization_Model_Mapper_Hardware_Optimization_DeviceInstance
 */
class Hardwareoptimization_Model_Mapper_Hardware_Optimization_DeviceInstance extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_deviceInstanceId = 'deviceInstanceId';
    public $col_hardwareOptimizationId = 'hardwareOptimizationId';
    public $col_action = 'action';
    public $col_masterDeviceId = 'masterDeviceId';
    public $col_deviceSwapReasonId = 'deviceSwapReasonId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Hardwareoptimization_Model_DbTable_Hardware_Optimization_DeviceInstance';

    /**
     * Gets an instance of the mapper
     *
     * @return Hardwareoptimization_Model_Mapper_Hardware_Optimization_DeviceInstance
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance
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
     * Saves (updates) an instance of Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance to the database.
     *
     * @param $object     Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance
     *                    Hardware optimization deviceInstance model to save to the database1
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
            $primaryKey = array($data[$this->col_deviceInstanceId], $data[$this->col_hardwareOptimizationId]);
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, $this->getWhereId($primaryKey));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance)
        {
            $whereClause = $this->getWhereId($this->getPrimaryKeyValueForObject($object));
        }
        else
        {
            $whereClause = $this->getWhereId($object);
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Template based on it's primaryKey
     *
     * @param array $id
     *            The id of Hardware optimization deviceInstance to find
     *
     * @return Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance)
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
        $object = new Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Template
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Templates
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
     * @return Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param array $id
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return array(
            "{$this->col_deviceInstanceId} = ?"       => $id[0],
            "{$this->col_hardwareOptimizationId} = ?" => $id[1],
        );
    }

    /**
     * @param Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array($object->deviceInstanceId, $object->hardwareOptimizationId);
    }

    /**
     * Resets all the hardware optimization device instances for a given hardware optimization
     *
     * @param int $hardwareOptimizationId
     *
     * @return int
     */
    public function resetAllForHardwareOptimization ($hardwareOptimizationId)
    {
        return $this->getDbTable()->update(array(
            $this->col_masterDeviceId     => new Zend_Db_Expr("NULL"),
            $this->col_deviceSwapReasonId => new Zend_Db_Expr("NULL"),
            $this->col_action             => Hardwareoptimization_Model_Hardware_Optimization_DeviceInstance::ACTION_KEEP,
        ), array(
            "{$this->col_hardwareOptimizationId} = ?" => $hardwareOptimizationId,
        ));
    }
}