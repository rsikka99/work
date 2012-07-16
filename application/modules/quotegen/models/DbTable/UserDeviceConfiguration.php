<?php

class Quotegen_Model_DbTable_UserDeviceConfiguration extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_user_device_configurations';
    protected $_primary = array (
            'deviceConfigurationId', 
            'userId' 
    );
}