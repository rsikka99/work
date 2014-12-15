<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DealerSurveySettingDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class DealerSurveySettingDbTable extends Zend_Db_Table_Abstract
{
    protected $_primary = "dealerId";
    protected $_name    = "dealer_survey_settings";
}