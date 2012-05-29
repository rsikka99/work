<?php

class Proposalgen_Model_DbTable_UserPrivileges extends Zend_Db_Table_Abstract
{
    protected $_name = 'user_privileges';
    protected $_primary = array (
            'user_id', 
            'priv_id' 
    );
    
    protected $_referenceMap = array (
            'ACL_Users' => array (
                    'columns' => 'user_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Users', 
                    'refColumns' => 'user_id' 
            ), 
            'ACL_Privilege' => array (
                    'columns' => 'priv_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Privileges', 
                    'refColumns' => 'priv_id' 
            ) 
    );
}
?>
