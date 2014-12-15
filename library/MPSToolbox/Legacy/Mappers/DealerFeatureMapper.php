<?php
namespace MPSToolbox\Legacy\Mappers;

use MPSToolbox\Legacy\Models\DealerFeatureModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class DealerFeatureMapper
 *
 * @package MPSToolbox\Legacy\Mappers
 */
class DealerFeatureMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_featureId = 'featureId';
    public $col_dealerId  = 'dealerId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\DbTables\DealerFeatureDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return DealerFeatureMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Models\DealerFeatureModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object DealerFeatureModel
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

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Models\DealerFeatureModel to the database.
     *
     * @param $object     DealerFeatureModel
     *                    The Dealer Feature model to save to the database
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
            $primaryKey [] = $data [$this->col_featureId];
            $primaryKey [] = $data [$this->col_dealerId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array(
            "{$this->col_featureId} = ?" => $primaryKey [0],
            "{$this->col_dealerId} = ?"  => $primaryKey [1],
        ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Models\DealerFeatureModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof DealerFeatureModel)
        {
            $whereClause = array(
                "{$this->col_featureId} = ?" => $object->featureId,
                "{$this->col_dealerId} = ?"  => $object->dealerId,

            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_featureId} = ?" => $object[0],
                "{$this->col_dealerId} = ?"  => $object[1],
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Dealer Feature based on it's primaryKey
     *
     * @param $id array
     *            The id of the Dealer Feature to find
     *
     * @return DealerFeatureModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof DealerFeatureModel)
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
        $object = new DealerFeatureModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a Template
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return DealerFeatureModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new DealerFeatureModel($row->toArray());

        // Save the object into the cache
        $primaryKey [0] = $object->featureId;
        $primaryKey [1] = $object->dealerId;
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all Templates
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
     * @return DealerFeatureModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new DealerFeatureModel($row->toArray());

            // Save the object into the cache
            $primaryKey [0] = $object->featureId;
            $primaryKey [1] = $object->dealerId;
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
            "{$this->col_featureId} = ?" => $id [0],
            "{$this->col_dealerId} = ?"  => $id [1],
        );
    }

    /**
     * @param DealerFeatureModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array(
            $object->featureId,
            $object->dealerId
        );
    }

    /**
     * Fetches a list of dealer features for the dealer
     *
     * @param $dealerId
     *
     * @return DealerFeatureModel[]
     */
    public function fetchFeatureListForDealer ($dealerId)
    {
        return $this->fetchAll(array("{$this->col_dealerId} = ?" => $dealerId));

    }
}