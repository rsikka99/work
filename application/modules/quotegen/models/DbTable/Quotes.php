<?php

class Quotegen_Model_DbTable_Quotes extends Zend_Db_Table_Abstract
{
    protected $_name = 'quotegen_quotes';
    protected $_primary = array (
            'id' 
    );
}