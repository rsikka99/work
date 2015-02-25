<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers;

use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationDeviceInstanceModel;
use My_Model_Mapper_Abstract;
use Zend_Db;
use Zend_Db_Expr;
use Zend_Db_Table;
use Zend_Db_Table_Select;

/**
 * Class HardwareOptimizationDeviceInstanceMapper
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers
 */
class HardwareOptimizationDeviceInstanceMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_deviceInstanceId       = 'deviceInstanceId';
    public $col_hardwareOptimizationId = 'hardwareOptimizationId';
    public $col_action                 = 'action';
    public $col_masterDeviceId         = 'masterDeviceId';
    public $col_deviceSwapReasonId     = 'deviceSwapReasonId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables\HardwareOptimizationDeviceInstanceDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return HardwareOptimizationDeviceInstanceMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationDeviceInstanceModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object HardwareOptimizationDeviceInstanceModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationDeviceInstanceModel to the database.
     *
     * @param $object     HardwareOptimizationDeviceInstanceModel
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
            $primaryKey = [$data[$this->col_deviceInstanceId], $data[$this->col_hardwareOptimizationId]];
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
     *                This can either be an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationDeviceInstanceModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof HardwareOptimizationDeviceInstanceModel)
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
     * @return HardwareOptimizationDeviceInstanceModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof HardwareOptimizationDeviceInstanceModel)
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
        $object = new HardwareOptimizationDeviceInstanceModel($row->toArray());

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
     * @return HardwareOptimizationDeviceInstanceModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new HardwareOptimizationDeviceInstanceModel($row->toArray());

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
     * @return HardwareOptimizationDeviceInstanceModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new HardwareOptimizationDeviceInstanceModel($row->toArray());

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
            "{$this->col_deviceInstanceId} = ?"       => $id[0],
            "{$this->col_hardwareOptimizationId} = ?" => $id[1],
        ];
    }

    /**
     * @param HardwareOptimizationDeviceInstanceModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [$object->deviceInstanceId, $object->hardwareOptimizationId];
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
        return $this->getDbTable()->update([
            $this->col_masterDeviceId     => new Zend_Db_Expr("NULL"),
            $this->col_deviceSwapReasonId => new Zend_Db_Expr("NULL"),
            $this->col_action             => HardwareOptimizationDeviceInstanceModel::ACTION_KEEP,
        ], [
            "{$this->col_hardwareOptimizationId} = ?" => $hardwareOptimizationId,
        ]);
    }

    /**
     * Gets all the master device ids with the quantities
     *
     * @param $hardwareOptimizationId
     *
     * @return array
     */
    public function getMasterDeviceQuantitiesForHardwareOptimization ($hardwareOptimizationId)
    {
        $select = $this->getDbTable()
                       ->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                       ->columns(["masterDeviceId", "quantity" => "COUNT(*)"])
                       ->group("masterDeviceId")
                       ->where("hardwareOptimizationId = ?", $hardwareOptimizationId)
                       ->where("masterDeviceId IS NOT NULL");

        return $select->query()->fetchAll(Zend_Db::FETCH_ASSOC);

    }

    public function deviceActionCounts ($hardwareOptimizationId)
    {
        $select = $this->getDbTable()
                       ->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
                       ->columns(["action" => "action", "count" => "COUNT(*)"])
                       ->group("action")
                       ->where("hardwareOptimizationId = ?", $hardwareOptimizationId);

        $results = $select->query()->fetchAll();

        $counts = [
            HardwareOptimizationDeviceInstanceModel::ACTION_DNR     => 0,
            HardwareOptimizationDeviceInstanceModel::ACTION_KEEP    => 0,
            HardwareOptimizationDeviceInstanceModel::ACTION_REPLACE => 0,
            HardwareOptimizationDeviceInstanceModel::ACTION_RETIRE  => 0,
            HardwareOptimizationDeviceInstanceModel::ACTION_UPGRADE => 0,
        ];

        foreach ($results as $result)
        {
            $counts[$result['action']] = (int)$result['count'];
        }

        return $counts;
    }
}