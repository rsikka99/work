<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class OptionCategoryDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class OptionCategoryDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'option_categories';
    protected $_primary = [
        'categoryId',
        'optionId',
    ];
}