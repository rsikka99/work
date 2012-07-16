<?php

class Proposalgen_Model_DbTable_PFDevice extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_pf_devices';
    protected $_primary = 'id';
    protected $_dependentTables = array (
            'PFMatchupUsers', 
            'PFSuggestedMatchup', 
            'PFModelRequest ' 
    );
}