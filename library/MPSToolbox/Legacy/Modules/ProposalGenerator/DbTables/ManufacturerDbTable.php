<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class ManufacturerDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class ManufacturerDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'manufacturers';
    protected $_primary = 'id';
}