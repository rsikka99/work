<?php

class Proposalgen_Model_DbTable_TicketCategory extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'proposalgenerator_ticket_categories';
    protected $_primary = 'id';
    protected $_dependentTables = array (
            'UnknownDeviceInstance', 
            'PFDevices' 
    );
}