<?php
namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRateModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class LeasingSchemaRateMapper
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers
 */
class LeasingSchemaRateMapper extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables\LeasingSchemaRateDbTable';

    /*
     * Define the primary key of the model association
    */
    public $col_leasingSchemaTermId  = 'leasingSchemaTermId';
    public $col_leasingSchemaRangeId = 'leasingSchemaRangeId';

    /**
     * Gets an instance of the mapper
     *
     * @return LeasingSchemaRateMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRateModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param LeasingSchemaRateModel $object The object to insert
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
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRateModel to the database.
     *
     * @param  LeasingSchemaRateModel $object     The leasingSchemaRate model to save to the database
     * @param mixed                   $primaryKey Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());

        if ($primaryKey === null)
        {
            $primaryKey [] = $data [$this->col_leasingSchemaTermId];
            $primaryKey [] = $data [$this->col_leasingSchemaRangeId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_leasingSchemaTermId} = ?"  => $primaryKey [0],
            "{$this->col_leasingSchemaRangeId} = ?" => $primaryKey [1],
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRateModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\QuoteGenerator\Models\LeasingSchemaRateModel or the
     *                primary key to delete
     *
     * @return int The primary key of the new row
     */
    public function delete ($object)
    {
        if ($object instanceof LeasingSchemaRateModel)
        {
            $whereClause = [
                "{$this->col_leasingSchemaTermId} = ?"  => $object->leasingSchemaTermId,
                "{$this->col_leasingSchemaRangeId} = ?" => $object->leasingSchemaRangeId,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_leasingSchemaTermId} = ?"  => $object [0],
                "{$this->col_leasingSchemaRangeId} = ?" => $object [1],
            ];
        }

        $result = $this->getDbTable()->delete($whereClause);

        return $result;
    }

    /**
     * Finds a leasingSchemaRate based on it's primaryKey
     *
     * @param array $id The id of the leasingSchemaRate to find
     *
     * @return LeasingSchemaRateModel
     */
    public function find ($id)
    {

        $result = $this->getDbTable()->find($id[0], $id[1]);

        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new LeasingSchemaRateModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a leasingSchemaRate
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: An SQL OFFSET value.
     *
     * @return LeasingSchemaRateModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new LeasingSchemaRateModel($row->toArray());

        $primaryKey [] = $object->leasingSchemaTermId;
        $primaryKey [] = $object->leasingSchemaRangeId;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all leasingSchemaRates
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
     * @return LeasingSchemaRateModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new LeasingSchemaRateModel($row->toArray());

            $primaryKey [] = $object->leasingSchemaTermId;
            $primaryKey [] = $object->leasingSchemaRangeId;

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
            "{$this->col_leasingSchemaTermId} = ?"  => $id [0],
            "{$this->col_leasingSchemaRangeId} = ?" => $id [1],
        ];
    }

    /**
     * Fetches all the rates for a leasing schema
     *
     * @param int $leasingSchemaId
     *            id of the leasing schema
     *
     * @return LeasingSchemaRateModel[]
     */
    public function fetchAllForLeasingSchema ($leasingSchemaId)
    {
        $rates = [];

        // Get the table names for convieniece
        $termTableName   = LeasingSchemaTermMapper::getInstance()->getTableName();
        $rangeTableName  = LeasingSchemaRangeMapper::getInstance()->getTableName();
        $schemaTableName = LeasingSchemaMapper::getInstance()->getTableName();
        $rateTableName   = $this->getTableName();

        // Create a select statement
        $select = LeasingSchemaMapper::getInstance()->getDbTable()->select(true);

        $select->joinRight(['terms' => $termTableName], "terms.leasingSchemaId = {$schemaTableName}.id");

        $select->join(['rates' => $rateTableName], "terms.id = rates.leasingSchemaTermId");

        $select->joinRight(['ranges' => $rangeTableName], "ranges.id = rates.leasingSchemaRangeId");

        $select->where("{$schemaTableName}.id = ?", $leasingSchemaId);
        $select->setIntegrityCheck(false);

        $result = $select->query()->fetchAll();

        if ($result && count($result) > 0)
        {
            foreach ($result as $row)
            {
                $rate = new LeasingSchemaRateModel($row);

                $primaryKey [] = $rate->leasingSchemaTermId;
                $primaryKey [] = $rate->leasingSchemaRangeId;

                // Save the object into the cache
                $this->saveItemToCache($rate);

                $rates [$rate->leasingSchemaTermId] [$rate->leasingSchemaRangeId] = $rate;
            }
        }

        return $rates;
    }

    /**
     * @param LeasingSchemaRateModel $object
     *
     * @return array
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return [
            $object->leasingSchemaTermId,
            $object->leasingSchemaRangeId,
        ];
    }
}

