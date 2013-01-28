<?php
/**
 * Created by JetBrains PhpStorm.
 * User: swilder
 * Date: 28/01/13
 * Time: 9:22 AM
 * To change this template use File | Settings | File Templates.
 */

class Proposalgen_Model_DbTable_Device_Instance_Replacement_Master_Device extends
        Zend_Db_Table_Abstract
{
    protected $_name = 'device_instance_replacement_master_devices';
    protected $_primary = 'deviceInstanceId';
}