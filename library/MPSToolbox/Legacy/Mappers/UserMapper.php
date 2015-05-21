<?php

namespace MPSToolbox\Legacy\Mappers;

use MPSToolbox\Legacy\Models\UserModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class UserMapper
 *
 * @package MPSToolbox\Legacy\Mappers
 */
class UserMapper extends My_Model_Mapper_Abstract
{
    // Column Names
    public $col_id       = "id";
    public $col_dealerId = "dealerId";
    public $col_email    = "email";
    public $col_lastSeen = "lastSeen";
    
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\DbTables\UserDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return UserMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Models\UserModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object UserModel
     *                The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert ($object)
    {
        // Get an array of data to save
        $data = $object->toArray();

        // Remove the id
        unset($data [$this->col_id]);

        // Insert the data
        $id = $this->getDbTable()->insert($data);

        $object->id = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Models\UserModel to the database.
     *
     * @param $object     UserModel
     *                    The user model to save to the database
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
            $primaryKey = $data [$this->col_id];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_id} = ?" => $primaryKey
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Models\UserModel or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof UserModel)
        {
            $whereClause = [
                "{$this->col_id} = ?" => $object->id
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_id} = ?" => $object
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a user based on it's primaryKey
     *
     * @param $id int
     *            The id of the user to find
     *
     * @return UserModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof UserModel)
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
        $object = new UserModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Checks to see if a user id exists
     *
     * @param int $id The id of the user
     *
     * @return bool
     */
    public function exists ($id)
    {
        return ($this->find($id) instanceof UserModel);
    }

    /**
     * Fetches a user
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return  UserModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new UserModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all users
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL An SQL LIMIT count. (Defaults to 150)
     * @param $offset int
     *                OPTIONAL An SQL LIMIT offset.
     *
     * @return UserModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 150, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new UserModel($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * @return UserModel[]
     */
    public function fetchAllExceptRoot ()
    {
        return $this->fetchAll(["{$this->col_id} != 1"]);
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
            "{$this->col_id} = ?" => $id
        ];
    }

    /**
     * Gets a where clause for filtering by email
     *
     * @param string $email
     *
     * @return array
     */
    public function getWhereUsername ($email)
    {
        return [
            "{$this->col_email} = ?" => $email
        ];
    }

    /**
     * Gets a where clause for filtering by dealerId
     *
     * @param string $dealerId
     *
     * @return array
     */
    public function getWhereDealerId ($dealerId)
    {
        return [
            "dealerId = ?" => $dealerId
        ];
    }

    /**
     * @param UserModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Fetches a list of users in the system
     *
     * @param bool $includeRootUser
     *
     * @return UserModel[]
     */
    public function fetchUserList ($includeRootUser = false)
    {
        if ($includeRootUser)
        {
            $users = $this->fetchAll();
        }
        else
        {
            $users = $this->fetchAll(["{$this->col_id} != ?" => 1]);
        }

        return $users;
    }

    /**
     * Fetches a list of users for the dealer
     *
     * @param $dealerId
     *
     * @return UserModel[]
     */
    public function fetchUserListForDealer ($dealerId)
    {
        return $this->fetchAll(["{$this->col_dealerId} = ?" => $dealerId], "{$this->col_lastSeen} DESC");

    }

    /**
     * * Fetches users by email
     *
     * @param $email
     *
     * @return UserModel
     */
    public function fetchUserByEmail ($email)
    {
        return $this->fetch(["{$this->col_email} = ?" => $email]);
    }

    /**
     * Searches the user by email (uses LIKE %EMAIL% in where clause)
     *
     * @param string $emailName
     *
     * @return UserModel []
     */
    public function searchByEmail ($emailName)
    {
        $results = $this->fetchAll(["{$this->col_email} LIKE ?" => "%{$emailName}%"]);

        return $results;
    }
}

