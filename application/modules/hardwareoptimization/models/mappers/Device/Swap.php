<?php
/**
 * Class Hardwareoptimization_Model_Mapper_Device_Swap
 */
class Hardwareoptimization_Model_Mapper_Device_Swap extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_masterDeviceId = 'masterDeviceId';
    public $col_dealerId = 'dealerId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Hardwareoptimization_Model_DbTable_Device_Swap';

    /**
     * Gets an instance of the mapper
     *
     * @return Hardwareoptimization_Model_Mapper_Device_Swap
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Hardwareoptimization_Model_Device_Swap to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Hardwareoptimization_Model_Device_Swap
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
     * Saves (updates) an instance of Hardwareoptimization_Model_Device_Swap to the database.
     *
     * @param $object     Hardwareoptimization_Model_Device_Swap
     *                    The Device Swap model to save to the database
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
                                                                "{$this->col_masterDeviceId} = ?" => $primaryKey[0],
                                                                "{$this->col_dealerId} = ?"       => $primaryKey[1]
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Hardwareoptimization_Model_Device_Swap or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Hardwareoptimization_Model_Device_Swap)
        {
            $whereClause = array(
                "{$this->col_masterDeviceId} = ?" => $object->masterDeviceId,
                "{$this->col_dealerId} = ?"       => $object->dealerId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_masterDeviceId} = ?" => $object[0],
                "{$this->col_dealerId} = ?"       => $object[1]
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Device Swapbased on it's primaryKey
     *
     * @param $id array
     *            The id of the Device Swap to find
     *
     * @return Hardwareoptimization_Model_Device_Swap
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Hardwareoptimization_Model_Device_Swap)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find((int)$id[0], (int)$id[1]);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new Hardwareoptimization_Model_Device_Swap($row->toArray());

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
     * @return Hardwareoptimization_Model_Device_Swap
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Hardwareoptimization_Model_Device_Swap($row->toArray());

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
     * @return Hardwareoptimization_Model_Device_Swap[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Hardwareoptimization_Model_Device_Swap($row->toArray());

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
            "{$this->col_masterDeviceId} = ?" => $id[0],
            "{$this->col_dealerId} = ?"       => $id[1]
        );
    }

    /**
     * @param Hardwareoptimization_Model_Device_Swap $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->masterDeviceId,
            $object->dealerId
        );
    }
}