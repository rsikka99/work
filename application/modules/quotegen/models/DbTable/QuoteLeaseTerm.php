<?php

class Quotegen_Model_DbTable_QuoteLeaseTerm extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_quote_lease_terms';
    protected $_primary = array (
            'quoteId', 
            'leaseTermId' 
    );
}