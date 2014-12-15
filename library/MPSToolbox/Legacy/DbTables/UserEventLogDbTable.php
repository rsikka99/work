<?php

namespace MPSToolbox\Legacy\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserEventLogDbTable
 *
 * @package MPSToolbox\Legacy\DbTables
 */
class UserEventLogDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'user_event_logs';
    protected $_primary = 'eventLogId';

}