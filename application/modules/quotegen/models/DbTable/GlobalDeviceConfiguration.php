<?php

class Quotegen_Model_DbTable_GlobalDeviceConfiguration extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_global_device_configurations';
    protected $_primary = array(
        'deviceConfigurationId'
    );
}