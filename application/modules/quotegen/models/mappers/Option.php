<?php

class Quotegen_Model_Mapper_Option extends My_Model_Mapper_Abstract
{
    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'Quotegen_Model_DbTable_Option';
    
    /*
     * Define the primary key of the model association
     */
    public $col_id = 'id';

    /**
     * Gets an instance of the mapper
     *
     * @return Quotegen_Model_Mapper_Option
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of Quotegen_Model_Option to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object Quotegen_Model_Option
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
     * Saves (updates) an instance of Quotegen_Model_Option to the database.
     *
     * @param $object Quotegen_Model_Option
     *            The option model to save to the database
     * @param $primaryKey mixed
     *            Optional: The original primary key, in case we're changing it
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $data = $this->unsetNullValues($object->toArray());
        
        if ($primaryKey === null)
        {
            $primaryKey = $data [$this->col_id];
        }
        
        // Update the row
        $rowsAffected = $this->getDbTable()->update($data, array (
                "{$this->col_id} = ?" => $primaryKey 
        ));
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $rowsAffected;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *            This can either be an instance of Quotegen_Model_Option or the
     *            primary key to delete
     * @return mixed The number of rows deleted
     */
    public function delete ($object)
    {
        if ($object instanceof Quotegen_Model_Option)
        {
            $whereClause = array (
                    "{$this->col_id} = ?" => $object->getId() 
            );
        }
        else
        {
            $whereClause = array (
                    "{$this->col_id} = ?" => $object 
            );
        }
        
        $rowsAffected = $this->getDbTable()->delete($whereClause);
        return $rowsAffected;
    }

    /**
     * Finds a option based on it's primaryKey
     *
     * @param $id int
     *            The id of the option to find
     * @return Quotegen_Model_Option
     */
    public function find ($id)
    {
        // Get the item from the cache and return it if we find it.
        $result = $this->getItemFromCache($id);
        if ($result instanceof Quotegen_Model_Option)
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
        $object = new Quotegen_Model_Option($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches a option
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *            OPTIONAL: A SQL OFFSET value.
     * @return Quotegen_Model_Option
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return;
        }
        
        $object = new Quotegen_Model_Option($row->toArray());
        
        // Save the object into the cache
        $this->saveItemToCache($object);
        
        return $object;
    }

