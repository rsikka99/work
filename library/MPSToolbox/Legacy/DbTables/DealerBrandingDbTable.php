<?php

namespace MPSToolbox\Legacy\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DealerBrandingDbTable
 *
 * @package MPSToolbox\Legacy\DbTables
 */
class DealerBrandingDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'dealer_branding';
    protected $_primary = 'dealerId';
}