<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class LeasingSchemaTermDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class LeasingSchemaTermDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'leasing_schema_terms';
    protected $_primary = 'id';
}