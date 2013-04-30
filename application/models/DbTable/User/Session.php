<?php
/**
 * Class Application_Model_DbTable_User_Session
 */
class Application_Model_DbTable_User_Session extends Zend_Db_Table_Abstract
{
    protected $_name = 'user_sessions';
    protected $_primary = 'sessionId';
}