<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class RmsDeviceDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class RmsDeviceDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'rms_devices';
    protected $_primary = array('rmsProviderId', 'rmsModelId');
}