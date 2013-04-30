<?php

/**
 * Class Quotegen_Model_DbTable_QuoteDeviceConfiguration
 */
class Quotegen_Model_DbTable_QuoteDeviceConfiguration extends Zend_Db_Table_Abstract
{
    protected $_name = 'quote_device_configurations';
    protected $_primary = array(
        'quoteDeviceId',
        'deviceId'
    );
}