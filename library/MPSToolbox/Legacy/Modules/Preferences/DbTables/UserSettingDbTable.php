<?php

namespace MPSToolbox\Legacy\Modules\Preferences\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserSettingDbTable
 *
 * @package MPSToolbox\Legacy\Modules\Preferences\DbTables
 */
class UserSettingDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = "user_settings";
    protected $_primary = "userId";
}