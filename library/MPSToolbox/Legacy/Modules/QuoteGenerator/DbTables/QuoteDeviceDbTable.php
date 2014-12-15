<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class QuoteDeviceDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class QuoteDeviceDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'quote_devices';
    protected $_primary = array(
        'id'
    );
}