<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceSwapReasonCategoryDbTable
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables
 */
class DeviceSwapReasonCategoryDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = "device_swap_reason_categories";
    protected $_primary = "id";
}
