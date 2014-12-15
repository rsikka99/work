<?php

namespace MPSToolbox\Legacy\Modules\Admin\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DealerRmsProviderDbTable
 *
 * @package MPSToolbox\Legacy\Modules\Admin\DbTables
 */
class DealerRmsProviderDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'dealer_rms_providers';
    protected $_primary = array(
        'dealerId',
        'rmsProviderId',
    );
}