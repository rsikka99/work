<?php
class Application_Model_Mapper_User extends My_Model_Mapper_Abstract
{
    // Column Names
    public $col_id = "id";
    public $col_dealerId = "dealerId";
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Application_Model_DbTable_User';

    /**
     * Gets an instance of the mapper
     *
     * @return Application_Model_Mapper_User
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Application_Model_User to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Application_Model_User
     *                The object to insert
     *
     * @return mixed The primary key of the new row
     */
    public function insert (&$object)
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
     * Saves (updates) an instance of Application_Model_User to the database.
     *
     * @param $object     Application_Model_User
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
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_id} = ?" => $primaryKey
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Application_Model_User or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Application_Model_User)
        {
            $whereClause = array(
                "{$this->col_id} = ?" => $object->id
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_id} = ?" => $object
            );
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
     * @return Application_Model_User
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Application_Model_User)
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
        $object = new Application_Model_User($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
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
     * @return  Application_Model_User
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Application_Model_User($row->toArray());

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
     * @return Application_Model_User[]
     */
    public function fetchAll ($where = null, $order = null, $count = 150, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Application_Model_User($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    public function fetchAllExceptRoot ()
    {
        return $this->fetchAll(array("{$this->col_id} != 1"));
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
            "{$this->col_id} = ?" => $id
        );
    }

    /**
     * Gets a where clause for filtering by username
     *
     * @param string $username
     *
     * @return array
     */
    public function getWhereUsername ($username)
    {
        return array(
            "username = ?" => $username
        );
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
        return array(
            "dealerId = ?" => $dealerId
        );
    }

    /**
     * @param Application_Model_User $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Fetches a list of users in the system
     * @param bool $includeRootUser
     *
     * @return Application_Model_User[]
     */
    public function fetchUserList ($includeRootUser = false)
    {
        $users = array();
        if ($includeRootUser)
        {
            $users = $this->fetchAll();
        }
        else
        {
            $users = $this->fetchAll(array("{$this->col_id} != ?" => 1));
        }

        return $users;
    }

    /**
     * Fetches a list of users for the dealer
     * @param bool $includeRootUser
     *
     * @return Application_Model_User[]
     */
    public function fetchUserListForDealer ($dealerId)
    {
        $users = $this->fetchAll(array("{$this->col_dealerId} = ?" => $dealerId));
        return $users;
    }
}

