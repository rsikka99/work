<?php
namespace MPSToolbox\Legacy\Modules\Admin\Mappers;

use MPSToolbox\Legacy\Modules\Admin\Models\DealerRmsProviderModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class MPSToolbox\Legacy\Modules\Admin\Mappers\DealerRmsProviderMapper
 */
class DealerRmsProviderMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_dealerId      = 'dealerId';
    public $col_rmsProviderId = 'rmsProviderId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\Admin\DbTables\DealerRmsProviderDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return DealerRmsProviderMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\Admin\Models\DealerRmsProviderModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object DealerRmsProviderModel
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\Admin\Models\DealerRmsProviderModel to the database.
     *
     * @param $object     DealerRmsProviderModel
     *                    The DealerRmsProvider model to save to the database
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
            $whereClause = $this->getWhereId($this->getPrimaryKeyValueForObject($object));
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
     *                This can either be an instance of MPSToolbox\Legacy\Modules\Admin\Models\DealerRmsProviderModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof DealerRmsProviderModel)
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
     * Finds a DealerRmsProvider based on it's primaryKey
     *
     * @param array $id The id of the DealerRmsProvider to find
     *
     * @return DealerRmsProviderModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof DealerRmsProviderModel)
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
        $object = new DealerRmsProviderModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a DealerRmsProvider
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL An SQL OFFSET value.
     *
     * @return DealerRmsProviderModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new DealerRmsProviderModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all DealerRmsProviders
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
     * @return DealerRmsProviderModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new DealerRmsProviderModel($row->toArray());

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
            "{$this->col_dealerId} = ?"      => $id[0],
            "{$this->col_rmsProviderId} = ?" => $id[1],
        ];
    }

    /**
     * @param DealerRmsProviderModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [
            $object->dealerId,
            $object->rmsProviderId,
        ];
    }

    /**
     * Fetches all DealerRmsProviders for a given dealer
     *
     * @param $dealerId int
     *
     * @return DealerRmsProviderModel[]
     */
    public function fetchAllForDealer ($dealerId)
    {
        return $this->fetchAll(["{$this->col_dealerId} = ?" => $dealerId]);
    }
}