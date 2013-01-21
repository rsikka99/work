<?php
class Proposalgen_Model_DbTable_Rms_Master_Matchup extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_rms_master_matchups';
    protected $_primary = array('rmsProviderId', 'rmsModelId', 'masterDeviceId');
}