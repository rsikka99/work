<?php

/**
 * Class Quotegen_Model_Mapper_Client
 */
class Quotegen_Model_Mapper_Client extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_Client';

    /*
     * Define the primary key of the model association
     */
    public $col_id = 'id';
    public $col_dealerId = 'dealerId';
    public $col_companyName = 'companyName';
    public $col_legalName = 'legalName';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_Client
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_Client to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_Client
     *                The object to insert
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
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_Client to the database.
     *
     * @param $object     Quotegen_Model_Client
     *                    The client model to save to the database
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
                                                                "{$this->col_id}  = ?" => $primaryKey
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Quotegen_Model_Client or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_Client)
        {
            $whereClause = array(
                "{$this->col_id}  = ?" => $object->id
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_id}  = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a client based on it's primaryKey
     *
     * @param $id int
     *            The id of the client to find
     *
     * @return Quotegen_Model_Client
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_Client)
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
        $object = new Quotegen_Model_Client($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a client
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return Quotegen_Model_Client
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Quotegen_Model_Client($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all clients
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
     * @return Quotegen_Model_Client[]
     */
    public function fetchAll ($where = null, $order = null, $count = 150, $offset = null)
    {
        if ($order === null)
        {
            $order = "{$this->col_companyName} ASC";
        }

        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Quotegen_Model_Client($row->toArray());

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
            "{$this->col_id}  = ?" => $id
        );
    }

    /**
     * @param Quotegen_Model_Client $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Searches for a client by the company name field.
     *
     * @param string $searchTerm
     *
     * @return Quotegen_Model_Client[]
     */
    public function searchForClientByCompanyName ($searchTerm)
    {
        $searchTerm = trim($searchTerm, "%");

        return $this->fetchAll(array(
                                    "{$this->col_companyName} LIKE ?" => "%{$searchTerm}%"
                               ));
    }

    /**
     * Searches for a client by the company name field.
     *
     * @param string $searchTerm
     *
     * @param        $dealerId
     *
     * @return Quotegen_Model_Client[]
     */
    public function searchForClientByCompanyNameAndDealer ($searchTerm, $dealerId)
    {
        $searchTerm = trim($searchTerm, "%");

        return $this->fetchAll(array(
                                    "{$this->col_companyName} LIKE ?" => "%{$searchTerm}%",
                                    "{$this->col_dealerId} = ?"       => $dealerId
                               ));
    }

    /**
     * Fetches a list of clients for the dealer
     *
     * @param int $dealerId
     *
     * @return Quotegen_Model_Client[]
     */
    public function fetchClientListForDealer ($dealerId)
    {
        $users = $this->fetchAll(array("{$this->col_dealerId} = ?" => $dealerId));

        return $users;
    }

    /**
     * Fetches the most recently viewed clients for a user.
     *
     * @param     $userId
     *
     * @param int $limit
     *
     * @return Quotegen_Model_Client[]
     */
    public function fetchRecentlyViewed ($userId, $limit = 5)
    {
        $userViewedClientMapper  = Quotegen_Model_Mapper_UserViewedClient::getInstance();
        $recentlyViewedTableName = $userViewedClientMapper->getTableName();
        $db                      = $this->getDbTable()->getAdapter();
        $select                  = $db->select();
        $select->from($this->getTableName())
               ->joinLeft($recentlyViewedTableName, "{$recentlyViewedTableName}.{$userViewedClientMapper->col_clientId} = {$this->getTableName()}.{$this->col_id}")
               ->where("{$recentlyViewedTableName}.{$userViewedClientMapper->col_userId} = ?", $userId)
               ->order("{$userViewedClientMapper->col_dateViewed} DESC")
               ->limit($limit);

        $resultSet = $db->fetchAll($select);

        $entries = array();
        foreach ($resultSet as $row)
        {
            $object = new Quotegen_Model_Client($row);

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;

    }
}