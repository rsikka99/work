<?php

/**
 * Class Application_Model_DbTable_User_Activity
 */
class Application_Model_DbTable_User_Activity extends Zend_Db_Table_Abstract
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