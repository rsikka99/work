<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserViewedClientModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class UserViewedClientMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class UserViewedClientMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\UserViewedClientDbTable';

    /*
     * Define the primary key of the model association
     */
    public $col_userId     = 'userId';
    public $col_clientId   = 'clientId';
    public $col_dateViewed = 'dateViewed';

    /**
     * Gets an instance of the mapper
     *
     * @return UserViewedClientMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserViewedClientModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object UserViewedClientModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserViewedClientModel to the database.
     *
     * @param $object     UserViewedClientModel
     *                    The userViewedClient model to save to the database
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
            $primaryKey [] = $data [$this->col_userId];
            $primaryKey [] = $data [$this->col_clientId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
            "{$this->col_userId} = ?"   => $primaryKey [0],
            "{$this->col_clientId} = ?" => $primaryKey [1]
        ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\UserViewedClientModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof UserViewedClientModel)
        {
            $whereClause = array(
                "{$this->col_userId} = ?"   => $object->userId,
                "{$this->col_clientId} = ?" => $object->clientId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_userId} = ?"   => $object [0],
                "{$this->col_clientId} = ?" => $object [1]
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a userViewedClient based on it's primaryKey
     *
     * @param $id array
     *            The id of the userViewedClient to find
     *
     * @return UserViewedClientModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof UserViewedClientModel)
        {
            return $result;
        }

        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id[0], $id[1]);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new UserViewedClientModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a userViewedClient
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return UserViewedClientModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new UserViewedClientModel($row->toArray());

        // Save the object into the cache
        $primaryKey [0] = $object->userId;
        $primaryKey [1] = $object->clientId;
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all userViewedClients
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
     * @return UserViewedClientModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new UserViewedClientModel($row->toArray());

            // Save the object into the cache
            $primaryKey [0] = $object->userId;
            $primaryKey [1] = $object->clientId;
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
            "{$this->col_userId} = ?"   => $id [0],
            "{$this->col_clientId} = ?" => $id [1]
        );
    }

    /**
     * @param UserViewedClientModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->userId,
            $object->clientId
        );
    }
}