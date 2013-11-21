<?php
/**
 * Class Memjetoptimization_Model_Mapper_Memjet_Optimization_Quote
 */
class Memjetoptimization_Model_Mapper_Memjet_Optimization_Quote extends My_Model_Mapper_Abstract
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
    protected $_defaultDbTable = 'Memjetoptimization_Model_DbTable_Memjet_Optimization_Quote';

    /**
     * Gets an instance of the mapper
     *
     * @return Memjetoptimization_Model_Mapper_Memjet_Optimization_Quote
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Memjetoptimization_Model_Memjet_Optimization_Quote to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Memjetoptimization_Model_Memjet_Optimization_Quote
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

        $object->quoteId = $id;

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $id;
    }

    /**
     * Saves (updates) an instance of Memjetoptimization_Model_Memjet_Optimization_Quote to the database.
     *
     * @param $object     Memjetoptimization_Model_Memjet_Optimization_Quote
     *                    Memjet optimization quotemodel to save to the database
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
        $rowsAffected = $this->getDbTable()->update($data, array(
                                                                "{$this->col_quoteId} = ?" => $primaryKey
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of Memjetoptimization_Model_Memjet_Optimization_Quote or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Memjetoptimization_Model_Memjet_Optimization_Quote)
        {
            $whereClause = array(
                "{$this->col_quoteId} = ?" => $object->quoteId
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_quoteId} = ?" => $object
            );
        }

        $rowsAffected = $this->getDbTable()->delete($whereClause);

        return $rowsAffected;
    }

    /**
     * Finds a Template based on it's primaryKey
     *
     * @param $id int
     *            The id of Memjet optimization quote to find
     *
     * @return Memjetoptimization_Model_Memjet_Optimization_Quote
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Memjetoptimization_Model_Memjet_Optimization_Quote)
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
        $object = new Memjetoptimization_Model_Memjet_Optimization_Quote($row->toArray());

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
     * @return Memjetoptimization_Model_Memjet_Optimization_Quote
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Memjetoptimization_Model_Memjet_Optimization_Quote($row->toArray());

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
     * @return Memjetoptimization_Model_Memjet_Optimization_Quote[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Memjetoptimization_Model_Memjet_Optimization_Quote($row->toArray());

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
            "{$this->col_quoteId} = ?" => $id
        );
    }

    /**
     * @param Memjetoptimization_Model_Memjet_Optimization_Quote $object
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
     * @return Memjetoptimization_Model_Memjet_Optimization_Quote[]
     */
    public function fetchByQuoteId ($quoteId)
    {
        return $this->fetchAll(array("{$this->col_quoteId} = ?" => $quoteId));
    }

    /**
     * @param $quoteId
     *
     * @return bool|Memjetoptimization_Model_Memjet_Optimization
     */
    public function fetchMemjetOptimizationByQuoteId ($quoteId)
    {
        $memjetOptimizationQuote = $this->fetch(array("{$this->col_quoteId} = ?" => $quoteId));

        if ($memjetOptimizationQuote  instanceof Memjetoptimization_Model_Memjet_Optimization_Quote)
        {
            return Memjetoptimization_Model_Mapper_Memjet_Optimization::getInstance()->find($memjetOptimizationQuote->hardwareOptimizationId);
        }
        else
        {
            return false;
        }
    }
}