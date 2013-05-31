<?php
/**
 * Class Hardwareoptimization_Model_DbTable_Device_Instance_Device_Swap_Reason
 */
class Hardwareoptimization_Model_DbTable_Device_Instance_Device_Swap_Reason extends Zend_Db_Table_Abstract
{
    protected $_name = "device_instance_device_swap_reason";
    protected $_primary = array(
        "hardwareOptimizationId",
        "deviceInstanceId"
    );
}
