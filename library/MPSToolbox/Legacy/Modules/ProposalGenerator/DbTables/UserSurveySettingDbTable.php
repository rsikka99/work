<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserSurveySettingDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class UserSurveySettingDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'user_survey_settings';
    protected $_primary = 'userId';
}