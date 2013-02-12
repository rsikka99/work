<?php

class Quotegen_Model_DbTable_UserQuoteSetting extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_user_quote_settings';
    protected $_primary = array(
        'userId',
        'quoteSettingId'
    );
}