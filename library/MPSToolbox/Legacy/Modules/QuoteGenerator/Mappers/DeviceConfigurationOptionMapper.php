<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationOptionModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class DeviceConfigurationOptionMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class DeviceConfigurationOptionMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\DeviceConfigurationOptionDbTable';

    /*
     * Define the primary key of the model association
     */
    public $col_deviceConfigurationId = 'deviceConfigurationId';
    public $col_optionId              = 'optionId';

    /**
     * Gets an instance of the mapper
     *
     * @return DeviceConfigurationOptionMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Counts and returns the amount of rows by deviceConfigurationId
     *
     * @param int $deviceConfigurationId
     *
     * @return int The amount of rows in the database.
     */
    public function countByDeviceId ($deviceConfigurationId)
    {
        return $this->count([
            "{$this->col_deviceConfigurationId} = ?" => $deviceConfigurationId,
        ]);
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationOptionModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object DeviceConfigurationOptionModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
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
     * @param DeviceConfigurationModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [
            $object->deviceConfigurationId,
            $object->optionId,
        ];
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationOptionModel to the database.
     *
     * @param $object     DeviceConfigurationOptionModel
     *                    The deviceConfigurationOption model to save to the database
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
            $primaryKey [] = $data [$this->col_deviceConfigurationId];
            $primaryKey [] = $data [$this->col_optionId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_deviceConfigurationId} = ?" => $primaryKey [0],
            "{$this->col_optionId} = ?"              => $primaryKey [1],
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\DeviceConfigurationOptionModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof DeviceConfigurationOptionModel)
        {
            $whereClause = [
                "{$this->col_deviceConfigurationId} = ?" => $object->deviceConfigurationId,
                "{$this->col_optionId} = ?"              => $object->optionId,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_deviceConfigurationId} = ?" => $object [0],
                "{$this->col_optionId} = ?"              => $object [1],
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Delete a deviceConfigurationOption by deviceConfigurationId
     *
     * @param int $deviceConfigurationId
     *
     * @return int The number of rows affected.
     */
    public function deleteDeviceConfigurationOptionById ($deviceConfigurationId)
    {
        return $this->getDbTable()->delete([
            "{$this->col_deviceConfigurationId} = ?" => $deviceConfigurationId,
        ]);
    }

    /**
     * Finds a deviceConfigurationOption based on it's primaryKey
     *
     * @param $id int []
     *            The id of the deviceConfigurationOption to find
     *
     * @return DeviceConfigurationOptionModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof DeviceConfigurationOptionModel)
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
        $object = new DeviceConfigurationOptionModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a deviceConfigurationOption
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return DeviceConfigurationOptionModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new DeviceConfigurationOptionModel($row->toArray());

        // Save the object into the cache
        $primaryKey [0] = $object->deviceConfigurationId;
        $primaryKey [1] = $object->optionId;
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all deviceConfigurationOptions
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL: An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL: An SQL LIMIT offset.
     *
     * @return DeviceConfigurationOptionModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new DeviceConfigurationOptionModel($row->toArray());

            // Save the object into the cache
            $primaryKey [0] = $object->deviceConfigurationId;
            $primaryKey [1] = $object->optionId;
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param int[] $id
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return [
            "{$this->col_deviceConfigurationId} = ?" => $id [0],
            "{$this->col_optionId} = ?"              => $id [1],
        ];
    }
}


