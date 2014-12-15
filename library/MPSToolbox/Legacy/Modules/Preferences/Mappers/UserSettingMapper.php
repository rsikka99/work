<?php

namespace MPSToolbox\Legacy\Modules\Preferences\Mappers;

use MPSToolbox\Legacy\Modules\Preferences\Models\UserSettingModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class UserSettingMapper
 *
 * @package MPSToolbox\Legacy\Modules\Preferences\Mappers
 */
class UserSettingMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_userId = 'userId';

    /**
     * The default db table class to use
     *
     * @var String
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\Preferences\DbTables\UserSettingDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return UserSettingMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\Preferences\Models\UserSettingModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object UserSettingModel
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

        $object->userId = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\Preferences\Models\UserSettingModel to the database.
     *
     * @param $object     UserSettingModel
     *                    The User Setting model to save to the database
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
            $primaryKey = $data [$this->col_userId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
            "{$this->col_userId} = ?" => $primaryKey
        ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\Preferences\Models\UserSettingModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof UserSettingModel)
        {
            $whereClause = array(
                "{$this->col_userId} = ?" => $object->userId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_userId} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a User Setting based on it's primaryKey
     *
     * @param $id int
     *            The id of the User Setting to find
     *
     * @return UserSettingModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof UserSettingModel)
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
        $object = new UserSettingModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a User Setting
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return UserSettingModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new UserSettingModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all User Settings
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
     * @return UserSettingModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new UserSettingModel($row->toArray());

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
            "{$this->col_userId} = ?" => $id
        );
    }

    /**
     * @param UserSettingModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->userId;
    }
}