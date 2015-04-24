<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\GlobalDeviceConfigurationModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class GlobalDeviceConfigurationMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class GlobalDeviceConfigurationMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\GlobalDeviceConfigurationDbTable';

    /*
     * Define the primary key of the model association
    */
    public $col_deviceConfigurationId = 'deviceConfigurationId';

    /**
     * Gets an instance of the mapper
     *
     * @return GlobalDeviceConfigurationMapper
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
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\GlobalDeviceConfigurationModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object GlobalDeviceConfigurationModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\GlobalDeviceConfigurationModel to the database.
     *
     * @param $object     GlobalDeviceConfigurationModel
     *                    The GlobalDeviceConfiguration model to save to the database
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
            $primaryKey = $data [$this->col_deviceConfigurationId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_deviceConfigurationId} = ?" => $primaryKey,
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\GlobalDeviceConfigurationModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof GlobalDeviceConfigurationModel)
        {
            $whereClause = [
                "{$this->col_deviceConfigurationId} = ?" => $object->deviceConfigurationId,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_deviceConfigurationId} = ?" => $object,
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a GlobalDeviceConfiguration based on it's primaryKey
     *
     * @param $id int
     *            The id of the GlobalDeviceConfiguration to find
     *
     * @return GlobalDeviceConfigurationModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof GlobalDeviceConfigurationModel)
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
        $object = new GlobalDeviceConfigurationModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a GlobalDeviceConfiguration
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return GlobalDeviceConfigurationModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new GlobalDeviceConfigurationModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all GlobalDeviceConfigurations
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
     * @return GlobalDeviceConfigurationModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new GlobalDeviceConfigurationModel($row->toArray());

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
            "{$this->col_deviceConfigurationId} = ?" => $id,
        ];
    }

    /**
     * @param GlobalDeviceConfigurationModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->deviceConfigurationId;
    }
}