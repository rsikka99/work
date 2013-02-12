<?php

class Quotegen_Model_DbTable_Quote extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_quotes';
    protected $_primary = array(
        'id'
    );
}