<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRangeModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class LeasingSchemaRangeMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class LeasingSchemaRangeMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\LeasingSchemaRangeDbTable';

    /*
     * Define the primary key of the model association
    */
    public $col_id = 'id';

    /**
     * Gets an instance of the mapper
     *
     * @return LeasingSchemaRangeMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRangeModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param LeasingSchemaRangeModel $object
     *                            The object to insert
     * @return int The primary key of the new row
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRangeModel to the database.
     *
     * @param LeasingSchemaRangeModel $object     The leasingSchemaRange model to save to the database
     * @param mixed                   $primaryKey Optional: The original primary key, in case we're changing it
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
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRangeModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $leasingSchemaRange mixed
     *                            This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRangeModel or the
     *                            primary key to delete
     *
     * @return int The primary key of the new row
     */
    public function delete ($leasingSchemaRange)
    {
        if ($leasingSchemaRange instanceof LeasingSchemaRangeModel)
        {
            $whereClause = [
                "{$this->col_id} = ?" => $leasingSchemaRange->id,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_id} = ?" => $leasingSchemaRange,
            ];
        }

        $result = $this->getDbTable()->delete($whereClause);

        return $result;
    }

    /**
     * Finds a leasingSchemaRange based on it's primaryKey
     *
     * @param $id int
     *            The id of the leasingSchemaRange to find
     *
     * @return LeasingSchemaRangeModel
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new LeasingSchemaRangeModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a leasingSchemaRange
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return LeasingSchemaRangeModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new LeasingSchemaRangeModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all leasingSchemaRanges
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
     * @return LeasingSchemaRangeModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new LeasingSchemaRangeModel($row->toArray());

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
     * Fetches all the ranges for a leasing schema
     *
     * @param int $leasingSchemaId
     *            id of the leasing schema
     *
     * @return LeasingSchemaRangeModel[]
     */
    public function fetchAllForLeasingSchema ($leasingSchemaId)
    {
        return $this->fetchAll([
            'leasingSchemaId = ?' => $leasingSchemaId,
        ], 'startRange ASC');
    }

    /**
     * @param LeasingSchemaRangeModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }
}

