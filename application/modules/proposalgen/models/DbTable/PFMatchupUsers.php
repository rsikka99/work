<?php

class Proposalgen_Model_DbTable_PFMatchupUser extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_user_pf_device_matchups';
    protected $_primary = array (
            'user_id', 
            'devices_pf_id', 
            'master_device_id' 
    );
    protected $_referenceMap = array (
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'master_device_id' 
            ), 
            'PFDevices' => array (
                    'columns' => 'pf_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_PFDevices', 
                    'refColumns' => 'id' 
            ), 
            'Users' => array (
                    'columns' => 'user_id', 
                    'refTableClass' => 'Application_Model_DbTable_User', 
                    'refColumns' => 'id' 
            ) 
    );
}