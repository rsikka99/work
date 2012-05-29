<?php

class Proposalgen_Model_DbTable_Users extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'users';
    protected $_primary = 'user_id';
    protected $_dependentTables = array (
            'UserPrivileges', 
            'UserReports', 
            'UserDeviceProperties', 
            'UserOverride', 
            'UserSessions', 
            'User_PasswordResetRequests' 
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