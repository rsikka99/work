<?php

class Proposalgen_Model_DbTable_TicketCategories extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'ticket_categories';
    protected $_primary = 'category_id';
    protected $_dependentTables = array (
            'unknown_device_instance', 
            'devices_pf' 
    );
}

?>