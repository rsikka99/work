<?php

/**
 * Class Quotegen_Model_DbTable_DeviceConfigurationOption
 */
class Quotegen_Model_DbTable_DeviceConfigurationOption extends Zend_Db_Table_Abstract
{
    protected $_name = 'device_configuration_options';
    protected $_primary = array(
        'deviceConfigurationId',
        'optionId'
    );
}