<?php
class Admin_Model_Mapper_UserRole extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Admin_Model_DbTable_UserRole';

    /**
     * Gets an instance of the mapper
     *
     * @return Admin_Model_Mapper_UserRole
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Admin_Model_UserRole to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Admin_Model_UserRole
     *                The object to insert
     *
     * @return mixed The primary key of the new row
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
     * Saves (updates) an instance of Admin_Model_UserRole to the database.
     *
     * @param Admin_Model_UserRole|array $object     The userRole model to save to the database
     * @param mixed                      $primaryKey Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

        if ($primaryKey === null)
        {
            if ($object instanceof Admin_Model_UserRole)
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
     *                This can either be an instance of Admin_Model_UserRole or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Admin_Model_UserRole)
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
     * @return Admin_Model_UserRole
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Admin_Model_UserRole)
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
        $object = new Admin_Model_UserRole($row->toArray());

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
     * @return Admin_Model_UserRole
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Admin_Model_UserRole($row->toArray());

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
     * @return Admin_Model_UserRole[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Admin_Model_UserRole($row->toArray());

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
        return array(
            'userId = ?' => $id [0],
            'roleId = ?' => $id [1]
        );
    }

    /**
     * @param Admin_Model_UserRole $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->userId,
            $object->roleId,
        );
    }

    /**
     * Fetches all the roles for a given user.
     *
     * @param number $userId
     *
     * @return number
     */
    public function fetchAllRolesForUser ($userId)
    {
        return $this->fetchAll(array(
                                    'userId = ?' => $userId
                               ));
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
        return $this->getDbTable()->delete(array(
                                                'userId = ?' => $userId
                                           ));
    }
}