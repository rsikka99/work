<?php

namespace MPSToolbox\Legacy\Modules\Preferences\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DealerSettingDbTable
 *
 * @package MPSToolbox\Legacy\Modules\Preferences\DbTables
 */
class DealerSettingDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = "dealer_settings";
    protected $_primary = "dealerId";
}