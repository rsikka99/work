<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceInstanceMeterDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class DeviceInstanceMeterDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'device_instance_meters';
    protected $_primary = 'id';
}