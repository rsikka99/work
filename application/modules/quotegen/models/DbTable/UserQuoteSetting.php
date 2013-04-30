<?php

/**
 * Class Quotegen_Model_DbTable_UserQuoteSetting
 */
class Quotegen_Model_DbTable_UserQuoteSetting extends Zend_Db_Table_Abstract
{
    protected $_name = 'user_quote_settings';
    protected $_primary = array(
        'userId',
        'quoteSettingId'
    );
}