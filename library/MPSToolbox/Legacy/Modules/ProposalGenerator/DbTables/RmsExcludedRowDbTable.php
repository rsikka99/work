<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class RmsExcludedRowDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class RmsExcludedRowDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'rms_excluded_rows';
    protected $_primary = 'id';
}