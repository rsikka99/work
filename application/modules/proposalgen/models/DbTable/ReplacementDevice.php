<?php

/**
 * Class Proposalgen_Model_DbTable_ReplacementDevice
 */
class Proposalgen_Model_DbTable_ReplacementDevice extends Zend_Db_Table_Abstract
{
    protected $_name = 'replacement_devices';
    protected $_primary = array(
        'masterDeviceId',
        'dealerId'
    );
}