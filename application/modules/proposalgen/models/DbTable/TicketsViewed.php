<?php

class Proposalgen_Model_DbTable_TicketsViewed extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'pgen_tickets_viewed';
    protected $_primary = array (
            'ticket_id', 
            'user_id' 
    );
}