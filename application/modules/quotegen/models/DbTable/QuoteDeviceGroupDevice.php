<?php

class Quotegen_Model_DbTable_QuoteDeviceGroupDevice extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_quote_device_group_devices';
    protected $_primary = array(
        'quoteDeviceId',
        'quoteDeviceGroupId'
    );
}