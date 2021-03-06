<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class RmsProviderDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class RmsProviderDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'rms_providers';
    protected $_primary = 'id';
}