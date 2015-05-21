<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserDeviceConfigurationModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class UserDeviceConfigurationMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class UserDeviceConfigurationMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\UserDeviceConfigurationDbTable';

    /*
     * Define the primary key of the model association
    */
    public $col_deviceConfigurationId = 'deviceConfigurationId';
    public $col_userId                = 'userId';

    /**
     * Gets an instance of the mapper
     *
     * @return UserDeviceConfigurationMapper
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
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserDeviceConfigurationModel to the database.
     *
     * @param $object UserDeviceConfigurationModel
     *                The object to insert
     * @return int id of the new row
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserDeviceConfigurationModel to the database.
     *
     * @param $object     UserDeviceConfigurationModel
     *                    The MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserDeviceConfigurationModel object to save to the database
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
            $primaryKey [] = $data [$this->col_userId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_deviceConfigurationId} = ?" => $primaryKey [0],
            "{$this->col_userId} = ?"                => $primaryKey [1],
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserDeviceConfigurationModel from the database.
     *
     * @param $userDeviceConfiguration mixed
     *                                 This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserDeviceConfigurationModel or the
     *                                 primary key to delete
     *
     * @return int number of rows that have been affected
     */
    public function delete ($userDeviceConfiguration)
    {
        if ($userDeviceConfiguration instanceof UserDeviceConfigurationModel)
        {
            $whereClause = [
                "{$this->col_deviceConfigurationId} = ?" => $userDeviceConfiguration->deviceConfigurationId,
                "{$this->col_userId} = ?"                => $userDeviceConfiguration->userId,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_deviceConfigurationId} = ?" => $userDeviceConfiguration [0],
                "{$this->col_userId} = ?"                => $userDeviceConfiguration [1],
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Deletes a deviceConfiguration by deviceConfigurationId
     *
     * @param int $deviceConfigurationId
     *
     * @return int The amount of rows affected
     */
    public function deleteUserDeviceConfigurationByDeviceId ($deviceConfigurationId)
    {
        return $rowsAffected = $this->getDbTable()->delete([
            "{$this->col_deviceConfigurationId} = ?" => $deviceConfigurationId,
        ]);
    }

    /**
     * Finds a userDeviceConfiguration based on it's primaryKey
     *
     * @param $id int
     *            The id of the template to find
     *
     * @return UserDeviceConfigurationModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof UserDeviceConfigurationModel)
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
        $object = new UserDeviceConfigurationModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a userDeviceConfiguration
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return object MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserDeviceConfigurationModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new UserDeviceConfigurationModel($row->toArray());

        // Save the object into the cache
        $primaryKey [0] = $object->deviceConfigurationId;
        $primaryKey [1] = $object->userId;
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all userDeviceConfiguration
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
     * @return UserDeviceConfigurationModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new UserDeviceConfigurationModel($row->toArray());

            // Save the object into the cache
            $primaryKey [0] = $object->deviceConfigurationId;
            $primaryKey [1] = $object->userId;
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
            "{$this->col_deviceConfigurationId} = ?" => $id [0],
            "{$this->col_userId} = ?"                => $id [1],
        ];
    }

    /**
     * @param UserDeviceConfigurationModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [
            $object->userId,
            $object->deviceConfigurationId,
        ];
    }
}

