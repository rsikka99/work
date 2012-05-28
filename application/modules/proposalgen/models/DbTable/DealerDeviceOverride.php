<?php

class Proposalgen_Model_DbTable_DealerDeviceOverride extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'dealer_device_override';
    protected $_primary = array (
            'dealer_company_id', 
            'master_device_id' 
    );
    protected $_referenceMap = array (
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'master_device_id' 
            ), 
            'DealerCompany' => array (
                    'columns' => 'dealer_company_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_DealerCompany', 
                    'refColumns' => 'dealer_company_id' 
            ) 
    );
}

?>
