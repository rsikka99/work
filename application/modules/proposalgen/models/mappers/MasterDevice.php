<?php

class Proposalgen_Model_Mapper_MasterDevice extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Proposalgen_Model_DbTable_MasterDevice';

    /**
     * Gets an instance of the mapper
     *
     * @return Proposalgen_Model_Mapper_MasterDevice
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Proposalgen_Model_MasterDevice to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Proposalgen_Model_MasterDevice
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $this->unsetNullValues($object->toArray());
        
        // Remove the id
        unset($data ['id']);
        
        // Insert the data
        $id = $this->getDbTable()->insert($data);
        
        $object->setId($id);
        
        // Save the object into the cache
        $this->saveItemToCache($object, $id);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Proposalgen_Model_MasterDevice to the database.
     *
     * @param $object Proposalgen_Model_MasterDevice
     *            The masterDevice model to save to the database
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
     *            This can either be an instance of Proposalgen_Model_MasterDevice or the
     *            primary key to delete
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Proposalgen_Model_MasterDevice)
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
     * Finds a masterDevice based on it's primaryKey
     *
     * @param $id int
     *            The id of the masterDevice to find
     * @return void Proposalgen_Model_MasterDevice
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Proposalgen_Model_MasterDevice)
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
        $object = new Proposalgen_Model_MasterDevice($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object, $id);
        
        return $object;
    }

    /**
     * Fetches a masterDevice
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL An SQL OFFSET value.
     * @return void Proposalgen_Model_MasterDevice
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Proposalgen_Model_MasterDevice($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object, $object->getId());
        
        return $object;
    }

    /**
     * Fetches all masterDevices
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL An SQL ORDER clause.
     * @param $count int
     *            OPTIONAL An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL An SQL LIMIT offset.
     * @return multitype:Proposalgen_Model_MasterDevice
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Proposalgen_Model_MasterDevice($row->toArray());
            
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
                'id = ?' => $id 
        );
    }

    /**
     * Fetchs all master devices that are available to be used in the quote generator
     */
    public function fetchAllAvailableMasterDevices ()
    {
        $qdTableName = Quotegen_Model_Mapper_Device::getInstance()->getTableName();
        
        $sql = "SELECT * FROM {$this->getTableName()} as md
        LEFT JOIN {$qdTableName} AS qd ON qd.masterDeviceId = md.id
        WHERE qd.masterDeviceId is null
        ORDER BY  md.manufacturer_id ASC, md.printer_model ASC
        ";

        $resultSet = $this->getDbTable()->getAdapter()->fetchAll($sql);
        
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Proposalgen_Model_MasterDevice($row);
        
            // Save the object into the cache
            $this->saveItemToCache($object, $object->getId());
        
            $entries [] = $object;
        }
        return $entries;
    }
}

