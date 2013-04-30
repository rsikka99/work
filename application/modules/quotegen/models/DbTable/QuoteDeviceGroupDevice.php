<?php

/**
 * Class Quotegen_Model_DbTable_QuoteDeviceGroupDevice
 */
class Quotegen_Model_DbTable_QuoteDeviceGroupDevice extends Zend_Db_Table_Abstract
{
    protected $_name = 'quote_device_group_devices';
    protected $_primary = array(
        'quoteDeviceId',
        'quoteDeviceGroupId'
    );
}