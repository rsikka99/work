<?php
/**
 * Class Proposalgen_Model_DbTable_Dealer_Master_Device_Attribute
 */
class Proposalgen_Model_DbTable_Dealer_Master_Device_Attribute extends Zend_Db_Table_Abstract
{
    protected $_name = 'dealer_master_device_attributes';
    protected $_primary = array(
        'masterDeviceId',
        'dealerId'
    );

}