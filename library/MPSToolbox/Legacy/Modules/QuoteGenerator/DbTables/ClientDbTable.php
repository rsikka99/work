<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class ClientDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class ClientDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'clients';
    protected $_primary = 'id';
}