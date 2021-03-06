<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers;

use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonDefaultModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class DeviceSwapReasonDefaultMapper
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers
 */
class DeviceSwapReasonDefaultMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_deviceSwapReasonCategoryId = 'deviceSwapReasonCategoryId';
    public $col_dealerId                   = 'dealerId';
    public $col_deviceSwapReasonId         = 'deviceSwapReasonId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables\DeviceSwapReasonDefaultDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return DeviceSwapReasonDefaultMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonDefaultModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object DeviceSwapReasonDefaultModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonDefaultModel to the database.
     *
     * @param $object     DeviceSwapReasonDefaultModel
     *                    The Device Swap Reason Default model to save to the database
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
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_deviceSwapReasonCategoryId} = ?" => $primaryKey[0],
            "{$this->col_dealerId} = ?"                   => $primaryKey[1],
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\DeviceSwapReasonDefaultModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof DeviceSwapReasonDefaultModel)
        {
            $whereClause = [
                "{$this->col_deviceSwapReasonCategoryId} = ?" => $object->deviceSwapReasonCategoryId,
                "{$this->col_dealerId} = ?"                   => $object->dealerId,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_deviceSwapReasonCategoryId} = ?" => $object[0],
                "{$this->col_dealerId} = ?"                   => $object[1],
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Device Swap Reason Default based on it's primaryKey
     *
     * @param $id array
     *            The id of the Device Swap Reason Default to find
     *
     * @return DeviceSwapReasonDefaultModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof DeviceSwapReasonDefaultModel)
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
        $object = new DeviceSwapReasonDefaultModel($row->toArray());

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
     * @return DeviceSwapReasonDefaultModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new DeviceSwapReasonDefaultModel($row->toArray());

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
     * @return DeviceSwapReasonDefaultModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new DeviceSwapReasonDefaultModel($row->toArray());

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
        return [
            "{$this->col_deviceSwapReasonCategoryId} = ?" => $id[0],
            "{$this->col_dealerId} = ?"                   => $id[1],
        ];
    }

    /**
     * @param DeviceSwapReasonDefaultModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [
            $object->deviceSwapReasonCategoryId,
            $object->dealerId,
        ];
    }

    /**
     * @param $reasonId
     *
     * @return DeviceSwapReasonDefaultModel
     */
    public function findDefaultByReasonId ($reasonId)
    {
        return $this->fetch(["{$this->col_deviceSwapReasonId} = ?" => $reasonId]);
    }

    /**
     * @param $categoryId
     * @param $dealerId
     *
     * @return DeviceSwapReasonDefaultModel
     */
    public function findDefaultByDealerId ($categoryId, $dealerId)
    {
        return $this->fetch(["{$this->col_deviceSwapReasonCategoryId} = ?" => $categoryId, "{$this->col_dealerId} = ?" => $dealerId]);
    }

    /**
     * Dealers all the default reasons for a dealer
     *
     * @param $dealerId
     *
     * @return int
     */
    public function deleteDefaultReasonByDealerId ($dealerId)
    {
        return $this->getDbTable()->delete(["{$this->col_dealerId} = ?" => $dealerId]);
    }
}