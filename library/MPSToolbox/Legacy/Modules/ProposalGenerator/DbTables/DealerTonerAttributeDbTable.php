<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class DealerTonerAttributeDbTable
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables
 */
class DealerTonerAttributeDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'dealer_toner_attributes';
    protected $_primary = array(
        'tonerId',
        'dealerId'
    );

}