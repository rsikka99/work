<?php

class Proposalgen_Model_DbTable_Privileges extends Zend_Db_Table_Abstract
{
    protected $_name = 'privileges';
    protected $_primary = 'priv_id';
    protected $_dependentTables = array (
            'UserPrivileges' 
    );
}
?>
