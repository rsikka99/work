<?php

class Quotegen_Model_DbTable_DeviceOptions extends Zend_Db_Table_Abstract
{
    protected $_name = 'quotegen_device_options';
    protected $_primary = array (
            'masterDeviceId', 
            'optionId' 
    );  
}