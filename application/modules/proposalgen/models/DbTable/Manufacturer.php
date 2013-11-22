<?php

/**
 * Class Proposalgen_Model_DbTable_Manufacturer
 */
class Proposalgen_Model_DbTable_Manufacturer extends Zend_Db_Table_Abstract
{
    protected $_name = 'manufacturers';
    protected $_primary = 'id';
    protected $_dependentTables = array(
        'MasterDevice'
    );
}