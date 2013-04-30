<?php

/**
 * Class Quotegen_Model_Mapper_LeasingSchemaTerm
 */
class Quotegen_Model_Mapper_LeasingSchemaTerm extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_LeasingSchemaTerm';

    /*
     * Define the primary key of the model association
    */
    public $col_id = 'id';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_LeasingSchemaTerm
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_LeasingSchemaTerm to the database.
     * If the id is null then it will insert a new row
     *
     * @param Quotegen_Model_LeasingSchemaTerm $object The object to insert
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
     * Saves (updates) an instance of Quotegen_Model_LeasingSchemaTerm to the database.
     *
     * @param Quotegen_Model_LeasingSchemaTerm $object     The leasingSchemaTerm model to save to the database
     * @param mixed                            $primaryKey Optional: The original primary key, in case we're changing it
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
                                                                "{$this->col_id} = ?" => $primaryKey
                                                           ));

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $rowsAffected;
    }

    /**
     * Saves an instance of Quotegen_Model_LeasingSchemaTerm to the database.
     * If the id is null then it will insert a new row
     *
     * @param $leasingSchemaTerm mixed
     *                           This can either be an instance of Quotegen_Model_LeasingSchemaTerm or the
     *                           primary key to delete
     *
     * @return int The primary key of the new row
     */
    public function delete ($leasingSchemaTerm)
    {
        if ($leasingSchemaTerm instanceof Quotegen_Model_LeasingSchemaTerm)
        {
            $whereClause = array(
                "{$this->col_id} = ?" => $leasingSchemaTerm->id
            );
        }
        else
        {
            $whereClause = array(
                "{$this->col_id} = ?" => $leasingSchemaTerm
            );
        }

        $result = $this->getDbTable()->delete($whereClause);

        return $result;
    }

    /**
     * Finds a leasingSchemaTerm based on it's primaryKey
     *
     * @param $id int
     *            The id of the leasingSchemaTerm to find
     *
     * @return Quotegen_Model_LeasingSchemaTerm
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return false;
        }
        $row    = $result->current();
        $object = new Quotegen_Model_LeasingSchemaTerm($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches a leasingSchemaTerm
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return Quotegen_Model_LeasingSchemaTerm
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new Quotegen_Model_LeasingSchemaTerm($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all leasingSchemaTerms
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL: A SQL LIMIT offset.
     *
     * @return Quotegen_Model_LeasingSchemaTerm[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row)
        {
            $object = new Quotegen_Model_LeasingSchemaTerm($row->toArray());

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
            "{$this->col_id} = ?" => $id
        );
    }

    /**
     * Fetches all the terms for a leasing schema
     *
     * @param int $leasingSchemaId
     *            The id of the leasing schema
     *
     * @return Quotegen_Model_LeasingSchemaTerm[]
     */
    public function fetchAllForLeasingSchema ($leasingSchemaId)
    {
        return $this->fetchAll(array(
                                    'leasingSchemaId = ?' => $leasingSchemaId
                               ), 'months ASC');
    }

    /**
     * @param Quotegen_Model_LeasingSchemaTerm $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->leasingSchemaId;
    }
}

