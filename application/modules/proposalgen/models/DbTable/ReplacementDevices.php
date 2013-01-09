<?php

class Proposalgen_Model_DbTable_ReplacementDevices extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_replacement_devices';
    protected $_primary = 'master_device_id';
    protected $_dependentTables = array (
            'MasterDevice' 
    );
}