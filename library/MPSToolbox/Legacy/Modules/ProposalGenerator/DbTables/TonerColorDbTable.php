<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class TonerColorDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class TonerColorDbTable extends Zend_Db_Table_Abstract
{
    protected $_name            = 'toner_colors';
    protected $_primary         = 'id';
    protected $_dependentTables = [
        'Toner',
    ];
}

