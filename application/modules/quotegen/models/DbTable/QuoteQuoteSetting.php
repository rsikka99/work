<?php

class Quotegen_Model_DbTable_QuoteQuoteSetting extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_quote_quote_settings';
    protected $_primary = array(
        'quoteId',
        'quoteSettingId'
    );
}