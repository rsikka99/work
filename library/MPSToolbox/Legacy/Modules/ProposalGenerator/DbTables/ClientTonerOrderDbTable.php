<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class ClientTonerOrderDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class ClientTonerOrderDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'client_toner_orders';
    protected $_primary = 'id';

}