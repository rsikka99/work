<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class HardwareOptimizationDbTable
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables
 */
class HardwareOptimizationDbTable extends Zend_Db_Table_Abstract
{
    protected $_primary = 'id';
    protected $_name    = 'hardware_optimizations';
}
