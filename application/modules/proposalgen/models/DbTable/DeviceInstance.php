<?php

class Proposalgen_Model_DbTable_DeviceInstance extends Zend_Db_Table_Abstract
{
    protected $_name = 'device_instance';
    protected $_primary = array (
            'device_instance_id' 
    );
    protected $_dependentTables = array (
            'device_meter_instance' 
    );
    protected $_referenceMap = array (
            'Upload_Data_Collector' => array (
                    'columns' => 'upload_data_collector_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_UploadDataCollector', 
                    'refColumns' => 'upload_data_collector_id' 
            ), 
            'Reports' => array (
                    'columns' => 'report_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Reports', 
                    'refColumns' => 'report_id' 
            ), 
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'master_device_id' 
            ) 
    );
}

?>
