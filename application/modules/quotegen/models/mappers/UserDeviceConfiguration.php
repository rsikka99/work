<?php

class Quotegen_Model_Mapper_UserDeviceConfiguration extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_UserDeviceConfiguration';
    
    /*
     * Define the primary key of the model association
    */
    public $col_deviceConfigurationId = 'deviceConfigurationId';
    public $col_userId = 'userId';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_UserDeviceConfiguration
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
                "{$this->col_deviceConfigurationId} = ?" => $deviceConfigurationId 
        ));
    }

    /**
     * Saves an instance of Quotegen_Model_UserDeviceConfiguration to the database.
     *
     * @param $object Quotegen_Model_UserDeviceConfiguration
     *            The object to insert
     * @return the id of the new row
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
     * Saves (updates) an instance of Quotegen_Model_UserDeviceConfiguration to the database.
     *
     * @param $object Quotegen_Model_UserDeviceConfiguration
     *            The Quotegen_Model_UserDeviceConfiguration object to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());
        
        if ($primaryKey === null)
        {
            $primaryKey [] = $data [$this->col_deviceConfigurationId];
            $primaryKey [] = $data [$this->col_userId];
        }
        
        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array (
                "{$this->col_deviceConfigurationId} = ?" => $primaryKey [0], 
                "{$this->col_userId} = ?" => $primaryKey [1] 
        ));
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $rowsAffected;
    }

    /**
     * Deletes an instance of Quotegen_Model_UserDeviceConfiguration from the database.
     *
     * @param $userDeviceConfiguration mixed
     *            This can either be an instance of Quotegen_Model_UserDeviceConfiguration or the
     *            primary key to delete
     * @return The number of rows that have been affected
     */
    public function delete ($userDeviceConfiguration)
    {
        if ($userDeviceConfiguration instanceof Quotegen_Model_UserDeviceConfiguration)
        {
            $whereClause = array (
                    "{$this->col_deviceConfigurationId} = ?" => $userDeviceConfiguration->getDeviceConfigurationId(), 
                    "{$this->col_userId} = ?" => $userDeviceConfiguration->getUserId() 
            );
        }
        else
        {
            $whereClause = array (
                    "{$this->col_deviceConfigurationId} = ?" => $userDeviceConfiguration [0], 
                    "{$this->col_userId} = ?" => $userDeviceConfiguration [1] 
            );
        }
        
        $rowsAffected = $this->getDbTable()->delete($whereClause);
        return $rowsAffected;
    }

    /**
     * Deletes a deviceConfiguration by deviceConfigurationId
     *
     * @param int $deviceConfigurationId            
     * @return number The amount of rows affected
     */
    public function deleteUserDeviceConfigurationByDeviceId ($deviceConfigurationId)
    {
        return $rowsAffected = $this->getDbTable()->delete(array (
                "{$this->col_deviceConfigurationId} = ?" => $deviceConfigurationId 
        ));
    }

    /**
     * Finds a userDeviceConfiguration based on it's primaryKey
     *
     * @param $id int
     *            The id of the template to find
     * @return Quotegen_Model_UserDeviceConfiguration
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_UserDeviceConfiguration)
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
        $object = new Quotegen_Model_UserDeviceConfiguration($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches a userDeviceConfiguration
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL: A SQL OFFSET value.
     * @return object Quotegen_Model_UserDeviceConfiguration
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_UserDeviceConfiguration($row->toArray());
        
        // Save the object into the cache
        $primaryKey [0] = $object->getDeviceConfigurationId();
        $primaryKey [1] = $object->getUserId();
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches all userDeviceConfiguration
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $count int
     *            OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL: A SQL LIMIT offset.
     * @return multitype:Quotegen_Model_UserDeviceConfiguration
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_UserDeviceConfiguration($row->toArray());
            
            // Save the object into the cache          
            $primaryKey [0] = $object->getDeviceConfigurationId();
            $primaryKey [1] = $object->getUserId();
            $this->saveItemToCache($object);
            
            $entries [] = $object;
        }
        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param
     *            array mixed$id
     * @return array
     */
    public function getWhereId ($id)
    {
        return array (
                "{$this->col_deviceConfigurationId} = ?" => $id [0], 
                "{$this->col_userId} = ?" => $id [1] 
        );
    }
    
    /*
     * (non-PHPdoc) @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return array (
                $object->getUserId(), 
                $object->getDeviceConfigurationId() 
        );
    }
}

