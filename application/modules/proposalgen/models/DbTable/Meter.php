<?php

class Proposalgen_Model_DbTable_Meter extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'pgen_device_instance_meters';
    protected $_primary = 'id';
    protected $_referenceMap = array (
            'DeviceInstance' => array (
                    'columns' => 'device_instance_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_DeviceInstance', 
                    'refColumns' => 'id' 
            ) 
    );
}
