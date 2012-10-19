<?php

class Quotegen_Model_DbTable_ClientAddress extends Zend_Db_Table_Abstract
{
    protected $_name = 'client_addresses';
    protected $_primary = 'clientId';
}