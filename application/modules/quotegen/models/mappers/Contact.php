<?php

/**
 * Quotegen_Model_Mapper_Contact
 *
 * @author Tyson Riehl
 *        
 */
class Quotegen_Model_Mapper_Contact extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_Contact';
    
    /*
     * Define the primary key of the model association
     */
    public $col_id = 'id';
    public $col_clientId = 'clientId';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_Contact
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_Contact to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_Contact
     *            The object to insert
     * @return mixed The primary key of the new row
     */
    public function insert (&$object)
    {
        // Get an array of data to save
        $data = $object->toArray();
        
        // Remove the id
        unset($data [$this->col_id]);
        
        // Insert the data
        $id = $this->getDbTable()->insert($data);
        
        $object->setId($id);
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $id;
    }

    /**
     * Saves (updates) an instance of Quotegen_Model_Contact to the database.
     *
     * @param $object Quotegen_Model_Contact
     *            The client model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        //$object->setExtension(null);
        $data = $object->toArray();
        $data = $this->unsetNullValues($object->toArray());
        if ($primaryKey === null)
        {
            $primaryKey = $data [$this->col_id];
        }
        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array (
                "{$this->col_id}  = ?" => $primaryKey 
        ));
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *            This can either be an instance of Quotegen_Model_Contact or the
     *            primary key to delete
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_Contact)
        {
            $whereClause = array (
                    "{$this->col_id}  = ?" => $object->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    "{$this->col_id}  = ?" => $object 
            );
        }
        
        $rowsAffected = $this->getDbTable()->delete($whereClause);
        return $rowsAffected;
    }

    /**
     * Finds a client based on it's primaryKey
     *
     * @param $id int
     *            The id of the client to find
     * @return Quotegen_Model_Contact
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_Contact)
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
        $object = new Quotegen_Model_Contact($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches a client
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL: A SQL OFFSET value.
     * @return Quotegen_Model_Contact
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_Contact($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches all clients
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $count int
     *            OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL: A SQL LIMIT offset.
     * @return multitype:Quotegen_Model_Contact
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_Contact($row->toArray());
            
            // Save the object into the cache
            $this->saveItemToCache($object);
            
            $entries [] = $object;
        }
        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param int $id
     *            the id of the country to find
     * @return array
     */
    public function getWhereId ($id)
    {
        return array (
                "{$this->col_id}  = ?" => $id 
        );
    }

    /**
     * Gets a where clause for filtering by clientId
     *
     * @param int $id
     *            the id of the client
     * @return array
     */
    public function getWhereClientId ($id)
    {
        return array (
                "{$this->col_clientId}  = ?" => $id 
        );
    }

    /**
     * (non-PHPdoc) @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->getId();
    }

    /**
     * Gets a contact using a client Id
     * 
     * @param int $clientId            
     * @return Quotegen_Model_Contact
     */
    public function getContactByClientId ($clientId)
    {
        return $this->fetch($this->getWhereClientId($clientId));
    }
}
