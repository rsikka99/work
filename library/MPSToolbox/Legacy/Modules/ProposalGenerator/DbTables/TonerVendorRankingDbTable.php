<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class TonerVendorRankingDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class TonerVendorRankingDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = "toner_vendor_rankings";
    protected $_primary = ["tonerVendorRankingSetId", "manufacturerId"];
}