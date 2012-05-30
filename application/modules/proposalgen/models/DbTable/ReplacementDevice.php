<?php

class Proposalgen_Model_DbTable_ReplacementDevice extends Zend_Db_Table_Abstract
{
    protected $_name = 'proposalgenerator_replacement_devices';
    protected $_primary = 'master_device_id';
    protected $_dependentTables = array (
            'MasterDevice' 
    );
}