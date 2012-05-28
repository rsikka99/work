<?php

class Proposalgen_Model_DbTable_TicketComments extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'ticket_comments';
    protected $_primary = 'comment_id';
    protected $_dependentTables = array (
            'tickets', 
            'users' 
    );
}

?>