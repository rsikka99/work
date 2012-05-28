<?php

class Proposalgen_Model_DbTable_DealerTonerOverride extends Zend_Db_Table_Abstract
{
    //put your code here
    protected $_name = 'dealer_toner_override';
    protected $_primary = array (
            'dealer_company_id', 
            'toner_id' 
    );
    protected $_referenceMap = array (
            'Toner' => array (
                    'columns' => 'toner_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Toner', 
                    'refColumns' => 'toner_id' 
            ), 
            'Company' => array (
                    'columns' => 'dealer_company_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_DealerCompany', 
                    'refColumns' => 'dealer_company_id' 
            ) 
    );
}

?>
