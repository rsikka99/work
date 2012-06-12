<?php

class Quotegen_Model_DbTable_UserDeviceConfigurations extends Zend_Db_Table_Abstract
{
    protected $_name = 'quotegen_user_device_configurations';
    protected $_primary = array (
            'deviceConfigurationId', 
            'optionId' 
    );  
}