<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserViewedClientDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class UserViewedClientDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'user_viewed_clients';
    protected $_primary = [
        'userId',
        'clientId',
    ];
}