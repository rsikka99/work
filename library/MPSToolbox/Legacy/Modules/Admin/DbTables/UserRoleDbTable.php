<?php

namespace MPSToolbox\Legacy\Modules\Admin\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserRoleDbTable
 *
 * @package MPSToolbox\Legacy\Modules\Admin\DbTables
 */
class UserRoleDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'user_roles';
    protected $_primary = [
        'userId',
        'roleId',
    ];
}
