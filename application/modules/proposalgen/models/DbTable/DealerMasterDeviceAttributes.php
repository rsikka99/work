<?php
class Proposalgen_Model_DbTable_DealerMasterDeviceAttributes extends Zend_Db_Table_Abstract
{
    protected $_name = 'dealer_master_device_attributes';
    protected $_primary = array (
        'masterDeviceId',
        'dealerId'
    );
    protected $_referenceMap = array (
        'MasterDevice' => array (
            'columns' => 'masterDeviceId',
            'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice',
            'refColumns' => 'id'
        ),
        'Dealer' => array (
            'columns' => 'dealerId',
            'refTableClass' => 'Admin_Model_DbTable_Dealer',
            'refColumns' => 'id'
        )
    );
}