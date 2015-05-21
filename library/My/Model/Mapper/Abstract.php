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
     * The db table to use
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
     * The hash table database objects once retrieved, cached in memory so that we don't try to fetch the same id more
     * than once.
     *
     * @var My_Model_Abstract[]
     */
    protected $_rowHashTable = [];

    /**
     * A hash table to store copies of mappers as singletons.
     *
     * @var My_Model_Mapper_Abstract[]
     */
    private static $_mapperHashTable = [];

    /**
     * Gets an instance of a mapper or class.
     *
     * @param null|string $class The class to get an instance of.
     *
     *
     * @return \My_Model_Mapper_Abstract
     */
    protected static function getCachedInstance ($class = null)
    {
        // By default we get the class that this was called from.
        if ($class === null)
        {
            $class = get_called_class();
        }

        // If we don't have a stored copy yet, instantiate and store.
        if (!array_key_exists($class, self::$_mapperHashTable))
        {
            self::$_mapperHashTable [$class] = new $class();
        }

        // Return the cached copy.
        return self::$_mapperHashTable [$class];
    }

    /**
     * Sets the db table class to be used
     *
     * @param Zend_Db_Table_Abstract|string $dbTable The db table
     *
     * @return My_Model_Mapper_Abstract
     * @throws Exception
     */
    public function setDbTable ($dbTable)
    {
        if (is_string($dbTable))
        {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract)
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
    public function getDbTable ()
    {
        if (null === $this->_dbTable)
        {
            $this->setDbTable($this->_defaultDbTable);
        }

        return $this->_dbTable;
    }

    /**
     * Unset all null values within an array.
     * If you want to set a field to null use new Zend_Db_Expr("NULL")
     *
     * @param array $array
     *
     * @return array|bool
     */
    protected function unsetNullValues ($array)
    {
        return array_filter($array, function ($value)
        {
            return (!($value === null));
        });
    }

    /**
     * Counts how many rows in a table
     *
     * @param null|string|array $where
     * OPTIONAL An SQL WHERE clause as an array
     *
     * @return int|string
     */
    public function count ($where = null)
    {
        $dbTable = $this->getDbTable();

        $select = $dbTable->select();
        $select->from($dbTable, [
            'COUNT(*) as count',
        ]);

        // If we have a where, apply all the where bindings.
        if ($where !== null)
        {
            foreach ($where as $whereStatement => $whereValue)
            {
                $select->where($whereStatement, $whereValue);
            }
        }
        $result = $dbTable->fetchRow($select);

        return ($result) ? $result->count : 0;
    }

    /**
     * Gets an item from the cache based on its key.
     *
     * @param array|string $key
     *                 The key to search the cache with.
     *
     * @param string       $cacheName
     *
     * @return My_Model_Abstract|bool
     */
    public function getItemFromCache ($key, $cacheName = 'default')
    {
        // Convert the key from an array to a string
        if (is_array($key))
        {
            $key = implode('_', $key);
        }

        $key = "{$cacheName}_{$key}";

        // If the item exists, return it.
        if (array_key_exists((string)$key, $this->_rowHashTable))
        {
            return $this->_rowHashTable [$key];
        }

        return false;
    }

    /**
     * Saves a completed model into the database
     *
     * @param My_Model_Abstract $object    Any object that extends My_Model_Abstract
     * @param string            $cacheName The cache name
     */
    public function saveItemToCache (My_Model_Abstract $object, $cacheName = 'default')
    {
        $key = $this->getPrimaryKeyValueForObject($object);
        if (is_array($key))
        {
            $key = implode('_', $key);
        }

        $key = "{$cacheName}_{$key}";

        // Save the item into the cache
        $this->_rowHashTable [$key] = $object;
    }

    /**
     * Deletes an item from the cache.
     *
     * @param mixed  $key
     *            The key of the model that we are deleting. This can be an array, it will be imploded into a single
     *            string using _'s as delimiters.
     * @param string $cacheName
     */
    protected function deleteItemFromCache ($key, $cacheName = 'default')
    {
        // Convert the key from an array to a string
        if (is_array($key))
        {
            $key = implode('_', $key);
        }

        $key = "{$cacheName}_{$key}";

        // Remove the item from the cache if it exists.
        if (array_key_exists($key, $this->_rowHashTable))
        {
            unset($this->_rowHashTable [$key]);
        }
    }

    /**
     *
     */
    public function clearItemCache() {
        foreach (array_keys($this->_rowHashTable) as $key) {
            unset($this->_rowHashTable[$key]);
        }
    }

    /**
     * Gets the name of the database table
     *
     * @return string
     */
    public function getTableName ()
    {
        return $this->getDbTable()->info('name');
    }

    abstract public function insert ($object);

    /**
     * Takes an object and returns a proper value for the primary key
     *
     * @param My_Model_Abstract $object
     */
    abstract public function getPrimaryKeyValueForObject ($object);

    abstract public function save ($object, $primaryKey = null);

    abstract public function delete ($object);

    abstract public function fetch ($where = null, $order = null, $offset = null);

    abstract public function fetchAll ($where = null, $order = null, $count = 25, $offset = null);
}
