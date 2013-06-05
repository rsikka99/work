<?php
/**
 * Class Hardwareoptimization_Model_DbTable_Device_Swap
 */
class Hardwareoptimization_Model_DbTable_Device_Swap extends Zend_Db_Table_Abstract
{
    protected $_name = "device_swaps";
    protected $_primary = array(
        "masterDeviceId",
        "dealerId"
    );
}
