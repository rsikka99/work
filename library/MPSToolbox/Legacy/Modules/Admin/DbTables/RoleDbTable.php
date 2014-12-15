<?php

namespace MPSToolbox\Legacy\Modules\Admin\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class RoleDbTable
 *
 * @package MPSToolbox\Legacy\Modules\Admin\DbTables
 */
class RoleDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'roles';
    protected $_primary = 'id';
}
