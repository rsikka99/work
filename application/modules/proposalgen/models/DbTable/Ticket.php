<?php

class Proposalgen_Model_DbTable_Ticket extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'pgen_tickets';
    protected $_primary = 'id';
    protected $_dependentTables = array (
            'users', 
            'ticket_categories', 
            'ticket_statuses' 
    );
}