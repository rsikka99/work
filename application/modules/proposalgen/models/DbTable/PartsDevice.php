<?php

class Proposalgen_Model_DbTable_PartsDevice extends Zend_Db_Table_Abstract
{
    protected $_name = 'parts_device';
    protected $_primary = array (
            'part_id', 
            'master_device_id' 
    );
    protected $_referenceMap = array (
            'Parts' => array (
                    'columns' => 'part_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Parts', 
                    'refColumns' => 'part_id' 
            ), 
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'master_device_id' 
            ) 
    );
}

?>
