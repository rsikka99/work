<?php

class My_Auth_Adapter extends Zend_Auth_Adapter_DbTable
{
    protected $_customerColumn = 'customer_id';
    protected $_customerId = false;

    public function setCustomerId ($id)
    {
        $this->_customerId = $id;
        return $this;
    }

    public function getCustomerId ()
    {
        return $this->_customerId !== false ? $this->_customerId : '';
    }

    public function _authenticateCreateSelect ()
    {
        $select = parent::_authenticateCreateSelect();
        $select->where($this->_zendDb->quoteIdentifier($this->_customerColumn, true) . " = ?", $this->getCustomerId());
        return $select;
    }
}