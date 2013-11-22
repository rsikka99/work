<?php

/**
 * Class Admin_Model_DbTable_UserRole
 */
class Admin_Model_DbTable_UserRole extends Zend_Db_Table_Abstract
{
    protected $_name = 'user_roles';
    protected $_primary = array(
        'userId',
        'roleId',
    );
}
