<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class LeasingSchemaRateDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class LeasingSchemaRateDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'leasing_schema_rates';
    protected $_primary = array(
        'leasingSchemaTermId',
        'leasingSchemaRangeId'
    );
}