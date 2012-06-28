<?php

class Quotegen_Model_Mapper_QuoteDeviceConfiguration extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_quoteDeviceId = 'quoteDeviceId';
    public $col_masterDeviceId = 'masterDeviceId';
    
    /*
     * Mapper Definitions
     */
    
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_QuoteDeviceConfiguration';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_QuoteDeviceConfiguration
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Counts and returns the amount of rows by deviceConfigurationId
     *
     * @param int $deviceConfigurationId            
     * @return number The amount of rows in the database.
     */
    public function countByDeviceId ($deviceConfigurationId)
    {
        return $this->count(array (
                'deviceConfigurationId = ?' => $deviceConfigurationId 
        ));
    }

    /**
     * Saves an instance of Quotegen_Model_QuoteDeviceConfiguration to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_QuoteDeviceConfiguration
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
     * Saves (updates) an instance of Quotegen_Model_QuoteDeviceConfiguration to the database.
     *
     * @param $object Quotegen_Model_QuoteDeviceConfiguration
     *            The quoteDeviceConfiguration model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());
        
        if ($primaryKey === null)
        {
            $primaryKey [] = $data [$this->col_quoteDeviceId];
            $primaryKey [] = $data [$this->col_masterDeviceId];
        }
        
        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array (
                "{$this->col_quoteDeviceId} = ?" => $primaryKey [0], 
                "{$this->col_masterDeviceId} = ?" => $primaryKey [1] 
        ));
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *            This can either be an instance of Quotegen_Model_QuoteDeviceConfiguration or the
     *            primary key to delete
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_QuoteDeviceConfiguration)
        {
            $whereClause = array (
                    "{$this->col_quoteDeviceId} = ?" => $object->getQuoteDeviceId(), 
                    "{$this->col_masterDeviceId} = ?" => $object->getMasterDeviceId() 
            );
        }
        else
        {
            $whereClause = array (
                    "{$this->col_quoteDeviceId} = ?" => $object [0], 
                    "{$this->col_masterDeviceId} = ?" => $object [1] 
            );
        }
        
        $rowsAffected = $this->getDbTable()->delete($whereClause);
        return $rowsAffected;
    }

    /**
     * Delete a quoteDeviceConfiguration by deviceConfigurationId
     * 
     * @param int $deviceConfigurationId            
     * @return number The number of rows affected
     */
    public function deleteQuoteDeviceConfigurationById ($deviceConfigurationId)
    {
        return $this->getDbTable()->delete(array (
                'deviceConfigurationId = ?' => $deviceConfigurationId 
        ));
    }

    /**
     * Finds a quoteDeviceConfiguration based on it's primaryKey
     *
     * @param $id int
     *            The id of the quoteDeviceConfiguration to find
     * @return Quotegen_Model_QuoteDeviceConfiguration
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_QuoteDeviceConfiguration)
        {
            return $result;
        }
        
        // Assuming we don't have a cached object, lets go get it.
        $result = $this->getDbTable()->find($id);
        if (0 == count($result))
        {
            return;
        }
        $row = $result->current();
        $object = new Quotegen_Model_QuoteDeviceConfiguration($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Finds a quoteDeviceConfiguration by device id
     *
     * @param $id int
     *            The id of the device configuration to find
     * @return Quotegen_Model_QuoteDeviceConfiguration
     */
    public function findByDeviceId ($id)
    {
        return $this->fetch(array (
                "{$this->col_masterDeviceId} = ?" => $id 
        ));
    }

    /**
     * Finds a quoteDeviceConfiguration by quote device id
     *
     * @param $id int
     *            The id of the quote device to find
     * @return Quotegen_Model_QuoteDeviceConfiguration
     */
    public function findByQuoteDeviceId ($id)
    {
        return $this->fetch(array (
                "{$this->col_quoteDeviceId} = ?" => $id 
        ));
    }

    /**
     * Fetches a quoteDeviceConfiguration
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return Quotegen_Model_QuoteDeviceConfiguration
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_QuoteDeviceConfiguration($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches all quoteDeviceConfigurations
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Quotegen_Model_QuoteDeviceConfiguration
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_QuoteDeviceConfiguration($row->toArray());
            
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
                "{$this->col_quoteDeviceId} = ?" => $id [0], 
                "{$this->col_masterDeviceId} = ?" => $id [1] 
        );
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array (
                $object->getQuoteDeviceId(), 
                $object->getDeviceId() 
        );
    }
}

