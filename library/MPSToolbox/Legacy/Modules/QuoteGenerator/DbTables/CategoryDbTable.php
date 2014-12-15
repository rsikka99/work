<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class CategoryDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class CategoryDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'categories';
    protected $_primary = array(
        'id'
    );
}