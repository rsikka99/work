<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceSwapReasonDefaultDbTable
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables
 */
class DeviceSwapReasonDefaultDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = "device_swap_reason_defaults";
    protected $_primary = array(
        "deviceSwapReasonCategoryId",
        "dealerId"
    );
}
