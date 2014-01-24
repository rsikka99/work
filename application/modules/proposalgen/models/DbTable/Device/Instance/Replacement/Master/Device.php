<?php

/**
 * Class Proposalgen_Model_DbTable_Device_Instance_Replacement_Master_Device
 */
class Proposalgen_Model_DbTable_Device_Instance_Replacement_Master_Device extends Zend_Db_Table_Abstract
{
    protected $_name = 'device_instance_replacement_master_devices';
    protected $_primary = array('deviceInstanceId', 'hardwareOptimizationId');
}