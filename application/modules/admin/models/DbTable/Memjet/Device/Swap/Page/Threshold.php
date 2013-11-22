<?php
/**
 * Class Admin_Model_DbTable_Memjet_Device_Swap_Page_Threshold
 */
class Admin_Model_DbTable_Memjet_Device_Swap_Page_Threshold extends Zend_Db_Table_Abstract
{
    protected $_name = 'memjet_device_swaps_page_thresholds';
    protected $_primary = array(
        'masterDeviceId',
        'dealerId'
    );

}