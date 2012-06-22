<?php

class Quotegen_Model_Mapper_LeasingSchemaTerm extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_LeasingSchemaTerm';

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
     * @param $leasingSchemaTerm Quotegen_Model_LeasingSchemaTerm
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
     * Saves (updates) an instance of Quotegen_Model_LeasingSchemaTerm to the database.
     *
     * @param $leasingSchemaTerm Quotegen_Model_LeasingSchemaTerm
     *            The leasingSchemaTerm model to save to the database
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
     * Saves an instance of Quotegen_Model_LeasingSchemaTerm to the database.
     * If the id is null then it will insert a new row
     *
     * @param $leasingSchemaTerm mixed
     *            This can either be an instance of Quotegen_Model_LeasingSchemaTerm or the
     *            primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($leasingSchemaTerm)
    {
        if ($leasingSchemaTerm instanceof Quotegen_Model_LeasingSchemaTerm)
        {
            $whereClause = array (
                    'id = ?' => $leasingSchemaTerm->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    'id = ?' => $leasingSchemaTerm 
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
     * @return void Quotegen_Model_LeasingSchemaTerm
     */
    public function find ($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        $object = new Quotegen_Model_LeasingSchemaTerm($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches a leasingSchemaTerm
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Quotegen_Model_LeasingSchemaTerm
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_LeasingSchemaTerm($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches all leasingSchemaTerms
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Quotegen_Model_LeasingSchemaTerm
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
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
     * Fetches all the terms for a leasing schema
     *
     * @param $leasingSchemaId The
     *            id of the leasing schema
     * @return multitype:Quotegen_Model_LeasingSchemaTerm
     */
    public function fetchAllForLeasingSchema ($leasingSchemaId)
    {
        return $this->fetchAll(array (
                'leasingSchemaId = ?' => $leasingSchemaId 
        ), 'months ASC');
    }
}

