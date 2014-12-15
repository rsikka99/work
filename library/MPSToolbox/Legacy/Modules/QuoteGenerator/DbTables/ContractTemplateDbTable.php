<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class ContractTemplateDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class ContractTemplateDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'contract_templates';
    protected $_primary = 'id';
}