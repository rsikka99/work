<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DealerMasterDeviceAttributeDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class DealerMasterDeviceAttributeDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'dealer_master_device_attributes';
    protected $_primary = array(
        'masterDeviceId',
        'dealerId'
    );

}