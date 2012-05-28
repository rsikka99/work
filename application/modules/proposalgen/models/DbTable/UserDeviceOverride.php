<?php

class Proposalgen_Model_DbTable_UserDeviceOverride extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'user_device_override';
    protected $_primary = array (
            'user_id', 
            'master_device_id' 
    );
    protected $_referenceMap = array (
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'master_device_id' 
            ), 
            'Users' => array (
                    'columns' => 'user_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Users', 
                    'refColumns' => 'user_id' 
            ) 
    );
}

?>
