<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceInstanceDeviceSwapReasonDbTable
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables
 */
class DeviceInstanceDeviceSwapReasonDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = "device_instance_device_swap_reasons";
    protected $_primary = array(
        "hardwareOptimizationId",
        "deviceInstanceId"
    );
}
