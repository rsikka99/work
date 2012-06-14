<?php

class Quotegen_Model_DbTable_Devices extends Zend_Db_Table_Abstract
{
    protected $_name = 'quotegen_devices';
    protected $_primary = array (
            'masterDeviceId' 
    );
}