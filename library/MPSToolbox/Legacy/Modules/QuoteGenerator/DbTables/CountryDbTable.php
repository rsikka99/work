<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class CountryDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class CountryDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'countries';
    protected $_primary = 'country_id';
}