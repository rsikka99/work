<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class LeasingSchemaDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class LeasingSchemaDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'leasing_schemas';
    protected $_primary = [
        'id',
    ];
}