<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class MasterDeviceDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class MasterDeviceDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'master_devices';
    protected $_primary = 'id';
}
