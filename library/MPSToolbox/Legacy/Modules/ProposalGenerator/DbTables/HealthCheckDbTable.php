<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class HealthCheckDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class HealthCheckDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'healthchecks';
    protected $_primary = 'id';
}