<?php

/**
 * Class Proposalgen_Model_DbTable_DeviceToner
 */
class Proposalgen_Model_DbTable_DeviceToner extends Zend_Db_Table_Abstract
{
    protected $_name = 'device_toners';
    protected $_primary = array(
        'toner_id',
        'master_device_id'
    );
}