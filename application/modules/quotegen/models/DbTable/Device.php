<?php

class Quotegen_Model_DbTable_Device extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_devices';
    protected $_primary = array (
            'masterDeviceId' 
    );
}