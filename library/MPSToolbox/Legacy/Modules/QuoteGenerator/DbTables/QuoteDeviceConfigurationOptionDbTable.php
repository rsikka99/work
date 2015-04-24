<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class QuoteDeviceConfigurationOptionDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class QuoteDeviceConfigurationOptionDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'quote_device_configuration_options';
    protected $_primary = [
        'quoteDeviceOptionId',
        'optionId',
    ];
}