<?php

namespace MPSToolbox\Legacy\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserSessionDbTable
 *
 * @package MPSToolbox\Legacy\DbTables
 */
class UserSessionDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'user_sessions';
    protected $_primary = 'sessionId';
}