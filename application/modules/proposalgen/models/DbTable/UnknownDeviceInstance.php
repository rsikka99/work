<?php

class Proposalgen_Model_DbTable_UnknownDeviceInstance extends Zend_Db_Table_Abstract
{
    protected $_name = 'pgen_unknown_device_instances';
    protected $_primary = 'id';
    protected $_referenceMap = array (
            'UploadDataCollectorRow' => array (
                    'columns' => 'upload_data_collector_row_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_UploadDataCollectorRow', 
                    'refColumns' => 'id' 
            ), 
            'Reports' => array (
                    'columns' => 'report_id', 
                    'refTableClass' => 'Proposalgen_Model_DbTable_Report', 
                    'refColumns' => 'id' 
            ), 
            'Users' => array (
                    'columns' => 'user_id', 
                    'refTableClass' => 'Application_Model_DbTable_User', 
                    'refColumns' => 'id' 
            ) 
    );
}