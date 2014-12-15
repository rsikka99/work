<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserReportSettingDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class UserReportSettingDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'user_report_settings';
    protected $_primary = 'userId';
}