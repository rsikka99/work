<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class RmsMasterMatchupDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class RmsMasterMatchupDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'rms_master_matchups';
    protected $_primary = ['rmsProviderId', 'rmsModelId'];
}