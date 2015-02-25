<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class DeviceInstanceMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class DeviceInstanceMapper extends My_Model_Mapper_Abstract
{
    const MAX_DEVICE_INSTANCES = 20000;

    /*
     * Column Definitions
     */
    public $col_id             = 'id';
    public $col_rmsUploadId    = 'rmsUploadId';
    public $col_rmsUploadRowId = 'rmsUploadRowId';
    public $col_useUserData    = 'useUserData';
    public $col_isExcluded     = 'isExcluded';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\DeviceInstanceDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return DeviceInstanceMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object DeviceInstanceModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel to the database.
     *
     * @param $object     DeviceInstanceModel
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
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_id} = ?" => $primaryKey,
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceModel or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof DeviceInstanceModel)
        {
            $whereClause = [
                "{$this->col_id} = ?" => $object->id,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_id} = ?" => $object,
            ];
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
     * @return DeviceInstanceModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof DeviceInstanceModel)
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
        $object = new DeviceInstanceModel($row->toArray());

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
     * @return DeviceInstanceModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new DeviceInstanceModel($row->toArray());

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
     * @return DeviceInstanceModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new DeviceInstanceModel($row->toArray());

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
        return [
            "{$this->col_id} = ?" => $id,
        ];
    }

    /**
     * @param DeviceInstanceModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Counts how many device instance rows we have for the report
     *
     * @param $rmsUploadId
     *
     * @return int
     */
    public function countRowsForRmsUpload ($rmsUploadId)
    {
        return $this->count(["{$this->col_rmsUploadId} = ?" => $rmsUploadId]);
    }

    /**
     * Fetches all rows for a report
     *
     * @param $rmsUploadId
     *
     * @return DeviceInstanceModel[]
     */
    public function fetchAllForRmsUpload ($rmsUploadId)
    {
        return $this->fetchAll(["{$this->col_rmsUploadId} = ?" => $rmsUploadId], null, DeviceInstanceMapper::MAX_DEVICE_INSTANCES);
    }

    /**
     * Fetches all devices for an upload with the same rms provider and model id
     *
     * @param int $rmsUploadId
     * @param int $rmsProviderId
     * @param int $rmsModelId
     *
     * @return DeviceInstanceModel[]
     */
    public function fetchAllWithRmsModelId ($rmsUploadId, $rmsProviderId, $rmsModelId)
    {
        $dbTable            = $this->getDbTable();
        $rmsUploadRowMapper = RmsUploadRowMapper::getInstance();

        $select = $dbTable->select()
                          ->from(["di" => $this->getTableName()])
                          ->join(["rum" => $rmsUploadRowMapper->getTableName()], "rum.{$rmsUploadRowMapper->col_id} = di.{$this->col_rmsUploadRowId}", [])
                          ->where("di.{$this->col_rmsUploadId} = ?", $rmsUploadId)
                          ->where("rum.{$rmsUploadRowMapper->col_rmsProviderId} = ?", $rmsProviderId)
                          ->where("rum.{$rmsUploadRowMapper->col_rmsModelId} = ?", $rmsModelId);

        $query = $dbTable->getAdapter()->query($select);

        $results = $query->fetchAll();

        $deviceInstances = [];
        foreach ($results as $result)
        {
            $object = new DeviceInstanceModel($result);

            // Save the object into the cache
            $this->saveItemToCache($object);

            $deviceInstances [] = $object;
        }

        return $deviceInstances;
    }

    /**
     * This function fetches match up devices
     *
     * @param int     $rmsUploadId
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
     * @return number|DeviceInstanceModel[] Returns an array, or if justCount is true then it will count how many rows are
     *           available
     */
    public function fetchDevicesInstancesForMapping ($rmsUploadId, $sortColumn = 'id', $sortDirection = 'ASC', $limit = null, $offset = null, $justCount = false)
    {
        /*
         * Setup our where clause
         */
        $whereClause = [
            "{$this->col_rmsUploadId} = ?" => $rmsUploadId,
        ];


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
            $order = [
                "{$sortColumn} {$sortDirection}"
            ];

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
     * @param        $rmsUploadId
     * @param string $sortColumn
     * @param string $sortDirection
     * @param null   $limit
     * @param null   $offset
     * @param bool   $justCount
     * @param bool   $onlyIncluded
     *
     * @return DeviceInstanceModel[]|int
     */
    public function getMappedDeviceInstances ($rmsUploadId, $sortColumn = 'id', $sortDirection = 'ASC', $limit = null, $offset = null, $justCount = false, $onlyIncluded = false)
    {
        $dbTable                          = $this->getDbTable();
        $deviceInstanceTableName          = $this->getTableName();
        $deviceInstanceMasterDeviceMapper = DeviceInstanceMasterDeviceMapper::getInstance();
        $masterDeviceMapper               = MasterDeviceMapper::getInstance();
        $manufacturerMapper               = ManufacturerMapper::getInstance();

        $columns = null;
        if ($justCount)
        {
            $columns = ["count" => "COUNT(*)"];
        }
        else
        {
            $columns = ["di.*"];
        }

        $select = $dbTable->select()->from(["di" => $deviceInstanceTableName], $columns)
                          ->distinct(true)
                          ->joinLeft(["di_md" => $deviceInstanceMasterDeviceMapper->getTableName()], "di_md.{$deviceInstanceMasterDeviceMapper->col_deviceInstanceId} = di.{$this->col_id}", [])
                          ->joinLeft(["md" => $masterDeviceMapper->getTableName()], "md.{$masterDeviceMapper->col_id} = di_md.{$deviceInstanceMasterDeviceMapper->col_masterDeviceId}", [])
                          ->joinLeft(["m" => $manufacturerMapper->getTableName()], "md.{$masterDeviceMapper->col_manufacturerId} = m.{$manufacturerMapper->col_id}", [])
                          ->where("di.{$this->col_rmsUploadId} = ?", $rmsUploadId)
                          ->where("di_md.{$deviceInstanceMasterDeviceMapper->col_masterDeviceId} IS NOT NULL OR di.{$this->col_useUserData} = 1");

        if ($onlyIncluded)
        {
            $select->where("di.{$this->col_isExcluded} = 0");
        }

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

            $deviceInstances = [];
            foreach ($results as $result)
            {
                $object = new DeviceInstanceModel($result);

                // Save the object into the cache
                $this->saveItemToCache($object);

                $deviceInstances [] = $object;
            }

            return $deviceInstances;
        }
    }
}