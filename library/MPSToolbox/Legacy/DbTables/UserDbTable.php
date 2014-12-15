<?php

namespace MPSToolbox\Legacy\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserDbTable
 *
 * @package MPSToolbox\Legacy\DbTables
 */
class UserDbTable extends Zend_Db_Table_Abstract
{
    protected $_name    = 'users';
    protected $_primary = 'id';
}
