<?php

class Proposalgen_Model_DbTable_PFDevices extends Zend_Db_Table_Abstract
{
    protected $_name = 'devices_pf';
    protected $_primary = 'devices_pf_id';
    protected $_dependentTables = array (
            'PFMatchupUsers', 
            'PFSuggestedMatchup', 
            'PFModelRequest ' 
    );
}
?>
