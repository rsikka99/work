<?php

class Quotegen_Model_Mapper_LeasingSchemaRange extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_LeasingSchemaRange';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_LeasingSchemaRange
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_LeasingSchemaRange to the database.
     * If the id is null then it will insert a new row
     *
     * @param $leasingSchemaRange Quotegen_Model_LeasingSchemaRange
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $object->toArray();
        
        // Remove the id
        unset($data ['id']);
        
        // Insert the data
        $id = $this->getDbTable()->insert($data);
        
        $object->setId($id);
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_LeasingSchemaRange to the database.
     *
     * @param $leasingSchemaRange Quotegen_Model_LeasingSchemaRange
     *            The leasingSchemaRange model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());
        
        if ($primaryKey === null)
        {
            $primaryKey = $data ['id'];
        }
        
        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array (
                'id = ?' => $primaryKey 
        ));
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $rowsAffected;
    }

    /**
     * Saves an instance of Quotegen_Model_LeasingSchemaRange to the database.
     * If the id is null then it will insert a new row
     *
     * @param $leasingSchemaRange mixed
     *            This can either be an instance of Quotegen_Model_LeasingSchemaRange or the
     *            primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($leasingSchemaRange)
    {
        if ($leasingSchemaRange instanceof Quotegen_Model_LeasingSchemaRange)
        {
            $whereClause = array (
                    'id = ?' => $leasingSchemaRange->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    'id = ?' => $leasingSchemaRange 
            );
        }
        
        $result = $this->getDbTable()->delete($whereClause);
        return $result;
    }

    /**
     * Finds a leasingSchemaRange based on it's primaryKey
     *
     * @param $id int
     *            The id of the leasingSchemaRange to find
     * @return void Quotegen_Model_LeasingSchemaRange
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        $object = new Quotegen_Model_LeasingSchemaRange($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches a leasingSchemaRange
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Quotegen_Model_LeasingSchemaRange
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_LeasingSchemaRange($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches all leasingSchemaRanges
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Quotegen_Model_LeasingSchemaRange
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_LeasingSchemaRange($row->toArray());
            
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
                'id = ?' => $id 
        );
    }

    /**
     * Fetches all the ranges for a leasing schema
     *
     * @param $leasingSchemaId The
     *            id of the leasing schema
     * @return multitype:Quotegen_Model_LeasingSchemaRange
     */
    public function fetchAllForLeasingSchema ($leasingSchemaId)
    {
        return $this->fetchAll(array (
                'leasingSchemaId = ?' => $leasingSchemaId 
        ), 'startRange ASC');
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
    */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->getId();
    }
}

