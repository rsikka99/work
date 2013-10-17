<?php
/**
 * Class Proposalgen_Model_DbTable_JitCompatibleMasterDevice
 */
class Proposalgen_Model_DbTable_JitCompatibleMasterDevice extends Zend_Db_Table_Abstract
{
    protected $_name = 'jit_compatible_master_devices';
    protected $_primary = array (
        'masterDeviceId',
        'dealerId'
    );
}