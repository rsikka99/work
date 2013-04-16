<?php
class Preferences_Model_DbTable_User_Setting extends Zend_Db_Table_Abstract
{
    protected $_name = "user_settings";
    protected $_primary = "userId";
}