<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class ContractTemplateSectionDbTable
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\DbTables
 */
class ContractTemplateSectionDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'contract_template_sections';
    protected $_primary = array(
        'contractTemplateId',
        'contractSectionId'
    );
}