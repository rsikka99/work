<?php

class Quotegen_Model_DbTable_DeviceConfigurationOptions extends Zend_Db_Table_Abstract
{
    protected $_name = 'quotegen_device_configuration_options';
    protected $_primary = array (
            'deviceConfigurationId', 
            'optionId' 
    );
}