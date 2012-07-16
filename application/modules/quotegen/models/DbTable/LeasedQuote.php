<?php

class Quotegen_Model_DbTable_LeasedQuote extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_leased_quotes';
    protected $_primary = 'quoteId';
}