<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class AddressDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class AddressDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'addresses';
    protected $_primary = 'id';
}