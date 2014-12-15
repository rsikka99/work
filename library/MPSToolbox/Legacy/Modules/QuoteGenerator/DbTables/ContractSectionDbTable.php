<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class ContractSectionDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class ContractSectionDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'contract_sections';
    protected $_primary = 'id';
}