<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class LeasingSchemaRangeDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class LeasingSchemaRangeDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'leasing_schema_ranges';
    protected $_primary = 'id';
}