<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DealerReportSettingDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class DealerReportSettingDbTable extends Zend_Db_Table_Abstract
{
    protected $_primary = "dealerId";
    protected $_name    = "dealer_report_settings";
}