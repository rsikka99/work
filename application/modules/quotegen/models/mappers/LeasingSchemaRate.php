<?php

class Quotegen_Model_Mapper_LeasingSchemaRate extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_LeasingSchemaRate';
    
    /*
     * Define the primary key of the model association
    */
    public $col_leasingSchemaTermId = 'leasingSchemaTermId';
    public $col_leasingSchemaRangeId = 'leasingSchemaRangeId';
    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_LeasingSchemaRate
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_LeasingSchemaRate to the database.
     * If the id is null then it will insert a new row
     *
     * @param $leasingSchemaRate Quotegen_Model_LeasingSchemaRate
     *            The object to insert
     * @return mixed The primary key of the new row
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
     * Saves (updates) an instance of Quotegen_Model_LeasingSchemaRate to the database.
     *
     * @param $leasingSchemaRate Quotegen_Model_LeasingSchemaRate
     *            The leasingSchemaRate model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
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
        $rowsAffected = $this->getDbTable()->update($data, array (
                "{$this->col_leasingSchemaTermId} = ?" => $primaryKey [0], 
                "{$this->col_leasingSchemaRangeId} = ?" => $primaryKey [1] 
        ));
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $rowsAffected;
    }

    /**
     * Saves an instance of Quotegen_Model_LeasingSchemaRate to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object mixed
     *            This can either be an instance of Quotegen_Model_LeasingSchemaRate or the
     *            primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_LeasingSchemaRate)
        {
            $whereClause = array (
                    "{$this->col_leasingSchemaTermId} = ?" => $object->getLeasingSchemaTermId(), 
                    "{$this->col_leasingSchemaRangeId} = ?" => $object->getLeasingSchemaRangeId() 
            );
        }
        else
        {
            $whereClause = array (
                    "{$this->col_leasingSchemaTermId} = ?" => $object [0], 
                    "{$this->col_leasingSchemaRangeId} = ?" => $object [1] 
            );
        }
        
        $result = $this->getDbTable()->delete($whereClause);
        return $result;
    }

    /**
     * Finds a leasingSchemaRate based on it's primaryKey
     *
     * @param $id int
     *            The id of the leasingSchemaRate to find
     * @return Quotegen_Model_LeasingSchemaRate
     */
    public function find ($id)
    {
        
        $result = $this->getDbTable()->find($id[0], $id[1]);
        
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        $object = new Quotegen_Model_LeasingSchemaRate($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches a leasingSchemaRate
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL: A SQL OFFSET value.
     * @return Quotegen_Model_LeasingSchemaRate
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_LeasingSchemaRate($row->toArray());
        
        $primaryKey [] = $object->getLeasingSchemaTermId();
        $primaryKey [] = $object->getLeasingSchemaRangeId();
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches all leasingSchemaRates
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $count int
     *            OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL: A SQL LIMIT offset.
     * @return multitype:Quotegen_Model_LeasingSchemaRate
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_LeasingSchemaRate($row->toArray());
            
            $primaryKey [] = $object->getLeasingSchemaTermId();
            $primaryKey [] = $object->getLeasingSchemaRangeId();
            
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
                "{$this->col_leasingSchemaTermId} = ?" => $id [0], 
                "{$this->col_leasingSchemaRangeId} = ?" => $id [1] 
        );
    }

    /**
     * Fetches all the rates for a leasing schema
     *
     * @param $leasingSchemaId The
     *            id of the leasing schema
     * @return multitype:Quotegen_Model_LeasingSchemaRate
     */
    public function fetchAllForLeasingSchema ($leasingSchemaId)
    {
        $rates = array ();
        
        // Get the table names for convieniece
        $termTableName = Quotegen_Model_Mapper_LeasingSchemaTerm::getInstance()->getTableName();
        $rangeTableName = Quotegen_Model_Mapper_LeasingSchemaRange::getInstance()->getTableName();
        $schemaTableName = Quotegen_Model_Mapper_LeasingSchema::getInstance()->getTableName();
        $rateTableName = $this->getTableName();
        
        // Create a select statement
        $select = Quotegen_Model_Mapper_LeasingSchema::getInstance()->getDbTable()->select(true);
        $select->joinRight(array (
                'terms' => $termTableName 
        ), "terms.leasingSchemaId = {$schemaTableName}.id");
        $select->join(array (
                'rates' => $rateTableName 
        ), "terms.id = rates.leasingSchemaTermId");
        $select->joinRight(array (
                'ranges' => $rangeTableName 
        ), "ranges.id = rates.leasingSchemaRangeId");
        
        $select->where("{$schemaTableName}.id = ?", $leasingSchemaId);
        $select->setIntegrityCheck(false);
        
        $result = $select->query()->fetchAll();
        
        if ($result && count($result) > 0)
        {
            foreach ( $result as $row )
            {
                $rate = new Quotegen_Model_LeasingSchemaRate($row);
                
                $primaryKey [] = $rate->getLeasingSchemaTermId();
                $primaryKey [] = $rate->getLeasingSchemaRangeId();
                
                // Save the object into the cache
                $this->saveItemToCache($rate, $primaryKey);
                
                $rates [$rate->getLeasingSchemaTermId()] [$rate->getLeasingSchemaRangeId()] = $rate;
            }
        }
        
        return $rates;
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array (
                $object->getLeasingSchemaTermId(), 
                $object->getLeasingSchemaRangeId() 
        );
    }
}

