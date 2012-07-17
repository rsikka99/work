<?php

class Quotegen_Model_DbTable_QuoteDeviceConfigurationOption extends Zend_Db_Table_Abstract
{
    protected $_name = 'qgen_quote_device_configuration_options';
    protected $_primary = array (
            'quoteDeviceOptionId', 
            'optionId' 
    );
}