<?php

class Quotegen_Model_DbTable_QuoteDevicePage extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_quote_device_pages';
    protected $_primary = array (
            'quoteDeviceId' 
    );
}