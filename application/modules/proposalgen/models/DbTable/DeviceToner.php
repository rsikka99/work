<?php

class Proposalgen_Model_DbTable_DeviceToner extends Zend_Db_Table_Abstract
{
    protected $_name = 'device_toner';
    protected $_primary = 'toner_id';
    protected $_referenceMap = array (
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'master_device_id' 
            ), 
            'Toner' => array (
                    'columns' => 'toner_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Toner', 
                    'refColumns' => 'toner_id' 
            ) 
    );
}
?>
