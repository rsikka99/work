<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DealerQuoteSettingDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class DealerQuoteSettingDbTable extends Zend_Db_Table_Abstract
{
    protected $_primary = 'dealerId';
    protected $_name    = 'dealer_quote_settings';
}