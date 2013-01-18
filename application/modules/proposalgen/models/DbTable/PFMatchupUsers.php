<?php

class Proposalgen_Model_DbTable_PFMatchupUsers extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_user_pf_device_matchups';
    protected $_primary = array (
            'user_id', 
            'devices_pf_id', 
            'master_device_id' 
    );
}