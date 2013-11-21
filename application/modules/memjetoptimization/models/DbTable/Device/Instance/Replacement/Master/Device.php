<?php
/**
 * Class Memjetoptimization_Model_DbTable_Device_Instance_Replacement_Master_Device
 */
class Memjetoptimization_Model_DbTable_Device_Instance_Replacement_Master_Device extends Zend_Db_Table_Abstract
{
    protected $_name = 'memjet_device_instance_replacement_master_devices';
    protected $_primary = array('deviceInstanceId', 'memjetOptimizationId');
}