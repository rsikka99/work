<?php

class My_Paginator_MapperAdapter implements Zend_Paginator_Adapter_Interface
{
    /**
     * An instance of a mapper to use to map objects
     *
     * @var My_Model_Mapper_Abstract
     */
    protected $_mapper;
    
    /**
     * The where array to use with the count and fetchall functions
     * 
     * @var array
     */
    protected $_where;
    
    /**
     * An array of objects
     *
     * @var array<My_Model_Abstract>
     */
    protected $_objects = array ();

    public function __construct (My_Model_Mapper_Abstract $mapper, $where = null)
    {
        $this->_mapper = $mapper;
        $this->_where = $where;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param integer $offset
     *            Page offset
     * @param integer $itemCountPerPage
     *            Number of items per page
     * @return array
     */
    public function getItems ($offset, $itemCountPerPage)
    {
        $this->_objects = $this->_mapper->fetchAll($this->_where, null, $itemCountPerPage, $offset);
        return $this->_objects;
    }
    
    /*
     * (non-PHPdoc) @see Countable::count()
     */
    public function count ()
    {
        return $this->_mapper->count($this->_where);
    }
}