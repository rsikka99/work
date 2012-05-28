<?php

class Proposalgen_Model_DbTable_Manufacturer extends Zend_Db_Table_Abstract
{
    protected $_name = 'manufacturer';
    protected $_primary = 'manufacturer_id';
    protected $_dependentTables = array (
            'Parts, MasterDevice' 
    );
}
?>
