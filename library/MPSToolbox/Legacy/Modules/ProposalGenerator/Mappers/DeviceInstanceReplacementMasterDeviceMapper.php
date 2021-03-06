<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceReplacementMasterDeviceModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Expr;
use Zend_Db_Table_Select;

/**
 * Class DeviceInstanceReplacementMasterDeviceMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class DeviceInstanceReplacementMasterDeviceMapper extends My_Model_Mapper_Abstract
{
    /**
     * /*
     * Column Definitions
     */
    public $col_deviceInstanceId       = 'deviceInstanceId';
    public $col_masterDeviceId         = 'masterDeviceId';
    public $col_hardwareOptimizationId = 'hardwareOptimizationId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\DeviceInstanceReplacementMasterDeviceDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return DeviceInstanceReplacementMasterDeviceMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceReplacementMasterDeviceModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object DeviceInstanceReplacementMasterDeviceModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceReplacementMasterDeviceModel to the database.
     *
     * @param $object     DeviceInstanceReplacementMasterDeviceModel
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
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_deviceInstanceId} = ?"       => $primaryKey[0],
            "{$this->col_hardwareOptimizationId} = ?" => $primaryKey[1],
        ]);
        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceReplacementMasterDeviceModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof DeviceInstanceReplacementMasterDeviceModel)
        {
            $id          = [$object->deviceInstanceId, $object->hardwareOptimizationId];
            $whereClause = $this->getPrimaryKeyValueForObject($object);
        }
        else
        {
            $id          = [$object[0], $object[1]];
            $whereClause = [
                "{$this->col_deviceInstanceId} = ?"       => $object[0],
                "{$this->col_hardwareOptimizationId} = ?" => $object[1],
            ];
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
     * @return DeviceInstanceReplacementMasterDeviceModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof DeviceInstanceReplacementMasterDeviceModel)
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
        $object = new DeviceInstanceReplacementMasterDeviceModel($row->toArray());

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
     * @return DeviceInstanceReplacementMasterDeviceModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new DeviceInstanceReplacementMasterDeviceModel($row->toArray());

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
     * @return DeviceInstanceReplacementMasterDeviceModel []
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new DeviceInstanceReplacementMasterDeviceModel($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Deletes all device instance replacements
     *
     * @param $hardwareOptimizationId
     *
     * @return int
     */
    public function deleteAllDeviceInstanceReplacementsByHardwareOptimizationId ($hardwareOptimizationId)
    {
        $rowsAffected = $this->getDbTable()->delete(["{$this->col_hardwareOptimizationId} = ?" => $hardwareOptimizationId]);

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
        return [
            "{$this->col_deviceInstanceId} = ?"       => $id[0],
            "{$this->col_hardwareOptimizationId} = ?" => $id[1],
        ];
    }

    /**
     * @param DeviceInstanceReplacementMasterDeviceModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [$object->deviceInstanceId, $object->hardwareOptimizationId];
    }

    /**
     * Returns an array of unique master device for the hardware optimization.
     *
     * @param $hardwareOptimizationId
     *
     * @return array
     */
    public function fetchUniqueReplacementDeviceInstancesForHardwareOptimization ($hardwareOptimizationId)
    {
        $db                     = $this->getDbTable()->getDefaultAdapter();
        $hardwareOptimizationId = $db->quote($hardwareOptimizationId, 'INTEGER');

        $select = $db->select()
                     ->from("{$this->getTableName()}", new Zend_Db_Expr("DISTINCT {$this->col_masterDeviceId} "))
                     ->where("{$this->col_hardwareOptimizationId} = ?", $hardwareOptimizationId);

        $masterDeviceIds = [];
        foreach ($db->query($select)->fetchAll() as $row)
        {
            $masterDeviceIds [] = $row[$this->col_masterDeviceId];
        }

        return $masterDeviceIds;
    }

    /**
     * Takes in a hardware optimization id, and a master device id, and returns the count of found devices
     *
     * @param $masterDeviceId         int
     * @param $hardwareoptimizationId int
     *
     * @return int The count of master devices in this table for a specific hardware optimization id
     */
    public function countReplacementDevicesById ($hardwareoptimizationId, $masterDeviceId)
    {
        return $this->count(["{$this->col_hardwareOptimizationId} = ?" => $hardwareoptimizationId, "{$this->col_masterDeviceId} = ?" => $masterDeviceId]);
    }
}