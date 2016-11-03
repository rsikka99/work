<?php
namespace MPSToolbox\Legacy\Modules\Admin\Mappers;

use MPSToolbox\Legacy\Modules\Admin\Models\UserRoleModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class UserRoleMapper
 *
 * @package MPSToolbox\Legacy\Modules\Admin\Mappers
 */
class UserRoleMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\Admin\DbTables\UserRoleDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return UserRoleMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\Admin\Models\UserRoleModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object UserRoleModel
     *                The object to insert
     * @return mixed The primary key of the new row
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\Admin\Models\UserRoleModel to the database.
     *
     * @param UserRoleModel|array $object     The userRole model to save to the database
     * @param mixed               $primaryKey Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

        if ($primaryKey === null)
        {
            if ($object instanceof UserRoleModel)
            {
                $whereClause = $this->getWhereId($this->getPrimaryKeyValueForObject($object));
            }
            else
            {
                $whereClause = $this->getWhereId($object);
            }
        }
        else
        {
            $whereClause = $this->getWhereId($primaryKey);
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, $whereClause);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\Admin\Models\UserRoleModel or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof UserRoleModel)
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
     * Finds a userRole based on it's primaryKey
     *
     * @param $id int
     *            The id of the userRole to find
     *
     * @return UserRoleModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof UserRoleModel)
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
        $object = new UserRoleModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a userRole
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return UserRoleModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new UserRoleModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all userRoles
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
     * @return UserRoleModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new UserRoleModel($row->toArray());

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
            'userId = ?' => $id [0],
            'roleId = ?' => $id [1],
        ];
    }

    /**
     * @param UserRoleModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [
            $object->userId,
            $object->roleId,
        ];
    }

    /**
     * Fetches all the roles for a given user.
     *
     * @param number $userId
     *
     * @return UserRoleModel[]
     */
    public function fetchAllRolesForUser ($userId)
    {
        return $this->fetchAll([
            'userId = ?' => $userId,
        ]);
    }

    /**
     * Deletes all the roles for a given user.
     *
     * @param number $userId
     *
     * @return number
     */
    public function deleteAllRolesForUser ($userId)
    {
        return $this->getDbTable()->delete([
            'userId = ?' => $userId,
        ]);
    }
}