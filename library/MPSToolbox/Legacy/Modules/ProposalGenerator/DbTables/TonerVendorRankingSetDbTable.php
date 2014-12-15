<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class TonerVendorRankingSetDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class TonerVendorRankingSetDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = "toner_vendor_ranking_sets";
    protected $_primary = "id";
}