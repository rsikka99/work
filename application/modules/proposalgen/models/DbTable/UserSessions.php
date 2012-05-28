<?php

class Proposalgen_Model_DbTable_UserSessions extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'user_sessions';
    protected $_primary = array (
            'user_id', 
            'session_id' 
    );
    protected $_referenceMap = array (
            'Users' => array (
                    'columns' => 'user_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Users', 
                    'refColumns' => 'user_id' 
            ) 
    );
}

?>
