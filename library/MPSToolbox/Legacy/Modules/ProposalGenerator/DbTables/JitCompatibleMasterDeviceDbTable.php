<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class JitCompatibleMasterDeviceDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class JitCompatibleMasterDeviceDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'jit_compatible_master_devices';
    protected $_primary = array(
        'masterDeviceId',
        'dealerId'
    );
}