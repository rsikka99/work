<?php

class Quotegen_Model_Mapper_LeasingSchema extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_LeasingSchema';
    
    /*
     * Define the primary key of the model association
    */
    public $col_id = 'id';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_LeasingSchema
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_LeasingSchema to the database.
     * If the id is null then it will insert a new row
     *
     * @param $client Quotegen_Model_LeasingSchema
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $object->toArray();
        
        // Remove the id
        unset($data [$this->col_id]);
        
        // Insert the data
        $id = $this->getDbTable()->insert($data);
        
        $object->setId($id);
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_LeasingSchema to the database.
     *
     * @param $client Quotegen_Model_LeasingSchema
     *            The client model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
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
        $rowsAffected = $this->getDbTable()->update($data, array (
                "{$this->col_id} = ?" => $primaryKey 
        ));
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $rowsAffected;
    }

    /**
     * Saves an instance of Quotegen_Model_LeasingSchema to the database.
     * If the id is null then it will insert a new row
     *
     * @param $client mixed
     *            This can either be an instance of Quotegen_Model_LeasingSchema or the
     *            primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($leasingSchema)
    {
        if ($leasingSchema instanceof Quotegen_Model_LeasingSchema)
        {
            $whereClause = array (
                    "{$this->col_id} = ?" => $leasingSchema->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    "{$this->col_id} = ?" => $leasingSchema 
            );
        }
        
        $result = $this->getDbTable()->delete($whereClause);
        return $result;
    }

    /**
     * Finds a client based on it's primaryKey
     *
     * @param $id int
     *            The id of the client to find
     * @return Quotegen_Model_LeasingSchema
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        $object = new Quotegen_Model_LeasingSchema($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches a client
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL: A SQL OFFSET value.
     * @return Quotegen_Model_LeasingSchema
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_LeasingSchema($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches all clients
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $count int
     *            OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL: A SQL LIMIT offset.
     * @return multitype:Quotegen_Model_LeasingSchema
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_LeasingSchema($row->toArray());
            
            // Save the object into the cache
            $this->saveItemToCache($object);
            
            $entries [] = $object;
        }
        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param unknown_type $id            
     * @return array
     */
    public function getWhereId ($id)
    {
        return array (
                "{$this->col_id} = ?" => $id 
        );
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->getId();
    }
}

