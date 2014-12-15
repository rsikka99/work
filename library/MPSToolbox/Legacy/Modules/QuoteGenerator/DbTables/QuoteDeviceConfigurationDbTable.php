<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class QuoteDeviceConfigurationDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class QuoteDeviceConfigurationDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'quote_device_configurations';
    protected $_primary = array(
        'quoteDeviceId',
        'deviceId'
    );
}