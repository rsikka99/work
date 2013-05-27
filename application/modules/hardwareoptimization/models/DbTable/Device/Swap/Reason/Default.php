<?php
/**
 * Class Hardwareoptimization_Model_DbTable_Device_Swap_Reason_Default
 */
class Hardwareoptimization_Model_DbTable_Device_Swap_Reason_Default extends Zend_Db_Table_Abstract
{
    protected $_name = "device_swap_reason_defaults";
    protected $_primary = array(
        "deviceSwapReasonCategoryId",
        "dealerId"
    );
}
