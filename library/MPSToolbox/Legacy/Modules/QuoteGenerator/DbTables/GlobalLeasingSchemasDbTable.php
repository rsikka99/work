<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class GlobalLeasingSchemasDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class GlobalLeasingSchemasDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'global_leasing_schemas';
    protected $_primary = 'leasingSchemaId';
}