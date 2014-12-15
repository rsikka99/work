<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class TonerVendorManufacturerDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class TonerVendorManufacturerDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'toner_vendor_manufacturers';
    protected $_primary = 'manufacturerId';
}