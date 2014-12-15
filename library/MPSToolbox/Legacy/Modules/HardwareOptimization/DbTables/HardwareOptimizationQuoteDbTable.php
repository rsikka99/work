<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class HardwareOptimizationQuoteDbTable
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\DbTables
 */
class HardwareOptimizationQuoteDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'hardware_optimization_quotes';
    protected $_primary = 'quoteId';
}