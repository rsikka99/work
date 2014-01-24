<?php

/**
 * Class Proposalgen_Model_DbTable_Rms_Master_Matchup
 */
class Proposalgen_Model_DbTable_Rms_Master_Matchup extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_master_matchups';
    protected $_primary = array('rmsProviderId', 'rmsModelId');
}