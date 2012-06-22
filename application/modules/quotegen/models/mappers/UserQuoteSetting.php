<?php

class Quotegen_Model_Mapper_UserQuoteSetting extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_UserQuoteSetting';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_UserQuoteSetting
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_UserQuoteSetting to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_UserQuoteSetting
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
     * Saves (updates) an instance of Quotegen_Model_UserQuoteSetting to the database.
     *
     * @param $object Quotegen_Model_UserQuoteSetting
     *            The userQuoteSetting model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());
        
        if ($primaryKey === null)
        {
            $primaryKey [] = $data ['userId'];
            $primaryKey [] = $data ['userQuoteSettingId'];
        }
        
        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array (
                'userId = ?' => $primaryKey [0], 
                'userQuoteSettingId = ?' => $primaryKey [1] 
        ));
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *            This can either be an instance of Quotegen_Model_UserQuoteSetting or the
     *            primary key to delete
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_UserQuoteSetting)
        {
            $whereClause = array (
                    'userId = ?' => $object->getUserId(), 
                    'userQuoteSettingId = ?' => $object->getUserQuoteSettingId() 
            );
        }
        else
        {
            $whereClause = array (
                    'userId = ?' => $object [0], 
                    'userQuoteSettingId = ?' => $object [1] 
            );
        }
        
        $rowsAffected = $this->getDbTable()->delete($whereClause);
        return $rowsAffected;
    }

    /**
     * Finds a userQuoteSetting based on it's primaryKey
     *
     * @param $id int
     *            The id of the userQuoteSetting to find
     * @return void Quotegen_Model_UserQuoteSetting
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_UserQuoteSetting)
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
        $object = new Quotegen_Model_UserQuoteSetting($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches a userQuoteSetting
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Quotegen_Model_UserQuoteSetting
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_UserQuoteSetting($row->toArray());
        
        // Save the object into the cache
        $primaryKey [0] = $object->getUserId();
        $primaryKey [1] = $object->getQuoteSettingId();
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches all userQuoteSettings
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Quotegen_Model_UserQuoteSetting
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_UserQuoteSetting($row->toArray());
            
            // Save the object into the cache
            $primaryKey [0] = $object->getUserId();
            $primaryKey [1] = $object->getQuoteSettingId();
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
                'userId = ?' => $id [0], 
                'userQuoteSettingId = ?' => $id [1] 
        );
    }

    /**
     * Takes in a usedid and returns a Quotgen_Model_UserQuoteSetting
     *
     * @param int $userId            
     * @return Quotegen_Model_UserQuoteSetting
     */
    public function fetchUserQuoteSetting ($userId)
    {
        return $this->fetch(array (
                'userId = ?' => $userId 
        ));
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
    */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array ($object->getUserId(),$object->getQuoteSettingId());
    }
}


