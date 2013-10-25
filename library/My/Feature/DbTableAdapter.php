<?php

/**
 * Class My_Feature_DbTableAdapter
 */
class My_Feature_DbTableAdapter implements My_Feature_AdapterInterface
{
    /**
     * @var My_Feature_DbTableInterface
     */
    protected $_dbTable;

    /**
     * @var string
     */
    protected $_dbTableClassName;

    /**
     * Sets the dbTableClassName
     *
     * @param $options
     */
    public function __construct ($options)
    {
        $this->_dbTableClassName = $options['className'];
    }

    /**
     * Getter for _dbTable
     *
     * @throws My_Feature_InvalidClassException
     * @return \My_Feature_DbTableInterface
     */
    public function getDbTable ()
    {
        if (!isset($this->_dbTable))
        {
            if (class_exists($this->_dbTableClassName))
            {
                $this->_dbTable = new $this->_dbTableClassName();
            }
            else
            {
                throw new My_Feature_InvalidClassException("Invalid db  table class name '" . $this->_dbTableClassName . "'");
            }
        }

        return $this->_dbTable;
    }

    /**
     * Setter for _dbTable
     *
     * @param \My_Feature_DbTableInterface $dbTable
     */
    public function setDbTable ($dbTable)
    {
        $this->_dbTable = $dbTable;
    }

    /**
     * @return string []
     */
    public function getFeatures ()
    {
        return $this->getDbTable()->getFeatures();
    }
}