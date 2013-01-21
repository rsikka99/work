<?php
class Proposalgen_Model_DbTable_Rms_Device extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_rms_devices';
    protected $_primary = array('rmsProviderId', 'rmsModelId');
}