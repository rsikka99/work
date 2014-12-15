<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceInstanceDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class DeviceInstanceDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'device_instances';
    protected $_primary = 'id';
}