<?php

class Quotegen_Model_Mapper_QuoteSetting extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_QuoteSetting';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_QuoteSetting
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_QuoteSettings to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_QuoteSetting
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
        
        // Sets the object id
        $object->setId($id);
        
        // Save the object into the cache
        $this->saveItemToCache($object, $id);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_QuoteSettings to the database.
     *
     * @param $objects Quotegen_Model_QuoteSetting
     *            The quote settings model to save to the database
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
        $this->saveItemToCache($object, $primaryKey);
        
        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *            This can either be an instance of Quotegen_Model_QuoteSetting or the
     *            primary key to delete
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_QuoteSetting)
        {
            $whereClause = array (
                    'id = ?' => $object->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    'id = ?' => $object 
            );
        }
        
        $rowsAffected = $this->getDbTable()->delete($whereClause);
        return $rowsAffected;
    }

    /**
     * Finds a quoteSetting based on it's primaryKey
     *
     * @param $id int
     *            The id of the quoteSetting to find
     * @return void Quotegen_Model_QuoteSettings
     */
    public function find ($id)
    {
        $result = $this->getItemFromCache($id);
        
        // If item is in cache return that objet
        if ($result instanceof Quotegen_Model_QuoteSetting)
        {
            return $result;
        }
        
        $result = $this->getDbTable()->find($id);
        
        // If item not found return to the caller
        if (0 == count($result))
        {
            return;
        }
        
        // Go to first row of result and set $row
        $row = $result->current();
        $object = new Quotegen_Model_QuoteSetting($row->toArray());
        
        // Save item to cache
        $this->saveItemToCache($object, $id);
        
        return $object;
    }

    /**
     * Fetches a quote setting
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Quotegen_Model_QuoteSettings
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_QuoteSetting($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object, $object->getId());
        
        return $object;
    }

    /**
     * Fetches all quote settings
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Quotegen_Model_QuoteSettings
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $entries [] = new Quotegen_Model_QuoteSetting($row->toArray());
        }
        return $entries;
    }
}

