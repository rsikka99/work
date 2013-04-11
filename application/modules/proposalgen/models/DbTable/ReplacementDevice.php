<?php

class Proposalgen_Model_DbTable_ReplacementDevice extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_replacement_devices';
    protected $_primary = array (
        'masterDeviceId',
        'dealerId'
    );
}