<?php

class Proposalgen_Model_DbTable_Meters extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'meters';
    protected $_primary = 'meter_id';
    protected $_referenceMap = array (
            'DeviceInstance' => array (
                    'columns' => 'device_instance_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_DeviceInstance', 
                    'refColumns' => 'device_instance_id' 
            ) 
    );
}

?>
