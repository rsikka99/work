<?php

namespace MPSToolbox\Legacy\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DealerDbTable
 *
 * @package MPSToolbox\Legacy\DbTables
 */
class DealerDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'dealers';
    protected $_primary = 'id';
}