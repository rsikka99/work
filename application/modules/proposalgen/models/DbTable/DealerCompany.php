<?php

class Proposalgen_Model_DbTable_DealerCompany extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'dealer_company';
    protected $_primary = 'dealer_company_id';
    protected $_dependentTables = array (
            'Users', 
            'DealerOverride' 
    );
}

?>
