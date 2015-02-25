<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMasterDeviceModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class DeviceInstanceMasterDeviceMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class DeviceInstanceMasterDeviceMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_deviceInstanceId = 'deviceInstanceId';
    public $col_masterDeviceId   = 'masterDeviceId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\DeviceInstanceMasterDeviceDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return DeviceInstanceMasterDeviceMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMasterDeviceModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object DeviceInstanceMasterDeviceModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMasterDeviceModel to the database.
     *
     * @param $object     DeviceInstanceMasterDeviceModel
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
            $primaryKey = $data [$this->col_deviceInstanceId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_deviceInstanceId} = ?" => $primaryKey,
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\DeviceInstanceMasterDeviceModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof DeviceInstanceMasterDeviceModel)
        {
            $whereClause = [
                "{$this->col_deviceInstanceId} = ?" => $object->id,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_deviceInstanceId} = ?" => $object,
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Deletes many ids in a single query
     *
     * @param DeviceInstanceMasterDeviceModel[]|int[] $objects
     *
     * @return int
     */
    public function deleteMany ($objects)
    {
        $ids = [];
        foreach ($objects as $object)
        {
            if ($object instanceof DeviceInstanceMasterDeviceModel)
            {
                $ids[] = $object->deviceInstanceId;
            }
            else
            {
                $ids[] = $object;
            }
        }

        return $this->getDbTable()->delete(["{$this->col_deviceInstanceId} IN(?)" => $ids]);
    }

    /**
     * Finds a Device_Instance_Master_Device based on it's primaryKey
     *
     * @param $id int
     *            The id of the Device_Instance_Master_Device to find
     *
     * @return DeviceInstanceMasterDeviceModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof DeviceInstanceMasterDeviceModel)
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
        $object = new DeviceInstanceMasterDeviceModel($row->toArray());

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
     * @return DeviceInstanceMasterDeviceModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new DeviceInstanceMasterDeviceModel($row->toArray());

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
     * @return DeviceInstanceMasterDeviceModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new DeviceInstanceMasterDeviceModel($row->toArray());

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
            "{$this->col_deviceInstanceId} = ?" => $id,
        ];
    }

    /**
     * @param DeviceInstanceMasterDeviceModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->deviceInstanceId;
    }
}