<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class QuoteDeviceGroupDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class QuoteDeviceGroupDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'quote_device_groups';
    protected $_primary = array(
        'id'
    );
}