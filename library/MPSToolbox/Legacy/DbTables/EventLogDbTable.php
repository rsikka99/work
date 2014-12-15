<?php

namespace MPSToolbox\Legacy\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class EventLogDbTable
 *
 * @package MPSToolbox\Legacy\DbTables
 */
class EventLogDbTable extends Zend_Db_Table_Abstract
{
    protected $_primary = "id";
    protected $_name    = "event_logs";
}