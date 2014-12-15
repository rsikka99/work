<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceTonerDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class DeviceTonerDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'device_toners';
    protected $_primary = array(
        'toner_id',
        'master_device_id'
    );
}