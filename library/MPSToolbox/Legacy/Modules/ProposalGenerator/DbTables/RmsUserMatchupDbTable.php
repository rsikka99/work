<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class RmsUserMatchupDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class RmsUserMatchupDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'rms_user_matchups';
    protected $_primary = ['rmsProviderId', 'rmsModelId', 'userId'];
}