<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceOptionDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class DeviceOptionDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'device_options';
    protected $_primary = [
        'masterDeviceId',
        'optionId',
    ];
}