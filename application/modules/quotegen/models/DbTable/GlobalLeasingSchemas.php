<?php

class Quotegen_Model_DbTable_GlobalLeasingSchemas extends Zend_Db_Table_Abstract
{
    protected $_name = 'quotegen_global_leasing_schemas';
    protected $_primary = array (
            'masterDeviceId', 
            'optionId' 
    );  
}