<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DeviceInstanceReplacementMasterDeviceDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class DeviceInstanceReplacementMasterDeviceDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'device_instance_replacement_master_devices';
    protected $_primary = ['deviceInstanceId', 'hardwareOptimizationId'];
}