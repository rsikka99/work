<?php

class Quotegen_Model_DbTable_DeviceOption extends Zend_Db_Table_Abstract
{
    protected $_name = 'device_options';
    protected $_primary = array(
        'masterDeviceId',
        'optionId'
    );
}