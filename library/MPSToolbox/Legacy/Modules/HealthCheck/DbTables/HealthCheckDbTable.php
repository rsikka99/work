<?php

namespace MPSToolbox\Legacy\Modules\HealthCheck\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\HealthCheckDbTable
 *
 * @package MPSToolbox\Legacy\Modules\HealthCheck\DbTables
 */
class HealthCheckDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'healthchecks';
    protected $_primary = 'id';
}