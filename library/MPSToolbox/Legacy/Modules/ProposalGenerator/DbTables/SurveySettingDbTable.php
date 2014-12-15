<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class SurveySettingDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class SurveySettingDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'survey_settings';
    protected $_primary = 'id';
}