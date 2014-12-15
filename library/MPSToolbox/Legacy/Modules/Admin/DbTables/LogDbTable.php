<?php

namespace MPSToolbox\Legacy\Modules\Admin\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class LogDbTable
 *
 * @package MPSToolbox\Legacy\Modules\Admin\DbTables
 */
class LogDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'logs';
    protected $_primary = 'id';
}
