<?php

class Proposalgen_Model_DbTable_PFMasterMatchup extends Zend_Db_Table_Abstract
{
    protected $_name = 'proposalgenerator_master_pf_device_matchups';
    protected $_primary = array (
            'master_device_id', 
            'pf_device_id' 
    );
    protected $_referenceMap = array (
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'id' 
            ), 
            'PFDevices' => array (
                    'columns' => 'pf_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_PFDevices', 
                    'refColumns' => 'id' 
            ) 
    );
}