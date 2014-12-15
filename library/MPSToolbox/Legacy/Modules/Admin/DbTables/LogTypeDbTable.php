<?php

namespace MPSToolbox\Legacy\Modules\Admin\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class LogTypeDbTable
 *
 * @package MPSToolbox\Legacy\Modules\Admin\DbTables
 */
class LogTypeDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'log_types';
    protected $_primary = 'id';
}
