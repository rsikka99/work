<?php

class Proposalgen_Model_DbTable_TicketPFRequests extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'ticket_pf_requests';
    protected $_primary = 'ticket_id';
    protected $_dependentTables = array (
            'devices_pf' 
    );
}

?>