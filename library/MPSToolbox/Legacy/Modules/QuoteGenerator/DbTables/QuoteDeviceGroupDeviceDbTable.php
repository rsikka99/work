<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class QuoteDeviceGroupDeviceDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class QuoteDeviceGroupDeviceDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'quote_device_group_devices';
    protected $_primary = [
        'quoteDeviceId',
        'quoteDeviceGroupId',
    ];
}