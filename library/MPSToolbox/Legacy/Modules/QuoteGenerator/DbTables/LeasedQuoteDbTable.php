<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class LeasedQuoteDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class LeasedQuoteDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'leased_quotes';
    protected $_primary = 'quoteId';
}