<?php

class Proposalgen_Model_DbTable_UnknownDeviceInstance extends Zend_Db_Table_Abstract
{
    protected $_name = 'unknown_device_instance';
    protected $_primary = array (
            'unknown_device_instance_id' 
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
            'Users' => array (
                    'columns' => 'user_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Users', 
                    'refColumns' => 'user_id' 
            ) 
    );
}

?>
