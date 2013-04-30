<?php

/**
 * Class Quotegen_Model_DbTable_Category
 */
class Quotegen_Model_DbTable_Category extends Zend_Db_Table_Abstract
{
    protected $_name = 'categories';
    protected $_primary = array(
        'id'
    );
}