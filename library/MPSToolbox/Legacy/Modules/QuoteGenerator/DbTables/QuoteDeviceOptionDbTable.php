<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class QuoteDeviceOptionDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class QuoteDeviceOptionDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'quote_device_options';
    protected $_primary = [
        'id',
    ];
}