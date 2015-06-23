<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers;

use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationModel;
use MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationQuoteModel;
use My_Model_Mapper_Abstract;
use Zend_Db_Table_Select;

/**
 * Class HardwareOptimizationQuoteMapper
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Mappers
 */
class HardwareOptimizationQuoteMapper extends My_Model_Mapper_Abstract
{
    /*
     * Column Definitions
     */
    public $col_quoteId = 'quoteId';

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables\HardwareOptimizationQuoteDbTable';

    /**
     * Gets an instance of the mapper
     *
     * @return HardwareOptimizationQuoteMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationQuoteModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object HardwareOptimizationQuoteModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());


        // Insert the data
        $id = $this->getDbTable()->insert($data);

        $object->quoteId = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationQuoteModel to the database.
     *
     * @param $object     HardwareOptimizationQuoteModel
     *                    Hardware optimization quotemodel to save to the database
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
            $primaryKey = $data [$this->col_quoteId];
        }

        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, [
            "{$this->col_quoteId} = ?" => $primaryKey,
        ]);

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\HardwareOptimization\Models\HardwareOptimizationQuoteModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof HardwareOptimizationQuoteModel)
        {
            $whereClause = [
                "{$this->col_quoteId} = ?" => $object->quoteId,
            ];
        }
        else
        {
            $whereClause = [
                "{$this->col_quoteId} = ?" => $object,
            ];
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Template based on it's primaryKey
     *
     * @param $id int
     *            The id of Hardware optimization quote to find
     *
     * @return HardwareOptimizationQuoteModel
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof HardwareOptimizationQuoteModel)
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
        $object = new HardwareOptimizationQuoteModel($row->toArray());

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
     * @return HardwareOptimizationQuoteModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new HardwareOptimizationQuoteModel($row->toArray());

        // Save the object into the cache
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
     * @return HardwareOptimizationQuoteModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new HardwareOptimizationQuoteModel($row->toArray());

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
            "{$this->col_quoteId} = ?" => $id,
        ];
    }

    /**
     * @param HardwareOptimizationQuoteModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->quoteId;
    }

    /**
     * @param $quoteId
     *
     * @return HardwareOptimizationQuoteModel[]
     */
    public function fetchByQuoteId ($quoteId)
    {
        return $this->fetchAll(["{$this->col_quoteId} = ?" => $quoteId]);
    }

    /**
     * @param $quoteId
     *
     * @return bool|HardwareOptimizationModel
     */
    public function fetchHardwareOptimizationByQuoteId ($quoteId)
    {
        $hardwareOptimizationQuote = $this->fetch(["{$this->col_quoteId} = ?" => $quoteId]);

        if ($hardwareOptimizationQuote instanceof HardwareOptimizationQuoteModel)
        {
            return HardwareOptimizationMapper::getInstance()->find($hardwareOptimizationQuote->hardwareOptimizationId);
        }
        else
        {
            return false;
        }
    }
}