<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceInstanceMasterDeviceDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class DeviceInstanceMasterDeviceDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'device_instance_master_devices';
    protected $_primary = 'deviceInstanceId';
}