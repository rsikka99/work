<?php

namespace MPSToolbox\Legacy\Modules\Assessment\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class AssessmentDbTable
 *
 * @package MPSToolbox\Legacy\Modules\Assessment\DbTables
 */
class AssessmentDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'assessments';
    protected $_primary = 'id';
}