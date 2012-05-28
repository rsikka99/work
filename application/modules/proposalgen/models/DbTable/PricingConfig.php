<?php

class Proposalgen_Model_DbTable_PricingConfig extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'pricing_config';
    protected $_primary = 'pricing_config_id';
    protected $_dependentTables = array (
            'Users', 
            'DealerCompany' 
    );
    protected $_referenceMap = array (
            'ColorTonerPartTypeId' => array (
                    'columns' => 'color_toner_part_type_id', 
                    
                    'refTableClass' => 'Proposalgen_Model_DbTable_PartType', 
                    'refColumns' => 'part_type_id' 
            ), 
            'MonoTonerPartTypeId' => array (
                    'columns' => 'mono_toner_part_type_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_PartType', 
                    'refColumns' => 'part_type_id' 
            ) 
    );
}

?>
