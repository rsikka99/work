<?php
class Proposalgen_Model_Mapper_MasterDevice extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id = 'id';
    public $col_manufacturerId = 'manufacturerId';
    public $col_modelName = 'modelName';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_MasterDevice';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_MasterDevice
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_MasterDevice to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_MasterDevice
     *                The object to insert
     *
     * @return int The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());

        // Remove the id
        unset($data ["{$this->col_id}"]);

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        $object->id = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Proposalgen_Model_MasterDevice to the database.
     *
     * @param $object     Proposalgen_Model_MasterDevice
     *                    The masterDevice model to save to the database
     * @param $primaryKey mixed
     *                    Optional: The original primary key, in case we're changing it
     *
     * @return string The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

        if ($primaryKey === null)
        {
            $primaryKey = $data [$this->col_id];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_id} = ?" => $primaryKey
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Proposalgen_Model_MasterDevice or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_MasterDevice)
        {
            $whereClause = array(
                "{$this->col_id} = ?" => $object->id
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_id} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a masterDevice based on it's primaryKey
     *
     * @param $id int
     *            The id of the masterDevice to find
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_MasterDevice)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new Proposalgen_Model_MasterDevice($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a masterDevice
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_MasterDevice($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all masterDevices
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
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_MasterDevice($row->toArray());

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
            "{$this->col_id} = ?" => $id
        );
    }

    /**
     * Get all the printer model with the wild cards %<modelName>%
     *
     * @param string  $criteria
     * @param null    $order
     * @param int     $count
     * @param null    $offset
     *
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function fetchAllLikePrinterModel ($criteria, $order = null, $count = 25, $offset = null)
    {
        return $this->fetchAll(array("{$this->col_modelName} LIKE ?" => "%{$criteria}%"), $order, $count, $offset);
    }

    /**
     * Get all master devices that match the manufacturer id that has been passed.
     *
     * @param string      $criteria
     * @param null        $order
     * @param int         $count
     * @param null        $offset
     *
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function fetchAllByManufacturerId ($criteria, $order = null, $count = 25, $offset = null)
    {
        return $this->fetchAll(array("{$this->col_manufacturerId} = ?" => $criteria), $order, $count, $offset);
    }

    /**
     * Fetches all master devices that are available to be used in the quote generator.
     *
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function fetchAllAvailableMasterDevices ()
    {
        $qdTableName = Quotegen_Model_Mapper_Device::getInstance()->getTableName();

        $sql = "SELECT * FROM {$this->getTableName()} as md
        LEFT JOIN {$qdTableName} AS qd ON qd.masterDeviceId = md.id
        WHERE qd.masterDeviceId is null
        ORDER BY  md.manufacturer_id ASC, md.printer_model ASC
        ";

        $resultSet = $this->getDbTable()
            ->getAdapter()
            ->fetchAll($sql);

        $entries = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_MasterDevice($row);

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * @param Proposalgen_Model_MasterDevice $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Searches the master devices by modelName (uses LIKE %NAME% in where clause)
     * If a manufacturer id is supplied it is used to refine the search
     *
     * @param string $modelName
     * @param int    $manufacturerId
     *
     * @return Proposalgen_Model_MasterDevice[]
     */
    public function searchByModelName ($modelName, $manufacturerId)
    {
        $whereClause = array(
            "{$this->col_modelName} LIKE ?" => "%{$modelName}%"
        );
        if ($manufacturerId !== null)
        {
            $whereClause["{$this->col_manufacturerId} = ?"] = "{$manufacturerId}";
        }

        return $this->fetchAll($whereClause);
    }

    /**
     * Returns results from a custom query that combines manufacturer and master device tables.
     * It returns a list of devices that match (like statement) the search terms that have been passed.
     * You can search by manufacturer name (manufacturers table), model name (master_device table) an
     * or both.
     *
     * @param string   $searchTerm
     *
     * @param null|int $manufacturerId
     *
     * @return array
     */
    public function searchByName ($searchTerm, $manufacturerId = null)
    {
        $manufacturerMapper = Proposalgen_Model_Mapper_Manufacturer::getInstance();

        $returnLimit = 10;
        $sortOrder   = 'device_name ASC';

        $db     = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(array("md" => $this->getTableName()), array('modelName', 'id'))
            ->joinLeft(array("m" => $manufacturerMapper->getTableName()), "m.{$manufacturerMapper->col_id} = md.{$this->col_manufacturerId}", array($manufacturerMapper->col_fullName, "device_name" => new Zend_Db_Expr("concat({$manufacturerMapper->col_fullName},' ', {$this->col_modelName})")))
            ->where("concat({$manufacturerMapper->col_fullName},' ', {$this->col_modelName}) LIKE ? AND m.isDeleted = 0")
            ->limit($returnLimit)
            ->order($sortOrder);

        /*
         * Filter by manufacturer id if provided
         */
        if ($manufacturerId)
        {
            $select->where("{$manufacturerMapper->col_fullName} = ?", $manufacturerId);
        }

        $stmt   = $db->query($select, $searchTerm);
        $result = $stmt->fetchAll();

        return $result;
    }
}