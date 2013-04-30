<?php

/**
 * Class Quotegen_Model_DbTable_Quote
 */
class Quotegen_Model_DbTable_Quote extends Zend_Db_Table_Abstract
{
    protected $_name = 'quotes';
    protected $_primary = array(
        'id'
    );
}