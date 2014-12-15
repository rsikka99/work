<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class MapDeviceInstanceDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class MapDeviceInstanceDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = "map_device_instances";
    protected $_primary = 'reportId';
}
