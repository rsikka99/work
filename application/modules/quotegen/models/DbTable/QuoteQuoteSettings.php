<?php

class Quotegen_Model_DbTable_QuoteQuoteSettings extends Zend_Db_Table_Abstract
{
    protected $_name = 'quotegen_quote_quote_settings';
    protected $_primary = array (
            'quoteId', 
            'quoteSettingId' 
    );  
}