    /**
     * Fetches all options
     *
     * @param $where string|array|Zend_Db_Table_Select
     *            OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order string|array
     *            OPTIONAL: A SQL ORDER clause.
     * @param $count int
     *            OPTIONAL: A SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *            OPTIONAL: A SQL LIMIT offset.
     * @return multitype:Quotegen_Model_Option
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_Option($row->toArray());
            
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
                "{$this->col_id} = ?" => $id 
        );
    }

    /**
     * Fetches all options that are available to be added to a device (Anything that is not already added).
     *
     * @param int $id
     *            The primary key of a device
     *            
     * @return multitype:Quotegen_Model_Option The list of available options to add
     */
    public function fetchAllAvailableOptionsForDevice ($id)
    {
        $devOptTableName = Quotegen_Model_Mapper_DeviceOption::getInstance()->getTableName();
        
        $sql = "SELECT * FROM {$this->getTableName()} as opt
                WHERE NOT EXISTS (
                    SELECT * from {$devOptTableName} AS do
                    WHERE do.masterDeviceId = ? AND do.optionId = opt.id
                )
                ORDER BY  opt.name ASC
                ";
        
        $resultSet = $this->getDbTable()
            ->getAdapter()
            ->fetchAll($sql, $id);
        
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_Option($row);
            
            // Save the object into the cache
            $this->saveItemToCache($object);
            
            $entries [] = $object;
        }
        return $entries;
    }

    /**
     * Fetches all options for a device
     *
     * @param int $id
     *            The primary key of a device
     *            
     * @return multitype:Quotegen_Model_Option The list of options
     */
    public function fetchAllDeviceOptionsForDevice ($id)
    {
        $deviceOptionMapper = Quotegen_Model_Mapper_DeviceOption::getInstance();
        $devOptTableName = $deviceOptionMapper->getTableName();
        
        $sql = "SELECT * FROM {$this->getTableName()} as opt
        		JOIN {$devOptTableName} AS do ON opt.{$this->col_id} = do.optionId
                    WHERE do.masterDeviceId = ? AND do.optionId = opt.id
                ORDER BY  opt.name ASC
        ";
        
        $resultSet = $this->getDbTable()
            ->getAdapter()
            ->fetchAll($sql, $id);
        $entries = array ();
        
        foreach ( $resultSet as $row )
        {
            $deviceOption = new Quotegen_Model_DeviceOption($row);
            $deviceOptionMapper->saveItemToCache($deviceOption);
            $option = new Quotegen_Model_Option($row);
            $deviceOption->setOption($option);
            // Save the object into the cache
            $this->saveItemToCache($option);
            
            $entries [] = $deviceOption;
        }
        return $entries;
    }

    /**
     * (non-PHPdoc) @see My_Model_Mapper_Abstract::getPrimaryKeyValueForObject()
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->getId();
    }

    /**
     * Fetches all options for a device
     *
     * @param int $id
     *            The primary key of a device
     *            
     * @return multitype:Quotegen_Model_DeviceConfigurationOption The list of options
     */
    public function fetchAllOptionsForDeviceConfiguration ($id)
    {
        $devOptTableName = Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->getTableName();
        
        $sql = "SELECT * FROM {$devOptTableName} as dco
                JOIN {$this->getTableName()} as opt on dco.optionId = opt.id
                WHERE dco.deviceConfigurationId = ?
                ORDER BY opt.name ASC
        ";
        
        $resultSet = $this->getDbTable()
            ->getAdapter()
            ->fetchAll($sql, $id);
        
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $deviceConfigurationOption = new Quotegen_Model_DeviceConfigurationOption($row);
            
            $option = new Quotegen_Model_Option($row);
            
            $deviceConfigurationOption->option = $option;
            
            // Save the device configuration option to cache
            Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->saveItemToCache($deviceConfigurationOption);
            
            // Save the option into the cache
            $this->saveItemToCache($option);
            
            $entries [] = $deviceConfigurationOption;
        }
        return $entries;
    }

    /**
     * Fetches all options that are available to be added to a device configuration (Anything that is not already
     * added).
     *
     * @param int $id
     *            The primary key of a device configuration
     *            
     * @return multitype:Quotegen_Model_Option The list of available options to add
     */
    public function fetchAllAvailableOptionsForDeviceConfiguration ($id)
    {
        $devOptTableName = Quotegen_Model_Mapper_DeviceConfigurationOption::getInstance()->getTableName();
        $deviceOptionTableName = Quotegen_Model_Mapper_DeviceOption::getInstance()->getTableName();
        
        $sql = "SELECT * FROM  {$deviceOptionTableName} AS do
                JOIN  {$this->getTableName()} AS opt ON do.optionId = opt.id
                WHERE NOT EXISTS (
                    SELECT * from {$devOptTableName} AS dco
                    WHERE dco.deviceConfigurationId = ? AND dco.optionId = opt.id
                )
                ORDER BY  opt.name ASC
                ";
        
        $resultSet = $this->getDbTable()
            ->getAdapter()
            ->fetchAll($sql, $id);
        
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_Option($row);
            
            // Save the object into the cache
            $this->saveItemToCache($object);
            
            $entries [] = $object;
        }
        return $entries;
    }

    /**
     * Fetches all options that are available to be added to a quote device (Anything that is not already
     * added).
     *
     * @param int $quoteDeviceId
     *            The primary key of a quote device
     * @param int $masterDeviceId
     *            The primary key of the device associated with the quote device
     *            
     * @return multitype:Quotegen_Model_Option The list of available options to add
     */
    public function fetchAllAvailableOptionsForQuoteDevice ($quoteDeviceId, $masterDeviceId)
    {
        $quoteDeviceConfigurationOptionTableName = Quotegen_Model_Mapper_QuoteDeviceConfigurationOption::getInstance()->getTableName();
        $quoteDeviceOptionTableName = Quotegen_Model_Mapper_QuoteDeviceOption::getInstance()->getTableName();
        $deviceOptionTableName = Quotegen_Model_Mapper_DeviceOption::getInstance()->getTableName();
        
        $sql = "SELECT * FROM  {$deviceOptionTableName} AS do
                JOIN  {$this->getTableName()} AS opt ON do.optionId = opt.id
                WHERE NOT EXISTS (
                    SELECT * from {$quoteDeviceConfigurationOptionTableName} AS qdco
                    JOIN  {$quoteDeviceOptionTableName} AS qdo ON qdo.id = qdco.quoteDeviceOptionId
                    WHERE qdo.quoteDeviceId = ? AND qdco.optionId = opt.id
                )
                AND do.masterDeviceId = ?
                ORDER BY  opt.name ASC
        ";
        
        $resultSet = $this->getDbTable()
            ->getAdapter()
            ->fetchAll($sql, array (
                $quoteDeviceId, 
                $masterDeviceId 
        ));
        
        $entries = array ();
        foreach ( $resultSet as $row )
        {
            $object = new Quotegen_Model_Option($row);
            
            // Save the object into the cache
            $this->saveItemToCache($object);
            
            $entries [] = $object;
        }
        return $entries;
    }
}

