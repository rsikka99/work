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

    protected function unsetNullValues ($array)
    {
        return array_filter($array, function  ($value)
        {
            return (! ($value === null));
        });
    }
}
?>