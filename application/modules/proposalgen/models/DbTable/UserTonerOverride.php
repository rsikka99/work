<?php

class Proposalgen_Model_DbTable_UserTonerOverride extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'user_toner_overrides';
    protected $_primary = array (
            'user_id', 
            'toner_id' 
    );
    protected $_referenceMap = array (
            'Toner' => array (
                    'columns' => 'toner_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Toner', 
                    'refColumns' => 'id' 
            ), 
            'Users' => array (
                    'columns' => 'user_id', 
                    'refTableClass' => 'Application_Model_DbTable_User', 
                    'refColumns' => 'id' 
            ) 
    );
}