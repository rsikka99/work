<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class TonerDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class TonerDbTable extends Zend_Db_Table_Abstract
{
    protected $_name         = 'toners';
    protected $_primary      = 'id';
    protected $_referenceMap = array(
        'TonerColor'   => array(
            'columns'       => 'toner_color_id',
            'refTableClass' => 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\TonerColorDbTable',
            'refColumns'    => 'id'
        ),
        'MasterDeviceDataAdapter' => array(
            'columns'       => 'master_device_id',
            'refTableClass' => 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\MasterDeviceDbTable',
            'refColumns'    => 'id'
        )
    );
}