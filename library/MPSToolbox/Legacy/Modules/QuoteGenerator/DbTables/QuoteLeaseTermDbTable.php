<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class QuoteLeaseTermDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class QuoteLeaseTermDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'quote_lease_terms';
    protected $_primary = 'quoteId';
}