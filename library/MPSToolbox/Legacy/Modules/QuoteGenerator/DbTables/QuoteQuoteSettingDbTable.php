<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class QuoteQuoteSettingDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class QuoteQuoteSettingDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'quote_quote_settings';
    protected $_primary = [
        'quoteId',
        'quoteSettingId',
    ];
}