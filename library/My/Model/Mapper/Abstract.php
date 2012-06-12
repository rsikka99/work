<?php

/**
 * Class My_Model_Mapper_Abstract
 * An abstract class to map database objects to php objects
 *
 * @author "Lee Robert"
 */
abstract class My_Model_Mapper_Abstract
{
    /**
     * The dbtable to use
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;
    
    /**
     * The default db table class to use
     *
     * @var String
     */
    protected $_defaultDbTable;
    
    /**
     * A hash table to store copies of mappers as singletons.
     *
     * @var array
     */
    private static $_mapperHashTable = array ();

    /**
     * Gets an instance of a mapper or class.
     *
     * @return multitype:
     */
    protected static function getCachedInstance ($class = null)
    {
        // By default we get the class that this was called from.
        if ($class === null)
        {
            $class = get_called_class();
        }
        
        // If we don't have a stored copy yet, instantiate and store.
        if (! array_key_exists($class, self::$_mapperHashTable))
        {
            self::$_mapperHashTable [$class] = new $class();
        }
        
        // Return the cached copy.
        return self::$_mapperHashTable [$class];
    }

    /**
     * Sets the dbtable class to be used
     *
     * @param $dbTable mixed            
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
     * Returns the DbTable.
     * If none is assigned yet, it will assign the default defined in the mapper
     *
     * @return Zend_Db_Table_Abstract
     */
    protected function getDbTable ()
    {
        if (null === $this->_dbTable)
        {
            $this->setDbTable($this->_defaultDbTable);
        }
        return $this->_dbTable;
    }

    /**
     * Unsets all null values within an array.
     * If you want to set a field to null use new Zend_Db_Expr("NULL")
     * 
     * @param unknown_type $array            
     * @return multitype: boolean
     */
    protected function unsetNullValues ($array)
    {
        return array_filter($array, function  ($value)
        {
            return (! ($value === null));
        });
    }

    abstract public function insert ($data);

    abstract public function save ($data, $primaryKey);

    abstract public function delete ($primaryKey);

    abstract public function count ();

    abstract public function fetch ($where = null, $order = null, $offset = null);

    abstract public function fetchAll ($where = null, $order = null, $count = 25, $offset = null);
}
?>