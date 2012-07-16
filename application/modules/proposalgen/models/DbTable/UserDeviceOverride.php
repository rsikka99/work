<?php

class Proposalgen_Model_DbTable_UserDeviceOverride extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'pgen_user_device_overrides';
    protected $_primary = array (
            'user_id', 
            'master_device_id' 
    );
    protected $_referenceMap = array (
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'id' 
            ), 
            'Users' => array (
                    'columns' => 'user_id', 
                    'refTableClass' => 'Application_Model_DbTable_User', 
                    'refColumns' => 'id' 
            ) 
    );
}