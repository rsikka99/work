<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceSwapReasonDbTable
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables
 */
class DeviceSwapReasonDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = "device_swap_reasons";
    protected $_primary = "id";
}
