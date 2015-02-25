<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class QuoteDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class QuoteDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'quotes';
    protected $_primary = [
        'id',
    ];
}