<?php

class Quotegen_Model_Mapper_DeviceConfiguration extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_DeviceConfiguration';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_DeviceConfiguration
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }
    
    /**
     * Counts and returns the amount of rows by masterDeviceId
     *
     * @param int $masterDeviceId
     * @return number The amount of rows in the database.
     */
    public function countByDeviceId ($masterDeviceId)
    {
        return $this->count(array (
                'masterDeviceId = ?' => $masterDeviceId
        ));
    }

    /**
     * Saves an instance of Quotegen_Model_DeviceConfiguration to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_DeviceConfiguration
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
     * Saves (updates) an instance of Quotegen_Model_DeviceConfiguration to the database.
     *
     * @param $object Quotegen_Model_DeviceConfiguration
     *            The deviceConfiguration model to save to the database
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
     * Deletes rows from the database.
     *
     * @param $object mixed
     *            This can either be an instance of Quotegen_Model_DeviceConfiguration or the
     *            primary key to delete
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_DeviceConfiguration)
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
     * Finds a deviceConfiguration based on it's primaryKey
     *
     * @param $id int
     *            The id of the deviceConfiguration to find
     * @return Quotegen_Model_DeviceConfiguration
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result && $result instanceof Quotegen_Model_DeviceConfiguration)
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
        $object = new Quotegen_Model_DeviceConfiguration($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches a deviceConfiguration
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return Quotegen_Model_DeviceConfiguration
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_DeviceConfiguration($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches all deviceConfigurations
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Quotegen_Model_DeviceConfiguration
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_DeviceConfiguration($row->toArray());
            
            // Save the object into the cache
            $this->saveItemToCache($object);
            
            $entries [] = $object;
        }
        return $entries;
    }

    /**
     * Fetches all deviceConfigurations by masterdeviceId
     * @param int $masterDeviceId            
     * @return multitype:Quotegen_Model_DeviceConfiguration
     */
    public function fetchAllDeviceConfigurationByDeviceId ($masterDeviceId)
    {
        $deviceConfigurations = array ();
        $resultSet = $this->getDbTable()->fetchAll(array (
                'masterDeviceId = ?' => $masterDeviceId 
        ));
        
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_DeviceConfiguration($row->toArray());
            
            // Save the item into cahce
            $this->saveItemToCache($object);
            $deviceConfigurations [] = $object;
        }
        
        return $deviceConfigurations;
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
    
    /*
     * (non-PHPdoc) @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->getMasterDeviceId();
    }
}

