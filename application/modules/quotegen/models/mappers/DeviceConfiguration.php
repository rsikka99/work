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
     * Saves an instance of Quotegen_Model_DeviceConfiguration to the database.
     * If the id is null then it will insert a new row
     *
     * @param $deviceConfiguration Quotegen_Model_DeviceConfiguration
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $object->toArray();
        
        // Remove the id
        unset($data ['masterDeviceId']);
        
        // Insert the data
        $id = $this->getDbTable()->insert($data);
        
        $object->setId($id);
        
        // Save the object into the cache
        $this->saveItemToCache($object, $id);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_DeviceConfiguration to the database.
     *
     * @param $deviceConfiguration Quotegen_Model_DeviceConfiguration
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
            $primaryKey = $data ['masterDeviceId'];
        }
        
        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array (
                'masterDeviceId = ?' => $primaryKey 
        ));
        
        // Save the object into the cache
        $this->saveItemToCache($object, $primaryKey);
        
        return $rowsAffected;
    }

    /**
     * Saves an instance of Quotegen_Model_DeviceConfiguration to the database.
     * If the id is null then it will insert a new row
     *
     * @param $deviceConfiguration mixed
     *            This can either be an instance of Quotegen_Model_DeviceConfiguration or the
     *            primary key to delete
     * @return mixed The primary key of the new row
     */
    public function delete ($deviceConfiguration)
    {
        if ($deviceConfiguration instanceof Quotegen_Model_DeviceConfiguration)
        {
            $whereClause = array (
                    'masterDeviceId = ?' => $deviceConfiguration->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    'masterDeviceId = ?' => $deviceConfiguration 
            );
        }
        
        $result = $this->getDbTable()->delete($whereClause);
        return $result;
    }

    /**
     * Finds a deviceConfiguration based on it's primaryKey
     *
     * @param $id int
     *            The id of the deviceConfiguration to find
     * @return void Quotegen_Model_DeviceConfiguration
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_DeviceConfiguration)
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
        $this->saveItemToCache($object, $id);
        
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
     * @return void Quotegen_Model_DeviceConfiguration
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
        $this->saveItemToCache($object, $object->getId());
        
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
            $this->saveItemToCache($object, $object->getId());
            
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
                'masterDeviceId = ?' => $id 
        );
    }
}

