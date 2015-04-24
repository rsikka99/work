<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceSwapDbTable
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables
 */
class DeviceSwapDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = "device_swaps";
    protected $_primary = ["masterDeviceId", "dealerId"];
}
