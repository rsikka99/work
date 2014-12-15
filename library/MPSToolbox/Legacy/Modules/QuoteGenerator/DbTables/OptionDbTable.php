<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class OptionDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class OptionDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'options';
    protected $_primary = 'id';
}