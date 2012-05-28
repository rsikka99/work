<?php

class Proposalgen_Model_DbTable_Tickets extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'tickets';
    protected $_primary = 'ticket_id';
    protected $_dependentTables = array (
            'users', 
            'ticket_categories', 
            'ticket_statuses' 
    );
}

?>