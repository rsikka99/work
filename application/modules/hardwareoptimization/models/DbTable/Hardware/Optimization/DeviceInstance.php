<?php

class Hardwareoptimization_Model_DbTable_Hardware_Optimization_DeviceInstance extends Zend_Db_Table_Abstract
{
    protected $_name = 'hardware_optimization_device_instances';
    protected $_primary = array('deviceInstanceId', 'hardwareOptimizationId');
}