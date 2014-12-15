<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class TonerConfigDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class TonerConfigDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'toner_configs';
    protected $_primary = 'id';
}