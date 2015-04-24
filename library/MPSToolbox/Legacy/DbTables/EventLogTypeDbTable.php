<?php

namespace MPSToolbox\Legacy\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class EventLogTypeDbTable
 *
 * @package MPSToolbox\Legacy\DbTables
 */
class EventLogTypeDbTable extends Zend_Db_Table_Abstract
{
    protected $_primary = 'id';
    protected $_name    = 'event_log_types';
}