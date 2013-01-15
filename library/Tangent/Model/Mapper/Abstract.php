<?php

/**
 * Class Tangent_Model_Mapper_Abstract
 * An abstract class to map database objects to php objects
 * @author "Lee Robert"
 */
abstract class Tangent_Model_Mapper_Abstract
{

    protected $_dbTable;
    protected $_defaultDbTableClassName;

    // Methods that MUST be defined in child classes
    //abstract protected function mapRowToObject ($row);

    //abstract public function save (Application_Model_Abstract $object);
    

    /**
     * Sets an Application_Model_DbTable_*
     * @param mixed $dbTable
     */
    protected function setDbTable ($dbTable)
    {
        if (is_string($dbTable))
        {
            $dbTable = new $dbTable();
        }
        if (! $dbTable instanceof Zend_Db_Table_Abstract)
        {
            throw new Exception('Invalid table data gateway provided in ' . __CLASS__);
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * Returns the DbTable. If none is assigned yet, it will assign a default
     * @return Zend_Db_Table_Abstract
     */
    protected function getDbTable ()
    {
        if (null === $this->_dbTable)
        {
            $this->setDbTable($this->_defaultDbTableClassName);
        }
        return $this->_dbTable;
    }

    /**
     * Fetches a single object from the database
     * @param mixed $primaryKey The values of the primary key(s)
     */
    public function find ($primaryKey)
    {
        $object = null;
        try
        {
            $result = $this->getDbTable()->find($primaryKey);
            if (0 == count($result))
            {
                return;
            }
            $row = $result->current();
            if ($row != null)
            {
                $object = $this->mapRowToObject($row);
            }
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed finding the specified row in " . get_class($this), 0, $e);
        }
        return $object;
    }

    /**
     * Fetches an array of objects from the database with the same syntax as Zend_Db_Table_Abstract
     * @see Zend_Db_Table_Abstract
     * @throws Exception when a db error occurs.
     * @return array(Tangent_Model_Abstract, ..)
     */
    public function fetchAll ($whereClause = null, $order = null, $count = null, $offset = null)
    {
        $entries = array ();
        try
        {
            $resultSet = $this->getDbTable()->fetchAll($whereClause, $order, $count, $offset);
            foreach ( $resultSet as $row )
            {
                $entries [] = $this->mapRowToObject($row);
            }
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to fetch all rows", 0, $e);
        }
        return $entries;
    }

    /**
     * Fetches a single object from the database with the same syntax as Zend_Db_Table_Abstract
     * @see Zend_Db_Table_Abstract
     * @throws Exception when a db error occurs.
     * @return Tangent_Model_Abstract
     */
    public function fetchRow ($whereClause = null, $order = null, $offset = null)
    {
        $object = null;
        try
        {
            $row = $this->getDbTable()->fetchRow($whereClause, $order, $offset);
            if ($row)
            {
                $object = $this->mapRowToObject($row);
            }
        }
        catch ( Exception $e )
        {
            throw new Exception("Failed to fetch a row", 0, $e);
        }
        return $object;
    }

    /**
     * Deletes a row based on the $where statement
     * @see Zend_Db_Table_Abstract
     */
    public function delete ($where)
    {
        
        try
        {
            $this->getDbTable()->delete($where);
        }
        catch ( Exception $e )
        {
            throw new Exception("Error deleting row in " . get_class($this), 0, $e);
        }
    }

    /**
     * Saves a row based on an array of $data. Will insert and update if the item already exists
     * @param array $data
     */
    public function saveRow ($data)
    {
        $insertId = 0;
        $db = $this->getDbTable()->getAdapter();
        try
        {
            $keys = "";
            $values = "";
            $updates = "";
            
            foreach ( $data as $rowName => $rowValue )
            {
                // Keys
                if (strlen($keys))
                    $keys .= ", ";
                $keys .= $rowName;
                
                // Values
                if (strlen($values))
                    $values .= ", ";
                $values .= (isset($rowValue)) ? $db->quote($rowValue) : "NULL";
                
                // Update
                if (strlen($updates))
                    $updates .= ", ";
                $updates .= "$rowName = VALUES($rowName)";
            }
            
            $tableName = $this->getDbTable()->info("name");
            $sql = "INSERT INTO $tableName ($keys) VALUES ($values) ON DUPLICATE KEY UPDATE $updates;";
            $result = $db->query($sql);
            
            $insertId = $db->lastInsertId();
        }
        catch ( Exception $e )
        {
            throw new Exception("Error saving " . get_class($this) . " to the database.\nLast Insert Id:$insertId\n\n SQL:\n $sql", 0, $e);
        }
        
        return $insertId;
    }

    /**
     * Saves multiple rows based on an array of $data. Will insert and update if the item already exists
     * @param array $data
     */
    public function saveRows ($rows)
    {
        if (count($rows) > 0)
        {
            $db = $this->getDbTable()->getAdapter();
            try
            {
                $keys = "";
                $values = "";
                $updates = "";
                $firstRow = true;
                foreach ( $rows as $data )
                {
                    if (strlen($values))
                        $values .= ", ";
                    $values .= "(";
                    $first_value = true;
                    foreach ( $data as $rowName => $rowValue )
                    {
                        // Only add update row once
                        if ($firstRow)
                        {
                            // Keys
                            if (strlen($keys))
                                $keys .= ", ";
                            $keys .= $rowName;
                            
                            // Update
                            if (strlen($updates))
                                $updates .= ", ";
                            $updates .= "$rowName = VALUES($rowName)";
                        }
                        // Values
                        if (! $first_value)
                            $values .= ", ";
                        $values .= (isset($rowValue)) ? $db->quote($rowValue) : "NULL";
                        $first_value = false;
                    }
                    $values .= ")\n";
                    $firstRow = false;
                }
                $tableName = $this->getDbTable()->info("name");
                $sql = "INSERT INTO $tableName ($keys) VALUES\n $values\n ON DUPLICATE KEY UPDATE\n $updates;";
                $result = $db->query($sql);
            }
            catch ( Exception $e )
            {
                throw new Exception("Error saving rows of " . get_class($this) . " to the database.\n\n SQL:\n $sql", 0, $e);
            }
        }
        else
        {
            return 0;
        }
        return $result;
    }
}
?>