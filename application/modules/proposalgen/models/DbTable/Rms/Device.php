<?php
/**
 * Class Proposalgen_Model_DbTable_Rms_Device
 */
class Proposalgen_Model_DbTable_Rms_Device extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_devices';
    protected $_primary = array('rmsProviderId', 'rmsModelId');
}