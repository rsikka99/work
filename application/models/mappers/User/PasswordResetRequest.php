<?php

/**
 * Class Application_Model_Mapper_User_PasswordResetRequest
 */
class Application_Model_Mapper_User_PasswordResetRequest extends My_Model_Mapper_Abstract
{
    // Column Names
    public $col_id = "id";
    public $userId = "userId";

    /**
     * The default db table class to use
     *
     * @var String
     */
    protected $_defaultDbTable = 'Application_Model_DbTable_User_PasswordResetRequests';

    /**
     * Gets an instance of the mapper
     *
     * @return Application_Model_Mapper_User_PasswordResetRequest
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Application_Model_User_PasswordResetRequest to the database.
     * If the id is null then it will insert a new row
     *
     * @param Application_Model_User_PasswordResetRequest $object The object to insert
     *
     * @return int The primary key of the new row
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
        //$this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Application_Model_User_PasswordResetRequest to the database.
     *
     * @param $object     Application_Model_User_PasswordResetRequest
     *                    The password reset request model to save to the database
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
        //$this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Application_Model_User_PasswordResetRequest or the
     *                primary key to delete
     *
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Application_Model_User_PasswordResetRequest)
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
     * Finds a Application_Model_User_PasswordResetRequest based on it's primaryKey
     *
     * @param $id int The id of the Application_Model_User_PasswordResetRequest to find
     *
     * @return Application_Model_User_PasswordResetRequest
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Application_Model_User_PasswordResetRequest)
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
        $object = new Application_Model_User_PasswordResetRequest($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Application_Model_User_PasswordResetRequest
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return  Application_Model_User_PasswordResetRequest
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Application_Model_User_PasswordResetRequest($row->toArray());

        // Save the object into the cache
//        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Takes an object and returns a proper value for the primary key
     *
     * @param My_Model_Abstract $object
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * @param null $where
     * @param null $order
     * @param int  $count
     * @param null $offset
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        // TODO: Implement fetchAll() method.
    }

    /**
     * Deletes all objects with the userId given
     *
     * @param $userId
     *
     * @return int
     */
    public function deleteByUserId ($userId)
    {
        $whereClause  = array(
            "{$this->userId} = ?" => $userId
        );
        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Deletes all password reset requests for a user
     *
     * @param $username
     *
     * @return bool|int
     */
    public function clearAllTokensForUser ($username)
    {
        $user = Application_Model_Mapper_User::getInstance()->fetch(Application_Model_Mapper_User::getInstance()->getWhereUsername($username));
        if ($user)
        {
            return $this->deleteByUserId($user->id);

        }

        return false;
    }
}