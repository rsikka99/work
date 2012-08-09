<?php

class Quotegen_Model_Mapper_QuoteDeviceConfigurationOption extends My_Model_Mapper_Abstract
{
    /*
     * Column name definitions. Define all columns up here and use them down below.
     */
    public $col_quoteDeviceOptionId = 'quoteDeviceOptionId';
    public $col_optionId = 'optionId';
    
    /*
     * Mapper Definitions
     */
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_QuoteDeviceConfigurationOption';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_QuoteDeviceConfigurationOption
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_QuoteDeviceConfigurationOption to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_QuoteDeviceConfigurationOption
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
     * (non-PHPdoc)
     *
     * @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array (
                $object->getQuoteDeviceOptionId(), 
                $object->getOptionId() 
        );
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_QuoteDeviceConfigurationOption to the database.
     *
     * @param $object Quotegen_Model_QuoteDeviceConfigurationOption
     *            The quoteDeviceConfigurationOption model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());
        
        if ($primaryKey === null)
        {
            $primaryKey = $this->col_quoteDeviceOptionId;
        }
        
        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array (
                "{$this->col_quoteDeviceOptionId} = ?" => $primaryKey 
        ));
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *            This can either be an instance of Quotegen_Model_QuoteDeviceConfigurationOption or the
     *            primary key to delete
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_QuoteDeviceConfigurationOption)
        {
            $whereClause = array (
                    "{$this->col_quoteDeviceOptionId} = ?" => $object->getQuoteDeviceOptionId() 
            );
        }
        else
        {
            $whereClause = array (
                    "{$this->col_quoteDeviceOptionId} = ?" => $object 
            );
        }
        
        $rowsAffected = $this->getDbTable()->delete($whereClause);
        return $rowsAffected;
    }

    /**
     * Finds a quoteDeviceConfigurationOption based on it's primaryKey
     *
     * @param $id int
     *            The id of the quoteDeviceConfigurationOption to find
     * @return Quotegen_Model_QuoteDeviceConfigurationOption
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_QuoteDeviceConfigurationOption)
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
        $object = new Quotegen_Model_QuoteDeviceConfigurationOption($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches a quoteDeviceConfigurationOption
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL: A SQL OFFSET value.
     * @return Quotegen_Model_QuoteDeviceConfigurationOption
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_QuoteDeviceConfigurationOption($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches all quoteDeviceConfigurationOptions
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $count int
     *            OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL: A SQL LIMIT offset.
     * @return multitype:Quotegen_Model_QuoteDeviceConfigurationOption
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_QuoteDeviceConfigurationOption($row->toArray());
            
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
                "{$this->col_quoteDeviceOptionId} = ?" => $id 
        );
    }

    /**
     * Finds a quote device configuration option by option id
     *
     * @param number $quoteDeviceOptionId            
     * @return Quotegen_Model_QuoteDeviceConfigurationOption
     */
    public function findByQuoteDeviceOptionId ($quoteDeviceOptionId)
    {
        return $this->fetch(array (
                "{$this->col_quoteDeviceOptionId} = ?" => $quoteDeviceOptionId 
        ));
    }
}


