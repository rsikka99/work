<?php

/**
 * Class Quotegen_Model_DbTable_DealerQuoteSetting
 */
class Quotegen_Model_DbTable_DealerQuoteSetting extends Zend_Db_Table_Abstract
{
    protected $_primary = 'dealerId';
    protected $_name = 'dealer_quote_settings';
}