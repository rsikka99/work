<?php

/**
 * Class Quotegen_Model_DbTable_QuoteQuoteSetting
 */
class Quotegen_Model_DbTable_QuoteQuoteSetting extends Zend_Db_Table_Abstract
{
    protected $_name = 'quote_quote_settings';
    protected $_primary = array(
        'quoteId',
        'quoteSettingId'
    );
}