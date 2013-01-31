<?php
class Proposalgen_Model_Mapper_DeviceInstance extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_id = 'id';
    public $col_reportId = 'reportId';
    public $col_rmsUploadRowId = 'rmsUploadRowId';
    public $col_useUserData = 'useUserData';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_DeviceInstance';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_DeviceInstance
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_DeviceInstance to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_DeviceInstance
     *                The object to insert
     *
     * @return mixed The primary key of the new row
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
     * Saves (updates) an instance of Proposalgen_Model_DeviceInstance to the database.
     *
     * @param $object     Proposalgen_Model_DeviceInstance
     *                    The DeviceInstance model to save to the database
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
     *                This can either be an instance of Proposalgen_Model_DeviceInstance or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_DeviceInstance)
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
     * Finds a DeviceInstance based on it's primaryKey
     *
     * @param $id int
     *            The id of the DeviceInstance to find
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_DeviceInstance)
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
        $object = new Proposalgen_Model_DeviceInstance($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a DeviceInstance
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return Proposalgen_Model_DeviceInstance
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Proposalgen_Model_DeviceInstance($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all DeviceInstances
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
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Proposalgen_Model_DeviceInstance($row->toArray());

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
     * @param Proposalgen_Model_DeviceInstance $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Gets all the device instances for a report
     *
     * @param int $reportId
     * @param int $isLeased   Whether or not to fetch leased devices. When true it only returns leased devices
     * @param int $isExcluded Whether or not to fetch excluded devices. When true it only returns excluded devices
     *
     * @return Proposalgen_Model_DeviceInstance[]
     * @throws Exception
     */
    public function getDevicesForReport ($reportId, $isLeased = 0, $isExcluded = 0)
    {
        $masterDeviceTableName   = Proposalgen_Model_Mapper_MasterDevice::getInstance()->getTableName();
        $deviceInstanceTableName = $this->getTableName();

        $reportId = 10;
        $devices  = array();
        try
        {
            $dbTable = $this->getDbTable();
            $select  = $dbTable->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
            $select->joinLeft(array(
                                   'md' => $masterDeviceTableName
                              ), "{$deviceInstanceTableName}.master_device_id = md.id", '*')
                ->where("{$deviceInstanceTableName}.is_excluded = ?", $isExcluded)
                ->where("{$deviceInstanceTableName}.report_id = ?", $reportId)
                ->setIntegrityCheck(false)
                ->limit(1000);
            $rows = $dbTable->fetchAll($select);
            if (!$rows)
            {
                throw new Exception('No devices found');
            }
            else
            {
                foreach ($rows as $row)
                {
                    $device     = new Proposalgen_Model_DeviceInstance($row);
                    $devices [] = $device;
                }
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Error fetching devices for report", 0, $e);
        }

        return $devices;
    }


    /**
     * Counts how many device instance rows we have for the report
     *
     * @param $reportId
     *
     * @return int
     */
    public function countRowsForReport ($reportId)
    {
        return $this->count(array("{$this->col_reportId} = ?" => $reportId));
    }

    /**
     * Fetches all rows for a report
     *
     * @param $reportId
     *
     * @return Proposalgen_Model_DeviceInstance[]
     */
    public function fetchAllForReport ($reportId)
    {
        return $this->fetchAll(array("{$this->col_reportId} = ?" => $reportId));
    }

    /**
     * This function fetches match up devices
     *
     * @param int     $reportId
     * @param string  $sortColumn
     *            The column to sort by
     * @param string  $sortDirection
     *            The direction to sort
     * @param number  $limit
     *            The number of records to retrieve
     * @param number  $offset
     *            The record to start at
     * @param boolean $justCount
     *            If set to true this function will return an integer of the row count of all available rows
     *
     * @return number|Proposalgen_Model_DeviceInstance[] Returns an array, or if justCount is true then it will count how many rows are
     *           available
     */
    public function fetchDevicesInstancesForMapping ($reportId, $sortColumn, $sortDirection, $limit = null, $offset = null, $justCount = false)
    {
        /*
         * Setup our where clause
         */
        $whereClause = array(
            "{$this->col_reportId} = ?" => $reportId
        );


        // If we're just counting we only need to return the count
        if ($justCount)
        {
            return $this->count($whereClause);
        }
        else
        {
            /*
             * Parse our order
             */
            $order = array(
                "{$sortColumn} {$sortDirection}"
            );

            /*
             * Parse our Limit
             */
            if ($limit > 0)
            {
                $offset = ($offset > 0) ? $offset : null;
            }

            return $this->fetchAll($whereClause, $order, $limit, $offset);
        }
    }

    /**
     * @param      $reportId
     * @param      $sortColumn
     * @param      $sortDirection
     * @param null $limit
     * @param null $offset
     * @param bool $justCount
     *
     * @return Proposalgen_Model_DeviceInstance[]|int
     */
    public function getMappedDeviceInstances ($reportId, $sortColumn, $sortDirection, $limit = null, $offset = null, $justCount = false)
    {
        $dbTable                          = $this->getDbTable();
        $deviceInstanceTableName          = $this->getTableName();
        $deviceInstanceMasterDeviceMapper = Proposalgen_Model_Mapper_Device_Instance_Master_Device::getInstance();

        $columns = null;
        if ($justCount)
        {
            $columns = array("count" => "COUNT(*)");
        }

        $select = $dbTable->select()->from(array("di" => $deviceInstanceTableName), $columns)
            ->distinct(true)
            ->joinLeft(array("di_md" => $deviceInstanceMasterDeviceMapper->getTableName()), "di_md.{$deviceInstanceMasterDeviceMapper->col_deviceInstanceId} = di.{$this->col_id}", array())
            ->where("di.{$this->col_reportId} = ?", $reportId)
            ->where("di_md.{$deviceInstanceMasterDeviceMapper->col_masterDeviceId} IS NOT NULL OR di.{$this->col_useUserData} = 1");

        // If we're just counting we only need to return the count
        if ($justCount)
        {
            $query = $dbTable->getAdapter()->query($select);

            return (int)$query->fetchColumn();
        }
        else
        {
            /*
             * Parse our order
             */
            $select->order("di.{$sortColumn} {$sortDirection}");


            /*
             * Parse our Limit
             */
            if ($limit > 0)
            {
                $offset = ($offset > 0) ? $offset : null;
                $select->limit($limit, $offset);
            }

            $query = $dbTable->getAdapter()->query($select);

            $results = $query->fetchAll();

            $deviceInstances = array();
            foreach ($results as $result)
            {
                $object = new Proposalgen_Model_DeviceInstance($result);

                // Save the object into the cache
                $this->saveItemToCache($object);

                $deviceInstances [] = $object;
            }

            return $deviceInstances;
        }
    }
}