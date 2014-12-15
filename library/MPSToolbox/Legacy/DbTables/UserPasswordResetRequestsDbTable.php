<?php

namespace MPSToolbox\Legacy\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserPasswordResetRequestsDbTable
 *
 * @package MPSToolbox\Legacy\DbTables
 */
class UserPasswordResetRequestsDbTable extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name    = 'user_password_reset_requests';
    protected $_primary = 'id';

}