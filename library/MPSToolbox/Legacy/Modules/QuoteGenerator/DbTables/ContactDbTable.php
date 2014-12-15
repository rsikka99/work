<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class ContactDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class ContactDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'contacts';
    protected $_primary = 'id';
}