<?php

class Proposalgen_Model_DbTable_PFMasterMatchup extends Zend_Db_Table_Abstract
{
    protected $_name = 'master_matchup_pf';
    protected $_primary = array (
            'master_device_id', 
            'pf_device_id' 
    );
    protected $_referenceMap = array (
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'master_device_id' 
            ), 
            'PFDevices' => array (
                    'columns' => 'pf_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'pf_device_id' 
            ) 
    );
}

?>
