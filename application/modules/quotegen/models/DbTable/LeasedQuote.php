<?php

/**
 * Class Quotegen_Model_DbTable_LeasedQuote
 */
class Quotegen_Model_DbTable_LeasedQuote extends Zend_Db_Table_Abstract
{
    protected $_name = 'leased_quotes';
    protected $_primary = 'quoteId';
}