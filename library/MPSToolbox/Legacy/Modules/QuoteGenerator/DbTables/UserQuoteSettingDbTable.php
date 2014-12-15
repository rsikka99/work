<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserQuoteSettingDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class UserQuoteSettingDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'user_quote_settings';
    protected $_primary = array(
        'userId',
        'quoteSettingId'
    );
}