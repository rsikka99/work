<?php

class Quotegen_Model_DbTable_UserQuoteSettings extends Zend_Db_Table_Abstract
{
    protected $_name = 'quotegen_user_quote_settings';
    protected $_primary = array (
            'userId', 
            'quoteSettingId' 
    );  
}