<?php
class Proposalgen_Model_DbTable_Rms_User_Matchup extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_rms_user_matchups';
    protected $_primary = array('rmsProviderId', 'rmsModelId', 'masterDeviceId', 'userId');
}