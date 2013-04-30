<?php
/**
 * Class Proposalgen_Model_DbTable_Rms_User_Matchup
 */
class Proposalgen_Model_DbTable_Rms_User_Matchup extends Zend_Db_Table_Abstract
{
    protected $_name = 'rms_user_matchups';
    protected $_primary = array('rmsProviderId', 'rmsModelId', 'userId');
}