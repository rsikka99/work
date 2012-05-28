<?php

class Proposalgen_Model_DbTable_Toner extends Zend_Db_Table_Abstract
{
    protected $_name = 'toner';
    protected $_primary = array (
            'toner_id' 
    );
    protected $_referenceMap = array (
            'Parts' => array (
                    'columns' => 'part_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Parts', 
                    'refColumns' => 'part_id' 
            ), 
            
            'TonerColor' => array (
                    'columns' => 'toner_color_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_TonerColor', 
                    'refColumns' => 'toner_color_id' 
            ), 
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'master_device_id' 
            ) 
    );
}
?>
