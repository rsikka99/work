<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class TemplateDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class TemplateDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'template';
    protected $_primary = 'id';
}