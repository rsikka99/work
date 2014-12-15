<?php

namespace MPSToolbox\Legacy\Modules\Admin\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DealerTonerVendorDbTable
 *
 * @package MPSToolbox\Legacy\Modules\Admin\DbTables
 */
class DealerTonerVendorDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'dealer_toner_vendors';
    protected $_primary = array(
        'dealerId',
        'manufacturerId',
    );
}