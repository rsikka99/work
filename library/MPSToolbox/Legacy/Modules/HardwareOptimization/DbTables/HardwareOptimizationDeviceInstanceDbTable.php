<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class HardwareOptimizationDeviceInstanceDbTable
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables
 */
class HardwareOptimizationDeviceInstanceDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'hardware_optimization_device_instances';
    protected $_primary = ['deviceInstanceId', 'hardwareOptimizationId'];
}