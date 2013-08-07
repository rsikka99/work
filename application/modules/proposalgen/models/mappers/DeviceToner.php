<?php
/**
 * Class Proposalgen_Model_Mapper_DeviceToner
 */
class Proposalgen_Model_Mapper_DeviceToner extends My_Model_Mapper_Abstract
{
    public $col_tonerId = 'toner_id';
    public $col_masterDeviceId = 'master_device_id';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_DeviceToner';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_DeviceToner
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_DeviceToner to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_DeviceToner
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
     * Saves (updates) an instance of Proposalgen_Model_DeviceToner to the database.
     *
     * @param $object     Proposalgen_Model_DeviceToner
     *                    The Device Toner model to save to the database
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
                                                                "{$this->col_tonerId} = ?"        => $primaryKey[0],
                                                                "{$this->col_masterDeviceId} = ?" => $primaryKey[1]
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
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
                "{$this->col_tonerId} = ?"        => $object->toner_id,
                "{$this->col_masterDeviceId} = ?" => $object->master_device_id,

            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_tonerId} = ?"        => $object[0],
                "{$this->col_masterDeviceId} = ?" => $object[1],
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Device Toner based on it's primaryKey
     *
     * @param $id array
     *            The id of the Device Toner to find
     *
     * @return Proposalgen_Model_DeviceToner
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_DeviceToner)
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
        $object = new Proposalgen_Model_DeviceToner($row->toArray());

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
     * @return Proposalgen_Model_DeviceToner
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_DeviceToner($row->toArray());

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
     * @return Proposalgen_Model_DeviceToner[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_DeviceToner($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
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
            "{$this->col_tonerId} = ?"        => $id[0],
            "{$this->col_masterDeviceId} = ?" => $id[1],
        );
    }

    /**
     * @param Proposalgen_Model_DeviceToner $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array($object->toner_id, $object->master_device_id);
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
            $tonerId = $deviceToner->toner_id;
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
        return $this->fetchAll(array("{$this->col_tonerId} = ?" => $tonerId));
    }

}