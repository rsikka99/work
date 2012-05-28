<?php

class Proposalgen_Model_DbTable_TonerColor extends Zend_Db_Table_Abstract
{
    protected $_name = 'toner_color';
    protected $_primary = 'toner_color_id';
    protected $_dependentTables = array (
            'Toner' 
    );
}
?>
