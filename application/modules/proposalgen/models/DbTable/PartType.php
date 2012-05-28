<?php

class Proposalgen_Model_DbTable_PartType extends Zend_Db_Table_Abstract
{
    protected $_name = 'part_type';
    protected $_primary = 'part_type_id';
    protected $_dependentTables = array (
            'Parts', 
            'PricingConfig' 
    );
}
?>
