<?php
class Preferences_Model_Mapper_Dealer_Setting extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_dealerId = 'dealerId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Preferences_Model_DbTable_Dealer_Setting';

    /**
     * Gets an instance of the mapper
     *
     * @return Preferences_Model_Mapper_Dealer_Setting
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Preferences_Model_Dealer_Setting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Preferences_Model_Dealer_Setting
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

        $object->dealerId = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Preferences_Model_Dealer_Setting to the database.
     *
     * @param $object     Preferences_Model_Dealer_Setting
     *                    The Dealer Setting model to save to the database
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
            $primaryKey = $data [$this->col_dealerId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_dealerId} = ?" => $primaryKey
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Preferences_Model_Dealer_Setting or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Preferences_Model_Dealer_Setting)
        {
            $whereClause = array(
                "{$this->col_dealerId} = ?" => $object->dealerId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_dealerId} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Dealer Setting based on it's primaryKey
     *
     * @param $id int
     *            The id of the Dealer Setting to find
     *
     * @return Preferences_Model_Dealer_Setting
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Preferences_Model_Dealer_Setting)
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
        $object = new Preferences_Model_Dealer_Setting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Dealer Setting
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return Preferences_Model_Dealer_Setting
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Preferences_Model_Dealer_Setting($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Dealer Settings
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
     * @return Preferences_Model_Dealer_Setting[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Preferences_Model_Dealer_Setting($row->toArray());

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
            "{$this->col_dealerId} = ?" => $id
        );
    }

    /**
     * @param Preferences_Model_Dealer_Setting $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->dealerId;
    }
}