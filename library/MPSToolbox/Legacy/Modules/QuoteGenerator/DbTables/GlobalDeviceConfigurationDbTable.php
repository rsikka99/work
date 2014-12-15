<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class GlobalDeviceConfigurationDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class GlobalDeviceConfigurationDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'global_device_configurations';
    protected $_primary = 'deviceConfigurationId';
}