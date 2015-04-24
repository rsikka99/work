<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceConfigurationOptionDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class DeviceConfigurationOptionDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'device_configuration_options';
    protected $_primary = [
        'deviceConfigurationId',
        'optionId',
    ];
}