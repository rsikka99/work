<?php

class Proposalgen_Model_DbTable_DeviceInstance extends Zend_Db_Table_Abstract
{
    protected $_name = 'proposalgenerator_device_instances';
    protected $_primary = array (
            'id' 
    );
    protected $_dependentTables = array (
            'proposalgenerator_device_instance_meters' 
    );
    protected $_referenceMap = array (
            'UploadDataCollector' => array (
                    'columns' => 'upload_data_collector_row_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_UploadDataCollector', 
                    'refColumns' => 'id' 
            ), 
            'Reports' => array (
                    'columns' => 'report_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Reports', 
                    'refColumns' => 'id' 
            ), 
            'MasterDevice' => array (
                    'columns' => 'master_device_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_MasterDevice', 
                    'refColumns' => 'id' 
            ) 
    );
}