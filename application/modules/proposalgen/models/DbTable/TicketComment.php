<?php

class Proposalgen_Model_DbTable_TicketComment extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'pgen_ticket_comments';
    protected $_primary = 'id';
    protected $_dependentTables = array (
            'tickets', 
            'users' 
    );
}