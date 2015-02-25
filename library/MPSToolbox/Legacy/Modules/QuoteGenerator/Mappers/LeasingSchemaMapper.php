<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class LeasingSchemaMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class LeasingSchemaMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\LeasingSchemaDbTable';

    /*
     * Define the primary key of the model association
    */
    public $col_id       = 'id';
    public $col_dealerId = 'dealerId';

    /**
     * Gets an instance of the mapper
     *
     * @return LeasingSchemaMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param LeasingSchemaModel $object The object to insert
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaModel to the database.
     *
     * @param LeasingSchemaModel $object     The client model to save to the database
     * @param mixed              $primaryKey Optional: The original primary key, in case we're changing it
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
            "{$this->col_id} = ?" => $primaryKey,
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param  mixed $leasingSchema This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaModel or the primary key to delete
     *
     * @return int The primary key of the new row
     */
    public function delete ($leasingSchema)
    {
        if ($leasingSchema instanceof LeasingSchemaModel)
        {
            $whereClause = [
                "{$this->col_id} = ?" => $leasingSchema->id,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_id} = ?" => $leasingSchema,
            ];
        }

        $result = $this->getDbTable()->delete($whereClause);

        return $result;
    }

    /**
     * Finds a client based on it's primaryKey
     *
     * @param $id int
     *            The id of the client to find
     *
     * @return LeasingSchemaModel
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new LeasingSchemaModel($row->toArray());

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
     * @return LeasingSchemaModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new LeasingSchemaModel($row->toArray());

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
     * @return LeasingSchemaModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new LeasingSchemaModel($row->toArray());

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
        return [
            "{$this->col_id} = ?" => $id,
        ];
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param $dealerId
     *
     * @return array
     */
    public function getWhereDealerId ($dealerId)
    {
        return [
            "{$this->col_dealerId} = ?" => $dealerId,
        ];
    }

    /**
     * @param LeasingSchemaModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * @param $dealerId
     *
     * @return LeasingSchemaModel[]
     */
    public function getSchemasForDealer ($dealerId)
    {
        return $this->fetchAll($this->getWhereDealerId($dealerId));
    }
}