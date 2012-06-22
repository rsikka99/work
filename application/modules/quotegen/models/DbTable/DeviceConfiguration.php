<?php

class Quotegen_Model_DbTable_DeviceConfiguration extends Zend_Db_Table_Abstract
{
    protected $_name = 'quotegen_device_configurations';
    protected $_primary = 'masterDeviceId';
}