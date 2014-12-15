<?php

namespace MPSToolbox\Legacy\DbTables;

use Zend_Db_Table_Abstract;

/**
 * Class UserActivityDbTable
 *
 * @package MPSToolbox\Legacy\DbTables
 */
class UserActivityDbTable extends Zend_Db_Table_Abstract
{
    /**
     * @var string
     */
    protected $_name = 'user_activities';

    /**
     * @var array
     */
    protected $_primary = 'id';
}