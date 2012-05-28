<?php

class Proposalgen_Model_DbTable_MasterDevice extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'master_device';
    protected $_primary = 'master_device_id';
    protected $_dependentTables = array (
            'PartsDevice', 
            'DeviceInstance', 
            'UserDeviceProperties', 
            'UserOverride', 
            'DealerOverride', 
            'PFSuggestedMatchup' 
    );
}

?>
