<?php

class Proposalgen_Model_DbTable_PricingConfig extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'proposalgenerator_pricing_configs';
    protected $_primary = 'id';
    protected $_dependentTables = array (
            'Users' 
    );
    protected $_referenceMap = array (
            'ColorTonerPartTypeId' => array (
                    'columns' => 'color_toner_part_type_id', 
                    
                    'refTableClass' => 'Proposalgen_Model_DbTable_PartType', 
                    'refColumns' => 'id' 
            ), 
            'MonoTonerPartTypeId' => array (
                    'columns' => 'mono_toner_part_type_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_PartType', 
                    'refColumns' => 'id' 
            ) 
    );
}