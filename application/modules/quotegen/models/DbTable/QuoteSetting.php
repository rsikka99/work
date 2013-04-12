<?php

class Quotegen_Model_DbTable_QuoteSetting extends Zend_Db_Table_Abstract
{
    protected $_name = 'quote_settings';
    protected $_primary = array(
        'id'
    );
}