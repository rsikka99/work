<?php

/**
 * Class Quotegen_Model_Mapper_Device
 */
class Quotegen_Model_Mapper_Device extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_Device';

    /*
     * Define the primary key of the model association
    */
    public $col_masterDeviceId = 'masterDeviceId';
    public $col_dealerId = 'dealerId';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_Device
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_Device to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_Device
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $object->toArray();

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_Device to the database.
     *
     * @param $object     Quotegen_Model_Device
     *                    The device model to save to the database
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
            $primaryKey [] = $data [$this->col_masterDeviceId];
            $primaryKey [] = $data [$this->col_dealerId];
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
     *                This can either be an instance of Quotegen_Model_Device or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_Device)
        {
            $whereClause = array(
                "{$this->col_masterDeviceId} = ?" => $object->masterDeviceId,
                "{$this->col_dealerId} = ?"       => $object->dealerId,

            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_masterDeviceId} = ?" => $object[0],
                "{$this->col_dealerId} = ?"       => $object[1],
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a device based on it's primaryKey
     *
     * @param array $id The id of the device to find
     *
     * @return Quotegen_Model_Device
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_Device)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id [0], $id [1]);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new Quotegen_Model_Device($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a device
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return Quotegen_Model_Device
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Quotegen_Model_Device($row->toArray());

        // Save the object into the cache
        $primaryKey [0] = $object->masterDeviceId;
        $primaryKey [1] = $object->dealerId;
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all devices
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL: A SQL LIMIT offset.
     *
     * @return Quotegen_Model_Device[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Quotegen_Model_Device($row->toArray());

            // Save the object into the cache
            $primaryKey [0] = $object->masterDeviceId;
            $primaryKey [1] = $object->dealerId;
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
            "{$this->col_masterDeviceId} = ?" => $id [0],
            "{$this->col_dealerId} = ?"       => $id [1],
        );
    }

    /**
     * @param Quotegen_Model_Device $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            (int)$object->masterDeviceId,
            (int)$object->dealerId
        );
    }

    /**
     * Fetches a list of devices for the dealer
     *
     * @param int $dealerId
     *
     * @return Quotegen_Model_Device[]
     */
    public function fetchQuoteDeviceListForDealer ($dealerId)
    {
        $devices = $this->fetchAll(array("{$this->col_dealerId} = ?" => $dealerId));

        return $devices;
    }

    public function searchByName ($searchTerm, $dealerId = null, $manufacturerId = null)
    {
        $manufacturerMapper = Proposalgen_Model_Mapper_Manufacturer::getInstance();
        $masterDeviceMapper = Proposalgen_Model_Mapper_MasterDevice::getInstance();

        $returnLimit = 10;
        $sortOrder   = 'device_name ASC';

        $caseStatement = new Zend_Db_Expr("*, CASE WHEN md.isCopier AND md.tonerConfigId = 1 THEN 'Monochrome MFP'
            WHEN md.isCopier AND md.tonerConfigId > 1 THEN 'Color MFP'
            WHEN NOT md.isCopier AND md.tonerConfigId > 1 THEN 'Color '
            WHEN NOT md.isCopier AND md.tonerConfigId = 1 THEN 'Monochrome'
            END AS deviceType");


        $db     = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array($this->getTableName()), $caseStatement)
               ->joinLeft(array("md" => $masterDeviceMapper->getTableName()), "{$this->getTableName()}.{$this->col_masterDeviceId} = md.{$masterDeviceMapper->col_id}", array("{$masterDeviceMapper->col_id}"))
               ->joinLeft(array("m" => $manufacturerMapper->getTableName()), "md.{$masterDeviceMapper->col_manufacturerId} = m.{$manufacturerMapper->col_id}", array($manufacturerMapper->col_fullName, "device_name" => new Zend_Db_Expr("concat({$manufacturerMapper->col_fullName},' ', {$masterDeviceMapper->col_modelName})")))
               ->where("concat({$manufacturerMapper->col_fullName},' ', {$masterDeviceMapper->col_modelName}) LIKE ? AND m.isDeleted = 0")
               ->limit($returnLimit)
               ->order($sortOrder);

        /*
         * Filter by manufacturer id if provided
         */
        if ($manufacturerId)
        {
            $select->where("{$manufacturerMapper->col_fullName} = ?", $manufacturerId);
        }

        if ($dealerId)
        {
            $select->where("{$this->getTableName()}.$this->col_dealerId = ?", $dealerId);
        }

        $stmt   = $db->query($select, $searchTerm);
        $result = $stmt->fetchAll();

        return $result;
    }
}

