<?php

class Quotegen_Model_DbTable_DeviceConfigurationOption extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_device_configuration_options';
    protected $_primary = array (
            'deviceConfigurationId', 
            'optionId' 
    );
}