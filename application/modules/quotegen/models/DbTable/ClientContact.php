<?php

class Quotegen_Model_DbTable_ClientContact extends Zend_Db_Table_Abstract
{
    protected $_name = 'client_contacts';
    protected $_primary = 'clientId';
}